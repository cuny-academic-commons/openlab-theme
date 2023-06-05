<?php

class Openlab_Clone_Course_Group {
	public $group_id;
	public $source_group_id;

	public $source_group_admins = array();

	public function __construct( $group_id, $source_group_id ) {
		$this->group_id        = $group_id;
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

	protected function migrate_docs() {
		$docs_args = array(
			'group_id'       => $this->source_group_id,
			'posts_per_page' => '-1',
		);

		if ( bp_docs_has_docs( $docs_args ) ) {

			$bp_docs_query       = new BP_Docs_Query();
			$source_group_admins = $this->get_source_group_admins();

			while ( bp_docs_has_docs() ) {
				bp_docs_the_doc();

				global $post;

				// Skip non-admin posts
				if ( in_array( (int) $post->post_author, $source_group_admins, true ) ) {

					// Docs has no good way of mass producing posts
					// We will insert the post via WP and manually
					// add the metadata
					$post_a = (array) $post;
					unset( $post_a['ID'] );

					if ( $this->change_content_attribution() ) {
						$post_a['post_author'] = bp_loggedin_user_id();
					}

					$new_doc_id = wp_insert_post( $post_a );

					// Associated group
					bp_docs_set_associated_group_id( $new_doc_id, $this->group_id );

					// Associated user tax
					$user         = new WP_User( $post->post_author );
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
					$temp_query             = new stdClass();
					$temp_query->doc_id     = $new_doc_id;
					$temp_query->is_new_doc = true;
					$temp_query->item_type  = 'group';
					$temp_query->item_id    = $this->group_id;
					buddypress()->bp_docs->post_activity( $temp_query );
				}
			}
		}
	}

	protected function migrate_files() {
		$source_group_admins = $this->get_source_group_admins();
		$source_files        = BP_Group_Documents::get_list_by_group( $this->source_group_id );

		foreach ( $source_files as $source_file ) {
			if ( ! in_array( (int) $source_file['user_id'], $source_group_admins, true ) ) {
				continue;
			}

			// Set up the document info
			$document = new BP_Group_Documents();

			$document->group_id = $this->group_id;

			if ( $this->change_content_attribution() ) {
				$document->user_id = bp_loggedin_user_id();
			} else {
				$document->user_id = $source_file['user_id'];
			}

			$document->name        = $source_file['name'];
			$document->description = $source_file['description'];
			$document->file        = $source_file['file'];
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
		$forum_ids           = bbp_get_group_forum_ids( $this->group_id );

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

		$source_forum_topics = new WP_Query(
			array(
				'post_type'      => bbp_get_topic_post_type(),
				'post_parent'    => $source_forum_id,
				'posts_per_page' => -1,
				'author__in'     => $source_group_admins,
			)
		);
		$group               = groups_get_group( array( 'group_id' => $this->group_id ) );

		// Set the default forum status
		switch ( $group->status ) {
			case 'hidden':
				$status = bbp_get_hidden_status_id();
				break;
			case 'private':
				$status = bbp_get_private_status_id();
				break;
			case 'public':
			default:
				$status = bbp_get_public_status_id();
				break;
		}

		// Then post them
		foreach ( $source_forum_topics->posts as $sftk ) {
			$topic_args = [
				'post_parent'  => $forum_id,
				'post_status'  => $status,
				'post_author'  => $sftk->post_author,
				'post_content' => $sftk->post_content,
				'post_title'   => $sftk->post_title,
				'post_date'    => $sftk->post_date,
			];

			if ( $this->change_content_attribution() ) {
				$topic_args['post_author'] = bp_loggedin_user_id();
			}

			bbp_insert_topic(
				$topic_args,
				array(
					'forum_id' => $forum_id,
				)
			);
		}

		// @todo - forum attachments
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

		return $this->source_group_admins;
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
