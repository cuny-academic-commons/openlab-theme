<?php

class Openlab_Clone_Course_Site {
	public $group_id;
	public $site_id;

	public $source_group_id;
	public $source_site_id;
	public $destination_path;

	public $source_group_admins = array();

	public function __construct( $group_id, $source_group_id, $source_site_id, $destination_path ) {
		$this->group_id         = $group_id;
		$this->source_group_id  = $source_group_id;
		$this->source_site_id   = $source_site_id;
		$this->destination_path = $destination_path;
	}

	/**
	 * Summary:
	 *
	 * 1) Create new empty blog with necessary details
	 * 2) Copy settings from old blog, using blacklist
	 * 3) Copy admin-authored posts from old blog
	 */
	public function go() {
		$this->create_site();

		if ( ! empty( $this->site_id ) ) {
			$this->migrate_site_settings();
			$this->migrate_posts();
		}
	}

	protected function create_site() {
		global $wpdb;

		$group = groups_get_group( $this->group_id );
		$title = $group->name;

		$clone_destination_path = groups_get_groupmeta( $this->group_id, 'clone_destination_path' );
		$validated              = wpmu_validate_blog_signup( $clone_destination_path, $title );

		// Assemble args and create the new site
		$domain = $validated['domain'];
		$path   = $validated['path'];

		$user_id = $group->creator_id;

		$meta = array(
			'public' => 1,
		);

		// We take care of this ourselves later on
		remove_action( 'wpmu_new_blog', 'st_wpmu_new_blog', 10, 6 );

		$site_id = wpmu_create_blog(
			$domain,
			$path,
			$title,
			$user_id,
			$meta
		);

		if ( ! is_wp_error( $site_id ) ) {
			$this->site_id = $site_id;

			// Associate site with the group in groupmeta
			cboxol_set_group_site_id( $this->group_id, $this->site_id );
		}
	}

	/**
	 * Taken from site-template
	 */
	protected function migrate_site_settings() {
		global $wpdb;

		$upload_dir = wp_upload_dir();

		$source_site_upload_dir = $upload_dir['basedir'];
		$dest_site_upload_dir   = str_replace( $this->source_site_id, $this->site_id, $source_site_upload_dir );
		$source_site_url        = get_blog_option( $this->source_site_id, 'home' );
		$dest_site_url          = get_blog_option( $this->site_id, 'home' );

		switch_to_blog( $this->source_site_id );

		// get all old options
		$all_options = wp_load_alloptions();
		$options     = array();
		foreach ( array_keys( $all_options ) as $key ) {
			$option_value    = get_option( $key ); // have to do this to deal with arrays
			$options[ $key ] = $option_value;
		}

		// theme mods -- don't show up in all_options.
		// Only add options for the current theme
		$theme = get_option( 'current_theme' );
		$mods  = get_option( 'mods_' . $theme );
		$mods  = map_deep(
			$mods,
			function( $v ) use ( $source_site_url, $source_site_upload_dir, $dest_site_url, $dest_site_upload_dir ) {
				return str_replace(
					array( $source_site_url, $source_site_upload_dir ),
					array( $dest_site_url, $dest_site_upload_dir ),
					$v
				);
			}
		);

		$preserve_option = array(
			'siteurl',
			'blogname',
			'admin_email',
			'new_admin_email',
			'home',
			'upload_path',
			'db_version',
			'blog_public',
			$wpdb->get_blog_prefix( $this->site_id ) . 'user_roles',
			'fileupload_url',
		);

		// now write them all back
		switch_to_blog( $this->site_id );
		foreach ( $options as $key => $value ) {
			if ( ! in_array( $key, $preserve_option, true ) ) {
				$value = map_deep(
					$value,
					function( $v ) use ( $source_site_url, $source_site_upload_dir, $dest_site_url, $dest_site_upload_dir ) {
						return str_replace(
							array( $source_site_url, $source_site_upload_dir ),
							array( $dest_site_url, $dest_site_upload_dir ),
							$v
						);
					}
				);

				update_option( $key, $value );
			}
		}

		// add the theme mods
		update_option( 'mods_' . $theme, $mods );

		// Just in case
		create_initial_taxonomies();
		flush_rewrite_rules();

		// Only add the Credits widget if there are non-self ancestors.
		$group = groups_get_group( $this->group_id );
		if ( openlab_get_group_clone_history_data( $group->id, $group->creator_id ) ) {
			openlab_add_widget_to_main_sidebar( 'openlab_clone_credits_widget' );
		}

		$enable_sharing = groups_get_groupmeta( $group->id, 'enable_sharing', true );
		if ( $enable_sharing ) {
			openlab_add_widget_to_main_sidebar( 'openlab_shareable_content_widget' );
		}

		restore_current_blog();

		// This has to be re-run, because the first time happens before site cloning is done.
		openlab_save_group_site_settings();
	}

	/**
	 * The strategy is to copy all posts, postmeta, and taxonomy. Then I'll
	 * delete the irrelevant stuff. This ensures that we don't lose any
	 * tax/metadata by trying to do it all manually.
	 */
	protected function migrate_posts() {
		global $wpdb;

		$tables_to_copy = array(
			'posts',
			'postmeta',
			'terms',
			'term_taxonomy',
			'term_relationships',
		);

		// Have to use different syntax for shardb
		$source_site_prefix = $wpdb->get_blog_prefix( $this->source_site_id );
		$site_prefix        = $wpdb->get_blog_prefix( $this->site_id );
		foreach ( $tables_to_copy as $ttc ) {
			$source_table = $source_site_prefix . $ttc;
			$table        = $site_prefix . $ttc;

			// @todo
			if ( defined( 'DO_SHARDB' ) && DO_SHARDB ) {
				global $shardb_hash_length, $shardb_prefix;
				$source_table_hash = strtoupper( substr( md5( $this->source_site_id ), 0, $shardb_hash_length ) );
				$table_hash        = strtoupper( substr( md5( $this->site_id ), 0, $shardb_hash_length ) );

				$source_table = $shardb_prefix . $source_table_hash . '.' . $source_table;
				$table        = $shardb_prefix . $table_hash . '.' . $table;
			}

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DROP TABLE {$table}" );
			$wpdb->query( "CREATE TABLE {$table} LIKE {$source_table}" );
			$wpdb->query( "INSERT INTO {$table} SELECT * FROM {$source_table}" );
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		// Loop through all posts and:
		// - if it's by an admin, switch to draft
		// - if it's not by an admin, delete
		// - if it's a nav item, change the GUID and the menu item URL meta
		switch_to_blog( $this->site_id );

		$source_site_url = get_blog_option( $this->source_site_id, 'home' );
		$dest_site_url   = get_option( 'home' );

		// Copy over attachments. Whee!
		$upload_dir = wp_upload_dir();
		$this->copyr( str_replace( $this->site_id, $this->source_site_id, $upload_dir['basedir'] ), $upload_dir['basedir'] );

		$site_posts          = $wpdb->get_results( "SELECT ID, guid, post_author, post_status, post_title, post_type FROM {$wpdb->posts}" );
		$source_group_admins = $this->get_source_group_admins();
		foreach ( $site_posts as $sp ) {
			if ( ! is_super_admin( $sp->post_author ) && ! in_array( (int) $sp->post_author, $source_group_admins, true ) && 'nav_menu_item' !== $sp->post_type ) {
				// Non-admins have their stuff deleted.
				if ( 'attachment' === $sp->post_type ) {
					$atts_to_delete_ids[] = $sp->ID;
				} else {
					$posts_to_delete_ids[] = $sp->ID;
				}
			} elseif ( $this->change_content_attribution() ) {
				// Admin-created content comes along, but may have its authorship changed.
				if ( $this->change_content_attribution() ) {
					wp_update_post(
						[
							'ID'          => $sp->ID,
							'post_author' => bp_loggedin_user_id(),
						]
					);
				}
			}

			if ( 'nav_menu_item' === $sp->post_type ) {
				$wpdb->update(
					$wpdb->posts,
					array(
						'guid' => str_replace( $source_site_url, $dest_site_url, $sp->guid ),
					),
					array(
						'ID' => $sp->ID,
					)
				);

				$url     = get_post_meta( $sp->ID, '_menu_item_url', true );
				$classes = get_post_meta( $sp->ID, '_menu_item_classes', true );

				if ( $url ) {
					update_post_meta( $sp->ID, '_menu_item_url', str_replace( $source_site_url, $dest_site_url, $url ) );
				}

				// Update "Group Profile" nav item url.
				if ( ! empty( $classes ) && in_array( 'group-profile-link', $classes, true ) ) {
					$group = groups_get_group( $this->group_id );
					update_post_meta( $sp->ID, '_menu_item_url', bp_get_group_permalink( $group ) );
				}

				continue;
			}

			if ( in_array( (int) $sp->post_author, $source_group_admins, true ) ) {
				if ( 'publish' === $sp->post_status ) {
					$post_arr = array(
						'ID'          => $sp->ID,
						'post_status' => 'draft',
					);
					wp_update_post( $post_arr );

					wp_update_comment_count_now( $sp->ID );
				}
			} elseif ( 'attachment' === $sp->post_type ) {
				// Non-teachers have their stuff deleted.
				// Will delete the file as well.
				wp_delete_attachment( $sp->ID, true );
			} else {
				wp_delete_post( $sp->ID, true );
			}
		}

		// Replace the site URL in all post content.
		// For some reason a regular MySQL query is not working.
		$this_site_url = get_option( 'home' );
		foreach ( $wpdb->get_col( "SELECT ID FROM $wpdb->posts" ) as $post_id ) {
			$post               = get_post( $post_id );
			$post->post_content = str_replace( $source_site_url, $this_site_url, $post->post_content );
			wp_update_post( $post );
		}

		restore_current_blog();
	}

	protected function get_source_group_admins() {
		if ( empty( $this->source_group_admins ) ) {
			$g                         = groups_get_group(
				array(
					'group_id'        => $this->source_group_id,
					'populate_extras' => true,
				)
			);
			$this->source_group_admins = wp_list_pluck( $g->admins, 'user_id' );
		}

		return array_map( 'intval', $this->source_group_admins );
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.1
	 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
	 * @param       string $source    Source path
	 * @param       string $dest      Destination path
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	public function copyr( $source, $dest ) {
		// Check for symlinks
		if ( is_link( $source ) ) {
			return symlink( readlink( $source ), $dest );
		}

		// Simple copy for a file
		if ( is_file( $source ) ) {
			return copy( $source, $dest );
		}

		// Nothing to do here.
		if ( ! file_exists( $source ) ) {
			return;
		}

		// Make destination directory
		if ( ! is_dir( $dest ) ) {
			wp_mkdir_p( $dest );
		}

		// Loop through the folder
		$dir = dir( $source );
		// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
		while ( false !== $entry = $dir->read() ) {
			// Skip pointers
			if ( '.' === $entry || '..' === $entry ) {
				continue;
			}

			// Deep copy directories
			$this->copyr( "$source/$entry", "$dest/$entry" );
		}

		// Clean up
		$dir->close();
		return true;
	}

	/**
	 * Determines whether content attribution should be switched to current user.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	protected function change_content_attribution() {
		$change = groups_get_groupmeta( $this->group_id, 'change_cloned_content_attribution' );
		return (bool) $change;
	}
}
