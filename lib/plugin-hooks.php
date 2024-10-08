<?php
/**
 * Plugin hooks
 * Complete archive of plugin hooking for openlab theme, wds-citytech plugin, and mu-plugins
 * Includes actual hooks, related includes, and references for folder/file overwrites and hooks that need to stay elsewhere
 */
/**
 * Invite Anyone
 * See also: openlab/buddypress/members/single/invite-anyone.php for template overrides
 */
require_once get_template_directory() . '/lib/plugin-mods/invite-funcs.php';

/**
 * Event Organiser
 * BuddyPress Event Organiser
 */
if ( function_exists( 'bpeo_is_action' ) ) {
	require_once get_template_directory() . '/lib/plugin-mods/calendar-control.php';
}

/**
 * Plugin: Invite Anyone
 * Don't send friend requests when accepting Invite Anyone invitations
 *
 * @see #666
 */
add_filter( 'invite_anyone_send_friend_requests_on_acceptance', '__return_false' );

/**
 * Buddypress Group Documents
 * See also: mu-plugins/openlab-group-documents-privacy.php
 */
if ( defined( 'BP_GROUP_DOCUMENTS_VERSION' ) ) {
	require_once get_template_directory() . '/lib/plugin-mods/files-funcs.php';
}

if ( defined( 'BP_DOCS_VERSION' ) ) {
	require_once get_template_directory() . '/lib/plugin-mods/docs-funcs.php';
}

/**
 * OpenLab Badges
 */
if ( defined( 'OLBADGES_VERSION' ) ) {
	require_once get_template_directory() . '/lib/plugin-mods/badges.php';
}

/**
 * BuddyPress Group Email Subscription
 * See also: openlab/buddypress/groups/single/notifications.php for template overrides
 */
require_once get_template_directory() . '/lib/plugin-mods/email-funcs.php';

/**
 * Plugin: BuddyPress Group Email Subscription
 * This function overwrites the email status output from the buddypress group email subscription plugin
 * Allows for layout control and Bootstrap injection
 *
 * @global type $members_template
 * @global type $groups_template
 * @param type $user_id
 * @param type $group
 * @return type
 */
function openlab_manage_members_email_status( $user_id = '', $group = '' ) {
	global $members_template, $groups_template;

	// if group admins / mods cannot manage email subscription settings, stop now!
	if ( 'no' === get_option( 'ass-admin-can-edit-email' ) ) {
		return;
	}

	// no user ID? fallback on members loop user ID if it exists
	if ( ! $user_id ) {
		$user_id = ! empty( $members_template->member->user_id ) ? $members_template->member->user_id : false;
	}

	// no user ID? fallback on group loop if it exists
	if ( ! $group ) {
		$group = ! empty( $groups_template->group ) ? $groups_template->group : false;
	}

	// no user or group? stop now!
	if ( ! $user_id || ! is_object( $group ) ) {
		return;
	}

	$user_id = (int) $user_id;

	$group_url = bp_get_group_permalink( $group ) . 'admin/manage-members/email';
	$sub_type  = ass_get_group_subscription_status( $user_id, $group->id );
	echo '<h5>Email Status</h5>';

	echo '<ul class="group-manage-members-bpges-status">';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'no', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/no/' . $user_id . '/', 'ass_member_email_status' ) ) . '" value="no" /> ' . esc_html__( 'No Email', 'commons-in-a-box' ) . '</li>';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'sum', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/sum/' . $user_id . '/', 'ass_member_email_status' ) ) . '" value="sum" /> ' . esc_html__( 'Weekly', 'commons-in-a-box' ) . '</li>';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'dig', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/dig/' . $user_id . '/', 'ass_member_email_status' ) ) . '" value="dig" /> ' . esc_html__( 'Daily', 'commons-in-a-box' ) . '</li>';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'sub', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/sub/' . $user_id . '/', 'ass_member_email_status' ) ) . '" value="sub" /> ' . esc_html__( 'New Topics', 'commons-in-a-box' ) . '</li>';
	echo '  <li><input name="group-manage-members-bpges-status-' . esc_attr( $user_id ) . '" type="radio" ' . checked( 'supersub', $sub_type, false ) . ' data-url="' . esc_url( wp_nonce_url( $group_url . '/supersub/' . $user_id . '/', 'ass_member_email_status' ) ) . '" value="supersub" /> ' . esc_html__( 'All Email', 'commons-in-a-box' ) . '</li>';

	echo '</ul>';

	wp_enqueue_script( 'openlab-bpges-js', get_template_directory_uri() . '/js/bpges.js', array( 'jquery' ), openlab_get_asset_version(), true );
}
remove_action( 'bp_group_manage_members_admin_item', 'ass_manage_members_email_status' );
add_action( 'bp_group_manage_members_admin_item', 'openlab_manage_members_email_status' );

// remove status from group profile pages
add_action(
	'bp_actions',
	function () {
		remove_action( 'bp_group_header_meta', 'ass_group_subscribe_button' );
	}
);


/**
 * Bbpress
 * See also: openlab/bbpress for template overrides
 */

/**
 * Plugin: BBPress
 * Adding the forums submenu into the BBPress layout
 */
function openlab_forum_tabs_output() {
	?>
	<div class="forum-nav">
		<ul class="nav nav-inline">
			<?php bp_get_template_part( 'groups/single/nav/forum' ); ?>
		</ul>

		<?php bbp_get_template_part( 'form', 'search' ); ?>
	</div>
	<?php
}

add_action( 'bbp_before_group_forum_display', 'openlab_forum_tabs_output' );

/**
 * Plugin: BBPress
 * Injectiong bootstrap classes into BBPress comment textarea field
 *
 * @param type $output
 * @return type
 */
function openlab_custom_bbp_content( $output ) {
	if ( strpos( $output, 'textarea' ) !== false ) {
		$output = str_replace( 'wp-editor-area', 'wp-editor-area form-control', $output );
	}

	return $output;
}
add_filter( 'bbp_get_the_content', 'openlab_custom_bbp_content', 10 );

/**
 * Plugin: BBPress
 * Updating BBPress page navigation to include font awesome icons
 *
 * @param type $pag_args
 * @return string
 */
function openlab_bbp_pagination( $pag_args ) {

	$pag_args['prev_text'] = __( '<i class="fa fa-angle-left"></i>' );
	$pag_args['next_text'] = __( '<i class="fa fa-angle-right"></i>' );
	$pag_args['type']      = 'list';

	return $pag_args;
}

add_filter( 'bbp_topic_pagination', 'openlab_bbp_pagination' );

/**
 * Plugin: BBPress
 * Injecting classes into pagination container to unify pagination styling
 *
 * @param type $pagination
 * @return type
 */
function openlab_bbp_paginatin_custom_markup( $pagination ) {

	$pagination = str_replace( 'page-numbers', 'page-numbers pagination', $pagination );

	return $pagination;
}

add_filter( 'bbp_get_forum_pagination_links', 'openlab_bbp_paginatin_custom_markup' );

/**
 * Plugin: BBpress
 * Injecting bootstrap and site standard button classes into subscription toggle button
 *
 * @param type $html
 * @return type
 */
function openlab_style_bbp_subscribe_link( $html ) {
	if ( ! bbp_is_single_topic() ) {
		$html = str_replace( 'class="subscription-toggle"', 'class="subscription-toggle btn btn-primary btn-margin btn-margin-top no-deco"', $html );
	}

	return $html;
}
add_filter( 'bbp_get_user_subscribe_link', 'openlab_style_bbp_subscribe_link', 10 );

/**
 * More generous cap mapping for bbPress topic posting.
 *
 * bbPress maps everything onto Participant. We don't want to have to use that.
 */
function openlab_bbp_map_group_forum_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
	if ( ! function_exists( 'bp_is_group' ) || ! bp_is_group() ) {
		return $caps;
	}
	switch ( $cap ) {
		// If user is a group mmember, allow them to create content.
		case 'read_forum':
		case 'publish_replies':
		case 'publish_topics':
		case 'read_hidden_forums':
		case 'read_private_forums':
			if ( bbp_group_is_member() || bbp_group_is_mod() || bbp_group_is_admin() ) {
				$caps = array( 'exist' );
			}
			break;
		// If user is a group mod ar admin, map to participate cap.
		case 'moderate':
		case 'edit_topic':
		case 'edit_reply':
		case 'view_trash':
		case 'edit_others_replies':
		case 'edit_others_topics':
			if ( bbp_group_is_mod() || bbp_group_is_admin() ) {
				$caps = array( 'exist' );
			}
			break;
		// If user is a group admin, allow them to delete topics and replies.
		case 'delete_topic':
		case 'delete_reply':
			if ( bbp_group_is_admin() ) {
				$caps = array( 'exist' );
			}
			break;
	}
	return apply_filters( 'bbp_map_group_forum_topic_meta_caps', $caps, $cap, $user_id, $args );
}

add_filter( 'bbp_map_meta_caps', 'openlab_bbp_map_group_forum_meta_caps', 10, 4 );

/**
 * Force bbPress to display all forums (ie don't hide any hidden forums during bbp_has_forums() queries).
 *
 * We manage visibility ourselves.
 *
 * See #1299.
 */
add_filter( 'bbp_include_all_forums', '__return_true' );

/**
 * Force bbp_has_forums() to show all post statuses.
 *
 * As above, I have no idea why bbPress makes some items hidden, but it appears
 * incompatible with BuddyPress groups.
 */
function openlab_bbp_force_all_forum_statuses( $r ) {
	$r['post_status'] = array( bbp_get_public_status_id(), bbp_get_private_status_id(), bbp_get_hidden_status_id() );
	return $r;
}

add_filter( 'bbp_before_has_forums_parse_args', 'openlab_bbp_force_all_forum_statuses' );

/**
 * Ensure that post results for bbPres forum queries are never marked hidden.
 *
 * Working with bbPress is really exhausting.
 */
function openlab_bbp_force_forums_to_public( $posts, $query ) {
	if ( ! function_exists( 'bp_is_group' ) || ! bp_is_group() ) {
		return $posts;
	}
	if ( 'forum' !== $query->get( 'post_type' ) ) {
		return $posts;
	}
	foreach ( $posts as &$post ) {
		$post->post_status = 'publish';
	}
	return $posts;
}

add_filter( 'posts_results', 'openlab_bbp_force_forums_to_public', 10, 2 );

/**
 * Force site public to 1 for bbPress.
 *
 * Otherwise activity is not posted.
 *
 * @param mixed $is_public Value from bbp_is_site_public filter.
 * @param int   $site_id   ID of the site.
 * @return mixed
 */
function openlab_bbp_force_site_public_to_1( $is_public, $site_id ) {
	if ( bp_get_root_blog_id() === (int) $site_id ) {
		$is_public = 1;
	}
	return $is_public;
}
add_filter( 'bbp_is_site_public', 'openlab_bbp_force_site_public_to_1', 10, 2 );

/**
 * Manages email notifications for group forums.
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
function openlab_forums_activity_notification_control( $send_it, $activity, $user_id, $sub ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	switch ( $activity->type ) {
		case 'bbp_topic_create':
			return openlab_notify_group_members_of_this_action() && 'no' !== $sub;

		default:
			return $send_it;
	}
}
add_action( 'bp_ass_send_activity_notification_for_user', 'openlab_forums_activity_notification_control', 100, 4 );

/**
 * Handle feature toggling for groups.
 */
function openlab_group_feature_toggle( $group_id ) {
	// Bail if in admin as these toggles are only available on the frontend.
	if ( is_admin() ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing
	// Discussion.
	$enable_forum        = ! empty( $_POST['openlab-edit-group-forum'] );
	$group               = groups_get_group( $group_id );
	$group->enable_forum = $enable_forum;
	$group->save();

	if ( $enable_forum ) {
		groups_delete_groupmeta( $group_id, 'openlab_disable_forum' );
	} else {
		groups_update_groupmeta( $group_id, 'openlab_disable_forum', '1' );
	}

	// Docs.
	$enable_docs                   = ! empty( $_POST['openlab-edit-group-docs'] );
	$docs_settings                 = bp_docs_get_group_settings( $group_id );
	$docs_settings['group-enable'] = (int) $enable_docs;
	groups_update_groupmeta( $group_id, 'bp-docs', $docs_settings );

	// Files.
	$enable_files = ! empty( $_POST['openlab-edit-group-files'] );
	if ( $enable_files ) {
		groups_delete_groupmeta( $group_id, 'group_documents_documents_disabled' );
	} else {
		groups_update_groupmeta( $group_id, 'group_documents_documents_disabled', 1 );
	}

	// Calendar.
	$enable_calendar = ! empty( $_POST['openlab-edit-group-calendar'] );
	if ( $enable_calendar ) {
		groups_update_groupmeta( $group_id, 'calendar_is_disabled', '0' );
	} else {
		groups_update_groupmeta( $group_id, 'calendar_is_disabled', '1' );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Missing
}
add_action( 'groups_settings_updated', 'openlab_group_feature_toggle' );

/**
 * Failsafe method for determining whether forums should be enabled for a group.
 *
 * Another kewl hack due to issues with bbPress. It should be possible to rely on the `enable_forum` group toggle to
 * determine whether the Discussion tab should be shown. But something about the combination between the old bbPress
 * and the new one means that some groups used to have an associated forum_id without having enable_forum turned on,
 * yet still expect to see the Discussion tab. Our workaround is to require the explicit presence of a 'disable' flag
 * for a group's Discussion tab to be turned off.
 */
function openlab_is_forum_enabled_for_group( $group_id = false ) {
	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	if ( ! $group_id ) {
		return false;
	}

	$disable  = (bool) groups_get_groupmeta( $group_id, 'openlab_disable_forum' );
	$forum_id = groups_get_groupmeta( $group_id, 'forum_id' );

	if ( $disable || ! $forum_id ) {
		return false;
	}

	return true;
}

/**
 * If Discussion is disabled for a group, ensure it's removed from the menu.
 *
 * Gah gah gah gah gah gah.
 */
function openlab_bbp_remove_group_nav_item() {
	if ( ! bp_is_group() ) {
		return;
	}

	if ( ! openlab_is_forum_enabled_for_group() ) {
		bp_core_remove_subnav_item( bp_get_current_group_slug(), 'forum' );
	}
}
add_action( 'bp_screens', 'openlab_bbp_remove_group_nav_item', 1 );

/**
 * Enforce group privacy settings when determining bbPress forum privacy.
 *
 * This helps ensure that activity items are marked hide_sitewide as appropriate.
 *
 * See https://bbpress.trac.wordpress.org/ticket/2782,
 * https://bbpress.trac.wordpress.org/ticket/2327,
 * http://openlab.citytech.cuny.edu/redmine/issues/1428
 */
function openlab_enforce_forum_privacy( $is_public, $forum_id ) {
	$group_ids = bbp_get_forum_group_ids( $forum_id );

	if ( ! empty( $group_ids ) ) {
		foreach ( $group_ids as $group_id ) {
			$group = groups_get_group( array( 'group_id' => $group_id ) );

			if ( 'public' !== $group->status ) {
				$is_public = false;
				break;
			}
		}
	}

	return $is_public;
}
add_filter( 'bbp_is_forum_public', 'openlab_enforce_forum_privacy', 10, 2 );

/**
 * Prevent bbPress from recounting forum topics.
 *
 * This can cause a costly tree rebuild. See bbPress #1799. See OL #1663,
 */
function openlab_prevent_bbp_recounts( $r ) {
	if ( bbp_get_group_forums_root_id() === (int) $r['forum_id'] ) {
		$r['forum_id'] = 0;
	}

	return $r;
}
add_filter( 'bbp_after_update_forum_parse_args', 'openlab_prevent_bbp_recounts' );

function openlab_prevent_bbpress_from_recalculating_group_root_reply_count( $id ) {
	$group_root      = bbp_get_group_forums_root_id();
	$group_root_post = get_post( $group_root );
	if ( ! $group_root_post ) {
		return $id;
	}

	$group_root_parent = $group_root_post->post_parent;
	if ( (int) $group_root !== (int) $id && (int) $group_root_parent !== (int) $id ) {
		return $id;
	}

	// phpcs:disable
	$db = debug_backtrace();
	// phpcs:enable
	$caller = '';
	foreach ( $db as $key => $step ) {
		if ( ! empty( $step['function'] ) && 'bbp_get_forum_id' === $step['function'] ) {
			$caller = $db[ $key + 1 ]['function'];
		}
	}

	if ( 'bbp_update_forum_reply_count' === $caller ) {
		return 0;
	}

	return $id;
}
add_filter( 'bbp_get_forum_id', 'openlab_prevent_bbpress_from_recalculating_group_root_reply_count' );

/**
 * Removes 'This forum is empty' status message.
 */
function openlab_remove_bbpress_empty_forum_description( $description, $r ) {
	$topic_count = bbp_get_forum_topic_count( $r['forum_id'], false );
	if ( ! $topic_count ) {
		return '';
	}

	return $description;
}
add_filter( 'bbp_get_single_forum_description', 'openlab_remove_bbpress_empty_forum_description', 10, 2 );

/**
 * Removes single forum title from group forum page.
 */
function openlab_remove_bbpress_forum_title( $title ) {
	if ( ! bp_is_group() || ! bp_is_current_action( 'forum' ) || bp_action_variables() ) {
		return $title;
	}

	// Pretty cool technique.
	$is_display_forums        = false;
	$is_single_forum_template = false;
	// phpcs:disable
	foreach ( debug_backtrace() as $db ) {
	// phpcs:enable
		if ( ! empty( $db['class'] ) && 'BBP_Forums_Group_Extension' === $db['class'] && ! empty( $db['function'] ) && 'display_forums' === $db['function'] ) {
			$is_display_forums = true;
		}

		if ( ! empty( $db['function'] ) && 'bbp_locate_template' === $db['function'] && ! empty( $db['args'][0][0] ) && 'content-single-forum.php' === $db['args'][0][0] ) {
			$is_single_forum_template = true;
		}
	}

	if ( $is_display_forums && ! $is_single_forum_template ) {
		return '';
	}

	return $title;
}
add_filter( 'bbp_get_forum_title', 'openlab_remove_bbpress_forum_title' );

/**
 * Filters bbPress's default search parameters.
 *
 * @param array $r
 * @return array
 */
function openlab_filter_bbpress_search_parameters( $r ) {
	if ( ! bp_is_group() ) {
		return $r;
	}

	unset( $r['post_type']['forum'] );

	if ( ! isset( $r['meta_query'] ) ) {
		$r['meta_query'] = [];
	}

	$forum_ids = bbp_get_group_forum_ids( bp_get_current_group_id() );
	if ( ! $forum_ids ) {
		$forum_ids = [ 0 ];
	}

	$r['meta_query'][] = [
		'key'     => '_bbp_forum_id',
		'value'   => $forum_ids,
		'compare' => 'IN',
	];

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$r['paged'] = ! empty( $_GET['search_paged'] ) ? (int) $_GET['search_paged'] : 1;

	return $r;
}
add_filter( 'bbp_after_has_search_results_parse_args', 'openlab_filter_bbpress_search_parameters' );

/**
 * Filters bbPress's default search pagination parameters.
 *
 * @param array $r
 * @return array
 */
function openlab_filter_bbpress_search_pagination_parameters( $r ) {
	if ( ! bp_is_group() ) {
		return $r;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( empty( $_GET['bbp_search'] ) ) {
		return $r;
	}

	$search_term  = wp_unslash( $_GET['bbp_search'] );
	$search_paged = ! empty( $_GET['search_paged'] ) ? (int) $_GET['search_paged'] : 1;
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$r['base']    = add_query_arg( 'bbp_search', $search_term, bp_get_group_permalink( groups_get_current_group() ) . 'forum/' ) . '&search_paged=%#%';
	$r['current'] = $search_paged;

	return $r;
}
add_filter( 'bbp_search_results_pagination', 'openlab_filter_bbpress_search_pagination_parameters' );

/**
 * Enable at-mention autocomplete on BuddyPress group forum textareas.
 */
function openlab_enable_mentions_in_group_forum_textareas( $retval ) {
	/*
	 * Don't run this more than once on the same page.
	 *
	 * @see bp_friends_prime_mentions_results()
	 */
	remove_filter( 'bp_activity_maybe_load_mentions_scripts', __FUNCTION__ );

	$bbpress = false;

	// Group forum reply.
	if ( bp_is_group() && 'topic' === bp_action_variable() && bp_action_variable( 1 ) ) {
		$bbpress = true;
	}

	// Group forum topic.
	if ( bp_is_group() && bp_is_current_action( 'forum' ) ) {
		$bbpress = true;
	}

	// Do stuff when on a group forum page.
	if ( true === $bbpress ) {
		$retval = true;

		// Modify TinyMCE for bbPress on BP group forums.
		add_filter( 'tiny_mce_before_init', 'openlab_group_forum_tinymce_settings', 10, 2 );
		add_action( 'wp_print_footer_scripts', 'openlab_group_forum_tinymce_footer_js' );
	}

	return $retval;
}
add_filter( 'bp_activity_maybe_load_mentions_scripts', 'openlab_enable_mentions_in_group_forum_textareas' );

/**
 * Modify TinyMCE settings on BP group forum pages.
 *
 * Currently:
 *   - Ensure at-mentions autocomplete works with TinyMCE for bbPress.
 *
 * @param array  $settings  An array with TinyMCE config.
 * @param string $editor_id Unique editor identifier, e.g. 'content'.
 * @return array $mceInit   An array with TinyMCE config.
 */
function openlab_group_forum_tinymce_settings( $settings, $editor_id ) {
	if ( 'bbp_topic_content' === $editor_id || 'bbp_reply_content' === $editor_id ) {
		// Enable BuddyPress' at-mentions autocomplete JS.
		$settings['init_instance_callback'] = 'OpenLabbbPtinyMCEinit';
	}

	return $settings;
}

/**
 * Footer inline JS for bbPress.
 *
 * - Fixes atwho.js markup when using the Visual Editor.
 *
 * @see cac_group_forum_tinymce_settings()
 * @see https://redmine.gc.cuny.edu/issues/12675
 */
function openlab_group_forum_tinymce_footer_js() {
	$js = <<<JS
	<script>
		function OpenLabbbPtinyMCEinit(editor) {
			// Same as window.bp.mentions.tinyMCEinit.
			jQuery( window.tinyMCE.activeEditor.contentDocument.activeElement )
				.atwho( 'setIframe', jQuery( '.wp-editor-wrap iframe' )[0] )
				.bp_mentions( bp.mentions.users );
			// Here's our addition to remove atwho's <span> element.
			editor.on('NodeChange', function(e) {
				var bbpContent, atwho, i, item;
				if ( -1 === e.element.innerText.indexOf('@') ) {
					return;
				}
				bbpContent = jQuery('<div>' + editor.getContent() + '</div>');
				atwho = bbpContent.find('span.atwho-inserted');
				if ( 0 === atwho.length ) {
					return;
				}
				for ( i = 0; i < atwho.length; ++i) {
					item = atwho[i];
					jQuery(item).replaceWith(item.innerText);
				}
				// Set edited contents.
				editor.setContent( bbpContent.html() );
				// After editing, set the cursor to the end.
				editor.selection.select(editor.getBody(), true);
				editor.selection.collapse(false);
			});
		}
	</script>
JS;

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $js;
}

/**
 * Plugin: Social
 */

/**
 * Don't let users logged into an account created by Social remain logged in
 *
 * See #3476
 */
function openlab_log_out_social_accounts() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$user_id = get_current_user_id();
	$social  = get_user_meta( $user_id, 'social_commenter', true );

	if ( 'true' === $social ) {
		// Make sure there's no last_activity, so the user doesn't show in directories.
		BP_Core_User::delete_last_activity( $user_id );

		// Mark the user as spam, so the profile can't be viewed directly.
		global $wpdb;
		$wpdb->update( $wpdb->users, array( 'status' => 1 ), array( 'ID' => $user_id ) );

		$user = new WP_User( $user_id );
		clean_user_cache( $user );

		// Log out and redirect.
		wp_clear_auth_cookie();
		wp_safe_redirect( '/' );
		die();
	}
}

add_action( 'init', 'openlab_log_out_social_accounts', 0 );

/**
 * Plugin: Braille
 */

/**
 * Remove bp-braille native settings UI.
 */
add_action(
	'bp_init',
	function () {
		if ( ! class_exists( '\HardG\BpBraille\Plugin' ) ) {
			return;
		}

		$instance = \HardG\BpBraille\Plugin::get_instance();

		if ( isset( $instance->messages->settings ) ) {
			remove_action( 'bp_core_general_settings_before_submit', array( $instance->messages->settings, 'render' ) );
		}

		if ( isset( $instance->groups->settings ) ) {
			remove_action( 'bp_after_group_details_admin', array( $instance->groups->settings, 'render' ) );
		}
	}
);
