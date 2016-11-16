<?php

/**
 * Course cloning
 */

/**
 * Get the courses that a user is an admin of
 */
function openlab_get_courses_owned_by_user( $user_id ) {
	global $wpdb, $bp;

	// This is pretty hackish, but the alternatives are all hacks too
	// First, get list of all groups a user is in
	$is_admin_of = BP_Groups_Member::get_is_admin_of( $user_id );
	$is_admin_of_ids = wp_list_pluck( $is_admin_of['groups'], 'id' );
	if ( empty( $is_admin_of_ids ) ) {
		$is_admin_of_ids = array( 0 );
	}

	// Next, get list of those that are courses
	$user_course_ids = $wpdb->get_col( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = 'course' AND group_id IN (" . implode( ',', wp_parse_id_list( $is_admin_of_ids ) ) . ")" );
	if ( empty( $user_course_ids ) ) {
		$user_course_ids = array( 0 );
	}

	// Finally, get a pretty list
	$user_courses = groups_get_groups( array(
		'type' => 'alphabetical',
		'include' => $user_course_ids,
		'show_hidden' => true,
		'per_page' => 100000,
		'populate_extras' => false,
	) );

	return $user_courses;
}

/**
 * Catch form submits and save to the new group
 */
function openlab_clone_create_form_catcher() {
	$new_group_id = bp_get_new_group_id();

	switch ( bp_get_groups_current_create_step() ) {
		case 'group-details' :
			if ( isset( $_POST['create-or-clone'] ) && 'clone' === $_POST['create-or-clone'] ) {
				$clone_source_group_id = isset( $_POST['group-to-clone'] ) ? (int) $_POST['group-to-clone'] : 0;

				if ( ! $clone_source_group_id ) {
					return;
				}

				groups_update_groupmeta( $new_group_id, 'clone_source_group_id', $clone_source_group_id );
				openlab_clone_course_group( $new_group_id, $clone_source_group_id );

				if ( isset( $_POST['new_or_old'] ) && ( 'clone' === $_POST['new_or_old'] ) && isset( $_POST['blog-id-to-clone'] ) && isset( $_POST['wds_website_check'] ) ) {
					$clone_source_blog_id = groups_get_groupmeta( $clone_source_group_id, 'wds_bp_group_site_id' );
					groups_update_groupmeta( $new_group_id, 'clone_source_blog_id', $clone_source_blog_id );

					// @todo validation
					$clone_destination_path = friendly_url( stripslashes( $_POST['clone-destination-path'] ) );
					groups_update_groupmeta( $new_group_id, 'clone_destination_path', $clone_destination_path );

					openlab_clone_course_site( $new_group_id, $clone_source_group_id, $clone_source_blog_id, $clone_destination_path );
				}
			}
			break;

		case 'group-settings' :
			$clone_source_group_id = intval( groups_get_groupmeta( $new_group_id, 'clone_source_group_id' ) );

			if ( ! $clone_source_group_id ) {
				return;
			}

			break;
	}
}
add_action( 'groups_create_group_step_complete', 'openlab_clone_create_form_catcher' );

/** FILTERS ***********************************************************/

/**
 * Swap out the group privacy status, if available
 */
function openlab_clone_bp_get_new_group_status( $status ) {
	$clone_source_group_id = intval( groups_get_groupmeta( bp_get_new_group_id(), 'clone_source_group_id' ) );

	if ( $clone_source_group_id ) {
		$clone_source_group = groups_get_group( array( 'group_id' => $clone_source_group_id ) );
		$status = $clone_source_group->status;
	}

	return $status;
}
add_filter( 'bp_get_new_group_status', 'openlab_clone_bp_get_new_group_status' );

/**
 * AJAX handler for fetching group details
 */
function openlab_group_clone_fetch_details() {
	$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
	$retval = openlab_group_clone_details( $group_id );

	die( json_encode( $retval ) );
}
add_action( 'wp_ajax_openlab_group_clone_fetch_details', 'openlab_group_clone_fetch_details' );

function openlab_group_clone_details( $group_id ) {
	$retval = array(
		'group_id'               => $group_id,
		'name'                   => '',
		'description'            => '',
		'schools'                => array(),
		'departments'            => array(),
		'course_code'            => '',
		'section_code'           => '',
		'additional_description' => '',
		'site_id'                => '',
		'site_url'               => '',
		'site_path'              => '',
	);

	if ( $group_id ) {
		$group = groups_get_group( array( 'group_id' => $group_id ) );

		$retval['name'] = $group->name;
		$retval['description'] = $group->description;

		$schools = groups_get_groupmeta( $group_id, 'wds_group_school' );
		if ( ! empty( $schools ) ) {
			$retval['schools'] = explode( ',', $schools );
		}

		$departments = groups_get_groupmeta( $group_id, 'wds_departments' );
		if ( ! empty( $departments ) ) {
			$retval['departments'] = explode( ',', $departments );
		}

		$retval['course_code'] = groups_get_groupmeta( $group_id, 'wds_course_code' );
		$retval['section_code'] = groups_get_groupmeta( $group_id, 'wds_section_code' );
		$retval['additional_description'] = groups_get_groupmeta( $group_id, 'wds_course_html' );

		$retval['site_id'] = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );
		$retval['site_url'] = get_blog_option( $retval['site_id'], 'home' );
		$retval['site_path'] = str_replace( bp_get_root_domain(), '', $retval['site_url'] );
	}

	return $retval;
}

function openlab_clone_course_group( $group_id, $source_group_id ) {
	$c = new Openlab_Clone_Course_Group( $group_id, $source_group_id );
	$c->go();
}

function openlab_clone_course_site( $group_id, $source_group_id, $source_site_id, $clone_destination_path ) {
	$c = new Openlab_Clone_Course_Site( $group_id, $source_group_id, $source_site_id, $clone_destination_path );
	$c->go();
}

/** CLASSES ******************************************************************/

class Openlab_Clone_Course_Group {
	var $group_id;
	var $source_group_id;

	var $source_group_admins = array();

	public function __construct( $group_id, $source_group_id ) {
		$this->group_id = $group_id;
		$this->source_group_id = $source_group_id;
	}

	/**
	 * Summary:
	 * - Some groupmeta
	 * - Docs posted by admins (but no comments)
	 * - Files posted by admins
	 * - Discussion topics posted by admins (but no replies)
	 */
	public function go() {
		$this->migrate_groupmeta();
		$this->migrate_avatar();
		$this->migrate_docs();
		$this->migrate_files();
		$this->migrate_topics();
	}

	protected function migrate_groupmeta() {
		$keys = array(
			'ass_default_subscription',
			'bpdocs',
			'external_site_comments_feed',
			'external_site_posts_feed',
			'external_site_type',
			'external_site_url',
			'invite_status',
		);

		foreach ( $keys as $k ) {
			$v = groups_get_groupmeta( $this->source_group_id, $k );
			groups_update_groupmeta( $this->group_id, $k, $v );
		}
	}

	protected function migrate_avatar() {
		$avatar_path  = trailingslashit( bp_core_avatar_upload_path() ) . trailingslashit( 'group-avatars' );
		$source_avatar_dir = $avatar_path . $this->source_group_id;

		if ( file_exists( $source_avatar_dir ) ) {
			if ( $av_dir = opendir( $source_avatar_dir ) ) {
				$dest_avatar_dir = $avatar_path . $this->group_id;
				mkdir( $dest_avatar_dir );
				while ( false !== ( $source_avatar_file = readdir( $av_dir ) ) ) {
					if ( 2 < strlen( $source_avatar_file ) ) {
						copy( $source_avatar_dir . '/' . $source_avatar_file, $dest_avatar_dir . '/' . $source_avatar_file );
					}
				}
			}
		}
	}

	protected function migrate_docs() {
		$docs = array();

		$bp_docs_query = new BP_Docs_Query;

		$group_type_term = term_exists( 'group', $bp_docs_query->associated_item_tax_name );

		$source_group = groups_get_group( array( 'group_id' => $this->source_group_id ) );

		$source_group_term = term_exists( $source_group->slug, $bp_docs_query->associated_item_tax_name, $group_type_term['term_id'] );

		$docs_args = array(
			'post_type' => $bp_docs_query->post_type_name,
			'tax_query' => array(
				array(
					'taxonomy' => $bp_docs_query->associated_item_tax_name,
					'field' => 'id',
					'terms' => $source_group_term['term_id'],
				),
			),
			'posts_per_page' => '-1',
		);

		$dq = new WP_Query( $docs_args );

		if ( $dq->have_posts() ) {

			$source_group_admins = $this->get_source_group_admins();

			$group = groups_get_group( array( 'group_id' => $this->group_id ) );

			// manually create the term
			$item_term_args = array(
				'description' => $group->name,
				'slug' => $group->slug,
				'parent' => $group_type_term['term_id'],
			);

			$item_term = wp_insert_term( $group->id, $bp_docs_query->associated_item_tax_name, $item_term_args );

			while ( $dq->have_posts() ) {
				$dq->the_post();

				global $post;

				// Skip non-admin posts
				if ( in_array( $post->post_author, $source_group_admins ) ) {

					// Docs has no good way of mass producing posts
					// We will insert the post via WP and manually
					// add the metadata
					$post_a = (array) $post;
					unset( $post_a['ID'] );
					$new_doc_id = wp_insert_post( $post_a );

					// Associated group
					wp_set_post_terms( $new_doc_id, $item_term['term_id'], $bp_docs_query->associated_item_tax_name );

					// Set last editor
					$last_editor = get_post_meta( $post->ID, 'bp_docs_last_editor', true );
					update_post_meta( $new_doc_id, 'bp_docs_last_editor', $last_editor );

					// Migrate settings. @todo Access validation? in case new group has more restrictive settings than previous
					$settings = get_post_meta( $post->ID, 'bp_docs_settings', true );
					update_post_meta( $new_doc_id, 'bp_docs_settings', $settings );

					// Set revision count to 1 - we're not bringing revisions with us
					update_post_meta( $new_doc_id, 'bp_docs_revision_count', 1 );

					// Update activity stream
					$temp_query = new stdClass;
					$temp_query->doc_id = $new_doc_id;
					$temp_query->is_new_doc = true;
					$temp_query->item_type = 'group';
					$temp_query->item_id = $this->group_id;
					BP_Docs_BP_Integration::post_activity( $temp_query );
				}
			}
		}

		/* 1.4+ */
		/*
		$docs_args = array(
			'group_id' => $this->source_group_id,
			'posts_per_page' => '-1',
		);

		if ( bp_docs_has_docs( $docs_args ) ) {

			$bp_docs_query = new BP_Docs_Query;
			$source_group_admins = $this->get_source_group_admins();

			while ( bp_docs_has_docs() ) {
				bp_docs_the_doc();

				global $post;

				// Skip non-admin posts
				if ( in_array( $post->post_author, $source_group_admins ) ) {

					// Docs has no good way of mass producing posts
					// We will insert the post via WP and manually
					// add the metadata
					$post_a = (array) $post;
					unset( $post_a['ID'] );
					$new_doc_id = wp_insert_post( $post_a );

					// Associated group
					bp_docs_set_associated_group_id( $new_doc_id, $this->group_id );

					// Associated user tax
					$user = new WP_User( $post->post_author );
					$user_term_id = bp_docs_get_item_term_id( $user->ID, 'user', $user->display_name );
					wp_set_post_terms( $new_doc_id, $user_term_id, $bp_docs_query->associated_item_tax_name, true );

					// Set last editor
					$last_editor = get_post_meta( $post->ID, 'bp_docs_last_editor', true );
					update_post_meta( $new_doc_id, 'bp_docs_last_editor', $last_editor );

					// Migrate settings. @todo Access validation? in case new group has more restrictive settings than previous
					$settings = get_post_meta( $post->ID, 'bp_docs_settings', true );
					update_post_meta( $new_doc_id, 'bp_docs_settings', $settings );

					// Read setting to a taxonomy
					$read_setting = isset( $settings['read'] ) ? $settings['read'] : 'anyone';
					bp_docs_update_doc_access( $new_doc_id, $read_setting );

					// Set revision count to 1 - we're not bringing revisions with us
					update_post_meta( $new_doc_id, 'bp_docs_revision_count', 1 );

					// Update activity stream
					$temp_query = new stdClass;
					$temp_query->doc_id = $new_doc_id;
					$temp_query->is_new_doc = true;
					$temp_query->item_type = 'group';
					$temp_query->item_id = $this->group_id;
					BP_Docs_Component::post_activity( $temp_query );
				}
			}
		}
		*/
	}

	protected function migrate_files() {
		$source_group_admins = $this->get_source_group_admins();
		$source_files = BP_Group_Documents::get_list_by_group( $this->source_group_id );

		foreach ( $source_files as $source_file ) {
			if ( ! in_array( $source_file['user_id'], $source_group_admins ) ) {
				continue;
			}

			// Set up the document info
			$document = new BP_Group_Documents();

			$document->group_id = $this->group_id;

			$document->user_id = $source_file['user_id'];
			$document->name = $source_file['name'];
			$document->description = $source_file['description'];
			$document->file = $source_file['file'];
			$document->save( false ); // false is "don't check file upload"

			// Copy the file itself
			$destination_dir = bp_core_avatar_upload_path() . '/group-documents/' . $this->group_id;
			if ( ! is_dir( $destination_dir ) ) {
				mkdir( $destination_dir, 0755, true );
			}

			$destination_path = $destination_dir . '/' . $document->file;

			$source_path = bp_core_avatar_upload_path() . '/group-documents/' . $this->source_group_id . '/' . $document->file;

			copy( $source_path, $destination_path );
		}
	}

        protected function migrate_topics() {
		$source_group_admins = $this->get_source_group_admins();
		$forum_ids = bbp_get_group_forum_ids( $this->group_id );

		// Should never happen, but just in case
		// (without this, it returns all topics)
		if ( empty( $forum_ids ) ) {
			return;
		}
		$forum_id = $forum_ids[0];

		// Get source topics
		$source_forum_ids = bbp_get_group_forum_ids( $this->source_group_id );
		if ( empty( $source_forum_ids ) ) {
			return;
		}

		$source_forum_id = $source_forum_ids[0];
		if ( ! $source_forum_id ) {
			return;
		}

		$source_forum_topics = new WP_Query( array(
			'post_type' => bbp_get_topic_post_type(),
			'post_parent' => $source_forum_id,
			'posts_per_page' => -1,
			'author__in' => $source_group_admins,
		) );
		$group = groups_get_group( array( 'group_id' => $this->group_id ) );

		// Set the default forum status
		switch ( $group->status ) {
			case 'hidden' :
				$status = bbp_get_hidden_status_id();
				break;
			case 'private' :
				$status = bbp_get_private_status_id();
				break;
			case 'public' :
			default :
				$status = bbp_get_public_status_id();
				break;
		}

		// Then post them
		foreach ( $source_forum_topics->posts as $sftk ) {
			bbp_insert_topic( array(
				'post_parent' => $forum_id,
				'post_status' => $status,
				'post_author' => $sftk->post_author,
				'post_content' => $sftk->post_content,
				'post_title' => $sftk->post_title,
				'post_date' => $sftk->post_date,
			), array(
				'forum_id' => $forum_id,
			) );
		}

		return;

		// bbPress 1

		// rekey for better lookups
		$source_forum_topics_keyed = array();
		foreach ($source_forum_topics as $sft) {
			if (!in_array($sft->post_author, $source_group_admins)) {
				continue;
			}
			$source_forum_topics_keyed[$sft->ID] = $sft;
		}

                // And their first posts
		global $wpdb, $bp, $bbdb;
		$source_forum_topic_ids = implode( ',', wp_list_pluck( $source_forum_topics, 'topic_id' ) );
		$source_forum_posts = $wpdb->get_results( "SELECT topic_id, post_text FROM {$bbdb->posts} WHERE topic_id IN ({$source_forum_topic_ids}) AND post_position = 1" );

		foreach ( $source_forum_posts as $sfp ) {
			if ( isset( $source_forum_topics_keyed[ $sfp->topic_id ] ) ) {
				$source_forum_topics_keyed[ $sfp->topic_id ]->post_text = $sfp->post_text;
			}
		}

		// Then post them
		foreach ( $source_forum_topics_keyed as $sftk ) {
			bp_forums_new_topic( array(
				'forum_id' => $forum_id,
				'topic_title' => $sftk->topic_title,
				'topic_slug' => $sftk->topic_slug,
				'topic_poster' => $sftk->topic_poster,
				'topic_poster_name' => $sftk->topic_poster_name,
				'topic_last_poster' => $sftk->topic_last_poster,
				'topic_last_poster_name' => $sftk->topic_last_poster_name,
				'topic_start_time' => $sftk->topic_start_time,
				'topic_time' => $sftk->topic_time,
				'topic_text' => $sftk->post_text,
			) );
		}

		// @todo - forum attachments
	}

	protected function get_source_group_admins() {
            if (empty($this->source_group_admins)) {
            $g = groups_get_group(array(
                'group_id' => $this->source_group_id,
                'populate_extras' => true,
                    ));
            $this->source_group_admins = wp_list_pluck($g->admins, 'user_id');
        }

        return $this->source_group_admins;
	}
}

class Openlab_Clone_Course_Site {
	var $group_id;
	var $site_id;

	var $source_group_id;
	var $source_site_id;
	var $destination_path;

	var $source_group_admins = array();

	public function __construct( $group_id, $source_group_id, $source_site_id, $destination_path ) {
		$this->group_id = $group_id;
		$this->source_group_id = $source_group_id;
		$this->source_site_id = $source_site_id;
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

		// Assemble args and create the new site
		$domain = $wpdb->get_var( "SELECT domain FROM {$wpdb->blogs} WHERE blog_id = 1" );

		$clone_destination_path = groups_get_groupmeta( $this->group_id, 'clone_destination_path' );
		$path = '/' . $clone_destination_path . '/';

		$group = groups_get_group( array( 'group_id' => $this->group_id ) );
		$title = $group->name;

		$user_id = $group->creator_id;

		$meta = array(
			'public' => get_blog_option( $this->source_site_id, 'blog_public' ),
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
			groups_update_groupmeta( $this->group_id, 'wds_bp_group_site_id', $this->site_id );
		}
	}

	/**
	 * Taken from site-template
	 */
	protected function migrate_site_settings() {
		global $wpdb;

		switch_to_blog( $this->source_site_id );

		// get all old options
		$all_options = wp_load_alloptions();
		$options = array();
		foreach ( array_keys( $all_options ) as $key ) {
			$options[ $key ] = get_option( $key );  // have to do this to deal with arrays
		}

		// theme mods -- don't show up in all_options.
		// Only add options for the current theme
		$theme = get_option( 'current_theme' );
		$mods = get_option( 'mods_' . $theme );

		$preserve_option = array(
			'siteurl',
			'blogname',
			'admin_email',
			'new_admin_email',
			'home',
			'upload_path',
			'db_version',
			$wpdb->get_blog_prefix( $this->site_id ) . 'user_roles',
			'fileupload_url',
		);

		// now write them all back
		switch_to_blog( $this->site_id );
		foreach ( $options as $key => $value ) {
			if ( !in_array( $key, $preserve_option ) ) {
				update_option( $key, $value );
			}
		}

		// add the theme mods
		update_option( 'mods_' . $theme, $mods );

                // Just in case
                create_initial_taxonomies();
                flush_rewrite_rules();

		restore_current_blog();
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
		$site_prefix = $wpdb->get_blog_prefix( $this->site_id );
		foreach ( $tables_to_copy as $ttc ) {
			$source_table = $source_site_prefix . $ttc;
			$table = $site_prefix . $ttc;

			// @todo
			if ( defined( 'DO_SHARDB' ) && DO_SHARDB ) {
                                global $shardb_hash_length, $shardb_prefix;
                                $source_table_hash = strtoupper( substr( md5( $this->source_site_id ), 0, $shardb_hash_length ) );
                                $table_hash = strtoupper( substr( md5( $this->site_id ), 0, $shardb_hash_length ) );

                                $source_table = $shardb_prefix . $source_table_hash . '.' . $source_table;
                                $table = $shardb_prefix . $table_hash . '.' . $table;
			}

			$wpdb->query( "DELETE FROM {$table}" );
			$wpdb->query( "INSERT INTO {$table} SELECT * FROM {$source_table}" );
		}

		// Loop through all posts and:
		// - if it's by an admin, switch to draft
		// - if it's not by an admin, delete
		// - if it's a nav item, change the GUID and the menu item URL meta
		switch_to_blog( $this->site_id );

		$source_site_url = get_blog_option( $this->source_site_id, 'home' );
		$dest_site_url = get_option( 'home' );

                // Copy over attachments. Whee!
		$upload_dir = wp_upload_dir();
		$this->copyr( str_replace( $this->site_id, $this->source_site_id, $upload_dir['basedir'] ), $upload_dir['basedir'] );

		$site_posts = $wpdb->get_results( "SELECT ID, guid, post_author, post_status, post_title, post_type FROM {$wpdb->posts}" );
		$source_group_admins = $this->get_source_group_admins();
		foreach ( $site_posts as $sp ) {
			if ( in_array( $sp->post_author, $source_group_admins ) ) {
				if ( 'publish' === $sp->post_status ) {
					$post_arr = array(
						'ID' => $sp->ID,
						'post_status' => 'draft',
					);
					wp_update_post( $post_arr );

					wp_update_comment_count_now( $sp->ID );
				}
			} else {
				// Non-teachers have their stuff deleted.
				if ( 'attachment' === $sp->post_type ) {
					// Will delete the file as well.
					wp_delete_attachment( $sp->ID, true );
				} else {
					wp_delete_post( $sp->ID, true );
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

				$url = get_post_meta( $sp->ID, '_menu_item_url', true );
				if ( $url ) {
					update_post_meta( $sp->ID, '_menu_item_url', str_replace( $source_site_url, $dest_site_url, $url ) );
				}
			}
		}

		// Replace the site URL in all post content.
                // For some reason a regular MySQL query is not working.
                $this_site_url = get_option( 'home' );
                foreach ( $wpdb->get_col( "SELECT ID FROM $wpdb->posts" ) as $post_id ) {
                        $post = get_post( $post_id );
                        $post->post_content = str_replace( $source_site_url, $this_site_url, $post->post_content );
                        wp_update_post( $post );
                }

		restore_current_blog();
	}

	protected function get_source_group_admins() {
		if ( empty( $this->source_group_admins ) ) {
			$g = groups_get_group( array(
				'group_id' => $this->source_group_id,
				'populate_extras' => true,
			) );
			$this->source_group_admins = wp_list_pluck( $g->admins, 'user_id' );
		}

		return $this->source_group_admins;
	}

        /**
	 * Copy a file, or recursively copy a folder and its contents
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.1
	 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	function copyr($source, $dest) {
	    // Check for symlinks
	    if (is_link($source)) {
		return symlink(readlink($source), $dest);
	    }

	    // Simple copy for a file
	    if (is_file($source)) {
		return copy($source, $dest);
	    }

	    // Make destination directory
	    if (!is_dir($dest)) {
		mkdir($dest);
	    }

	    // Loop through the folder
	    $dir = dir($source);
	    while (false !== $entry = $dir->read()) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
		    continue;
		}

		// Deep copy directories
		$this->copyr("$source/$entry", "$dest/$entry");
	    }

	    // Clean up
	    $dir->close();
	    return true;
	}
}

