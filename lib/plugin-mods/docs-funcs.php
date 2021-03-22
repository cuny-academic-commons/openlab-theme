<?php

/**
 * Customizations related to BuddyPress Docs.
 */

/**
 * Plugin: BuddyPress Docs
 * See also: openlab/buddypress/docs for template overrides
 */

function openlab_disable_wplink_for_docs( $plugins ) {
	if ( ! bp_docs_is_doc_edit() && ! bp_docs_is_doc_create() ) {
		return $plugins;
	}

	return array_diff( $plugins, array( 'wplink' ) );
}
add_filter( 'tiny_mce_plugins', 'openlab_disable_wplink_for_docs' );

function openlab_bp_docs_template( $template ) {
	global $bp;

	switch ( $bp->bp_docs->current_view ) {
		case 'edit':
		case 'create':
			$template = bp_locate_template( 'docs/single/edit.php' );
			break;

		case 'single':
			$template = bp_locate_template( 'docs/single/index.php' );
			break;

		case 'list':
			$template = bp_locate_template( 'docs/docs-loop.php' );
			break;
	}
	return $template;
}
add_filter( 'bp_docs_template', 'openlab_bp_docs_template' );

add_action(
	'bp_docs_setup_theme_compat',
	function( $theme_compat ) {
		remove_action( 'bp_replace_the_content', array( $theme_compat, 'single_content' ) );
		remove_action( 'bp_replace_the_content', array( $theme_compat, 'create_content' ) );
	}
);

/**
 * BuddyPress Docs directory filters should be disabled.
 */
add_filter( 'bp_docs_filter_types', '__return_empty_array', 999 );


/**
 * Plugin: BuddyPress Docs
 * Disable group creation step.
 */
add_filter( 'bp_docs_force_enable_at_group_creation', '__return_true' );

/**
 * Plugin: BuddyPress Docs
 * Overriding the BP Docs header file to clean up sub menus
 *
 * @param type $menu_template
 * @return string
 */
function openlab_hide_docs_native_menu() {
	return bp_locate_template( 'docs/docs-header.php' );
}
add_filter( 'bp_docs_header_template', 'openlab_hide_docs_native_menu' );

/**
 * Allow super admins to edit any BuddyPress Doc
 *
 * @global type $bp
 * @param type $user_can
 * @param type $action
 * @return boolean
 */
function openlab_allow_super_admins_to_edit_bp_docs( $user_can, $action ) {
	global $bp;

	if ( 'edit' === $action ) {
		if ( is_super_admin() || bp_loggedin_user_id() === get_the_author_meta( 'ID' ) || $user_can ) {
			$user_can                                 = true;
			$bp->bp_docs->current_user_can[ $action ] = 'yes';
		} else {
			$user_can                                 = false;
			$bp->bp_docs->current_user_can[ $action ] = 'no';
		}
	}

	return $user_can;
}

add_filter( 'bp_docs_current_user_can', 'openlab_allow_super_admins_to_edit_bp_docs', 10, 2 );

/**
 * Cache-friendly method for fetching a group's Docs count.
 */
function openlab_get_group_doc_count( $group_id ) {
	$cache_key = $group_id . wp_cache_get_last_changed( 'posts' );
	$count     = wp_cache_get( $cache_key, 'bp_docs_group_doc_counts' );
	if ( false === $count ) {
		$dq = new BP_Docs_Query(
			array(
				'group_id' => $group_id,
			)
		);

		add_filter( 'bp_docs_pre_query_args', 'openlab_filter_docs_query_for_count' );
		$doc_query = $dq->get_wp_query();
		remove_filter( 'bp_docs_pre_query_args', 'openlab_filter_docs_query_for_count' );

		$count = $doc_query->found_posts;
		wp_cache_set( $cache_key, $count, 'bp_docs_group_doc_counts' );
	}

	return intval( $count );
}

function openlab_filter_docs_query_for_count( $args ) {
	$args['fields']         = 'ids';
	$args['posts_per_page'] = -1;
	return $args;
}

/**
 * Hack alert! Allow group avatars to be deleted
 *
 * There is a bug in BuddyPress Docs that blocks group avatar deletion, because
 * BP Docs is too greedy about setting its current view, and thinks that you're
 * trying to delete a Doc instead. Instead of fixing that, which I have no
 * patience for at the moment, I'm just going to override BP Docs's current
 * view in the case of deleting an avatar.
 */
function openlab_fix_avatar_delete( $view ) {
	if ( bp_is_group_admin_page() ) {
		$view = '';
	}

	return $view;
}

add_filter( 'bp_docs_get_current_view', 'openlab_fix_avatar_delete', 9999 );

/**
 * Remove the 'Unlink from Group' link and replace with 'Delete'.
 */
function openlab_docs_action_links( $links, $doc_id ) {
	$link_index = null;
	foreach ( $links as $link_index => $link ) {
		if ( false === strpos( $link, 'unlink-from-group' ) ) {
			continue;
		}
	}

	$delete_link = null;
	if ( current_user_can( 'bp_docs_manage', $doc_id ) ) {
		$delete_link = sprintf(
			'<a href="%s" class="delete confirm">%s</a>',
			esc_url( bp_docs_get_delete_doc_link() ),
			esc_html__( 'Delete', 'bp-docs' )
		);
	}

	if ( $delete_link ) {
		$links[ $link_index ] = $delete_link;
	} else {
		unset( $links[ $link_index ] );
		$links = array_values( $links );
	}

	return $links;
}
add_filter( 'bp_docs_doc_action_links', 'openlab_docs_action_links', 50, 2 );

/**
 * Add missing associated_group_id field to Docs edit screen.
 *
 * Otherwise, during group creation, the AJAX query for fetching default access settings will fail.
 */
function openlab_docs_associated_group_id_field() {
	printf( '<input type="hidden" name="associated_group_id" id="associated_group_id" value="%s" />', esc_attr( bp_get_current_group_id() ) );
}
add_action( 'bp_docs_closing_meta_box', 'openlab_docs_associated_group_id_field' );

/**
 * Checks whether Docs is enabled for a group.
 *
 * @param int $group_id Group ID.
 * @return bool
 */
function openlab_is_docs_enabled_for_group( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Default to true in case no value is found.
	if ( ! $group_id ) {
		return true;
	}

	$group_settings = bp_docs_get_group_settings( $group_id );

	// Default to true in case no value is found.
	if ( ! $group_settings || ! isset( $group_settings['group-enable'] ) ) {
		return true;
	}

	return ! empty( $group_settings['group-enable'] );
}

/**
 * Manages email notifications for Docs.
 *
 * @since 1.3.0
 *
 * @param bool   $send_it  Whether the notification should be sent.
 * @param object $activity Activity object.
 * @param int    $user_id  ID of the user.
 * @param string $sub      Subscription level of the user.
 * @return bool
 */
// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
function openlab_docs_activity_notification_control( $send_it, $activity, $user_id, $sub ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	switch ( $activity->type ) {
		case 'bp_doc_created':
		case 'bp_doc_edited':
		case 'bp_doc_comment':
			return openlab_notify_group_members_of_this_action() && 'no' !== $sub;

		default:
			return $send_it;
	}
}
add_action( 'bp_ass_send_activity_notification_for_user', 'openlab_docs_activity_notification_control', 100, 4 );
add_action( 'bp_ges_add_to_digest_queue_for_user', 'openlab_docs_activity_notification_control', 100, 4 );


/**
 * Inject "Notify members" interface before Docs comment submit button.
 *
 * @since 1.3.0
 */
add_filter(
	'comment_form_submit_button',
	function( $button ) {
		if ( ! bp_docs_is_existing_doc() ) {
			return $button;
		}

		ob_start();
		?>
		<div class="notify-group-members-ui">
			<?php openlab_notify_group_members_ui( true ); ?>
		</div>
		<?php
		$ui = ob_get_contents();
		ob_end_clean();

		return $ui . $button;
	},
	100
);
