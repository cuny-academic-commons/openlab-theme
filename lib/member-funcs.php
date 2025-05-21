<?php

/**
 *     Member related functions
 */
function openlab_is_admin_truly_member( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group = & $groups_template->group;
	}

	return apply_filters( 'bp_group_is_member', ! empty( $group->is_member ) );
}

function openlab_flush_user_cache_on_save( $user_id ) {

	clean_user_cache( $user_id );
}
add_action( 'xprofile_updated_profile', 'openlab_flush_user_cache_on_save' );

/**
 * People archive page
 */
function openlab_list_members() {
	global $wpdb, $bp, $members_template, $wp_query;

	// Set up variables
	// There are two ways to specify user type: through the page name, or a URL param
	$sequence_type = '';
	$search_terms  = '';
	$user_school   = '';
	$user_dept     = '';

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	// phpcs:disable WordPress.Security.NonceVerification.Missing

	if ( ! empty( $_GET['sort'] ) ) {
		$sequence_type = $_GET['sort'];
	}

	if ( ! empty( $_POST['people_search'] ) ) {
		$search_terms = $_POST['people_search'];
	} elseif ( ! empty( $_GET['search'] ) ) {
		$search_terms = $_GET['search'];
	} elseif ( ! empty( $_POST['group_search'] ) ) {
		$search_terms = $_POST['group_search'];
	}

	// @todo 'all' needs special treatment once tax queries work without shim.
	$academic_units = array();
	foreach ( $_GET as $get_key => $get_value ) {
		if ( 'academic-unit-' !== substr( $get_key, 0, 14 ) ) {
			continue;
		}

		$academic_units[] = urldecode( wp_unslash( $get_value ) );
	}

	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	$academic_units = array_filter( $academic_units );

	if ( ! empty( $academic_units ) ) {
		$academic_units_tax_query = cboxol_get_tax_query_for_academic_units(
			array(
				'units'       => $academic_units,
				'object_type' => 'group',
			)
		);
	}

	// Set up the bp_has_members() arguments
	// Note that we're not taking user_type into account. We'll do that with a query filter
	$args = array(
		'per_page'  => 48,
		'tax_query' => array( 'relation' => 'AND' ),
	);

	if ( ! empty( $academic_units_tax_query ) ) {
		$args['tax_query']['academic_units'] = $academic_units_tax_query;
	}

	if ( $sequence_type ) {
		$args['type'] = $sequence_type;
	}

	if ( $search_terms ) {
		$args['search_terms'] = $search_terms;
	}

	$avatar_args = array(
		'type'   => 'full',
		'width'  => 72,
		'height' => 72,
		'class'  => 'avatar',
		'id'     => false,
		'alt'    => __( 'Member avatar', 'buddypress' ),
	);

	if ( bp_has_members( $args ) ) {
		?>
		<div class="row group-archive-header-row">
			<div class="current-group-filters current-portfolio-filters col-md-18 col-sm-16">
				<?php openlab_current_directory_filters(); ?>
			</div>
			<div class="col-md-6 col-sm-8 text-right"><?php cuny_members_pagination_count( 'members' ); ?></div>
		</div>

		<div id="group-members-list" class="group-list item-list row">
		<?php
		while ( bp_members() ) {
			bp_the_member();

			// the following checks the current $id agains the passed list from the query
			$member_id   = $members_template->member->id;
			$registered  = bp_format_time( strtotime( $members_template->member->user_registered ), true );
			$user_avatar = bp_core_fetch_avatar(
				array(
					'item_id' => bp_get_member_user_id(),
					'object'  => 'user',
					'type'    => 'full',
					'html'    => false,
				)
			);
			?>

			<div class="group-item col-md-8 col-xs-12">
				<div class="group-item-wrapper">
					<div class="row">
						<div class="item-avatar col-md-10 col-xs-8">
							<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $user_avatar ); ?>" alt="<?php bp_member_name(); ?>"/></a>
						</div>

						<div class="item col-md-14 col-xs-16">
							<h2 class="item-title"><a class="no-deco" href="<?php bp_member_permalink(); ?>" title="<?php bp_member_name(); ?>"><?php bp_member_name(); ?></a></h2>

							<span class="member-since-line timestamp">
								<?php
								echo esc_html(
									sprintf(
										// translators: user registration date
										__( 'Member since %s', 'commons-in-a-box' ),
										$registered
									)
								);
								?>
							</span>

							<?php if ( bp_get_member_latest_update() ) : ?>
								<span class="update"><?php bp_member_latest_update( 'length=10' ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

		<?php } // endwhile ?>
		</div>
		<div id="pag-top" class="pagination">

			<div class="pagination-links" id="member-dir-pag-top">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo openlab_members_pagination_links(); ?>
			</div>

		</div>

		<?php
	} else {
		?>
		<div class="row group-archive-header-row">
			<div class="current-group-filters current-portfolio-filters col-sm-18">
				<?php openlab_current_directory_filters(); ?>
			</div>
		</div>

		<div id="group-members-list" class="item-list group-list row">
			<div class="widget-error query-no-results col-sm-24">
				<p class="bold"><?php esc_html_e( 'There are no people to display.', 'commons-in-a-box' ); ?></p>
			</div>
		</div>

		<?php
	}
}

function openlab_members_pagination_links( $page_args = 'upage' ) {
	global $members_template;

	$pagination = paginate_links(
		array(
			'base'      => add_query_arg( $page_args, '%#%' ),
			'format'    => '',
			'total'     => ceil( (int) $members_template->total_member_count / (int) $members_template->pag_num ),
			'current'   => (int) $members_template->pag_page,
			'prev_text' => _x( '<i class="fa fa-angle-left" aria-hidden="true"></i><span class="sr-only">Previous</span>', 'Group pagination previous text', 'commons-in-a-box' ),
			'next_text' => _x( '<i class="fa fa-angle-right" aria-hidden="true"></i><span class="sr-only">Next</span>', 'Group pagination next text', 'commons-in-a-box' ),
			'mid_size'  => 3,
			'type'      => 'list',
		)
	);

	if ( $pagination ) {
		$pagination = str_replace( 'page-numbers', 'page-numbers pagination', $pagination );
	}

	return $pagination;
}

// a variation on bp_members_pagination_count() to match design
function cuny_members_pagination_count() {
	global $bp, $members_template;

	if ( empty( $members_template->type ) ) {
		$members_template->type = '';
	}

	$start_num = intval( ( $members_template->pag_page - 1 ) * $members_template->pag_num ) + 1;
	$from_num  = bp_core_number_format( $start_num );
	$to_num    = bp_core_number_format( ( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
	$total     = bp_core_number_format( $members_template->total_member_count );

	// translators: 1. Pagination start number, 2. Pagination end number, 3. Pagination total number
	$pag = sprintf( __( '%1$s to %2$s (of %3$s members)', 'buddypress' ), $from_num, $to_num, $total );
	echo esc_html( $pag );
}

function openlab_displayed_user_account_type() {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo openlab_get_displayed_user_account_type();
}

function openlab_get_displayed_user_account_type() {
	return xprofile_get_field_data( 'Account Type', bp_displayed_user_id() );
}

/**
 * Prints a status message regarding the group visibility.
 *
 * @global BP_Groups_Template $groups_template Groups template object
 * @param  object $group Group to get status message for. Optional; defaults to current group.
 */
function openlab_group_status_message( $group = null ) {
	global $groups_template;

	if ( ! $group ) {
		$group = & $groups_template->group;
	}

	$group_type = cboxol_get_group_group_type( $group->id );

	$site_id  = openlab_get_site_id_by_group_id( $group->id );
	$site_url = openlab_get_group_site_url( $group->id );

	$site_status = 1;
	if ( $site_url ) {
		// If we have a site URL but no ID, it's an external site, and is public
		if ( ! $site_id ) {
			$site_status = 1;
		} else {
			$site_status = get_blog_option( $site_id, 'blog_public' );
		}
	}

	$site_status = (float) $site_status;

	$message = '';

	switch ( $site_status ) {
		// Public
		case 1:
		case 0:
			if ( 'public' === $group->status ) {
				$message = $group_type->get_label( 'status_open' );
			} elseif ( ! $site_url ) {
				// Special case: $site_status will be 0 when the
				// group does not have an associated site. When
				// this is the case, and the group is not
				// public, don't mention anything about the Site.
				$message = $group_type->get_label( 'status_private' );
			} else {
				$message = $group_type->get_label( 'status_private_open_site' );
			}

			break;

		case -1:
			if ( 'public' === $group->status ) {
				$message = $group_type->get_label( 'status_open_community_site' );
			} else {
				$message = $group_type->get_label( 'status_private_community_site' );
			}

			break;

		case -2:
		case -3:
			if ( 'public' === $group->status ) {
				$message = $group_type->get_label( 'status_open_private_site' );
			} else {
				$message = $group_type->get_label( 'status_private_private_site' );
			}

			break;
	}

	return $message;
}

function openlab_get_groups_of_user( $args = array() ) {
	global $bp, $wpdb;

	$retval = array(
		'group_ids'     => array(),
		'group_ids_sql' => '',
		'activity'      => array(),
	);

	$defaults = array(
		'user_id'      => bp_loggedin_user_id(),
		'show_hidden'  => true,
		'group_type'   => 'club',
		'get_activity' => true,
	);
	$r        = wp_parse_args( $args, $defaults );

	$select = '';
	$where  = '';

	$select = "SELECT a.group_id FROM {$bp->groups->table_name_members} a";
	$where  = $wpdb->prepare( 'WHERE a.is_confirmed = 1 AND a.is_banned = 0 AND a.user_id = %d', $r['user_id'] );

	if ( ! $r['show_hidden'] ) {
		$select .= " JOIN {$bp->groups->table_name} c ON ( c.id = a.group_id ) ";
		$where  .= " AND c.status != 'hidden' ";
	}

	if ( 'all' !== $r['group_type'] ) {
		// Sanitize
		$group_type = in_array( strtolower( $r['group_type'] ), array( 'club', 'project', 'course' ), true ) ? strtolower( $r['group_type'] ) : 'club';

		$select .= " JOIN {$bp->groups->table_name_groupmeta} d ON ( a.group_id = d.group_id ) ";
		$where  .= $wpdb->prepare( " AND d.meta_key = 'wds_group_type' AND d.meta_value = %s ", $group_type );
	}

	$sql = $select . ' ' . $where;

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$group_ids = array_map( 'intval', $wpdb->get_col( $sql ) );

	$retval['group_ids'] = $group_ids;

	// Now that we have group ids, get the associated activity items and format the
	// whole shebang in the proper way
	if ( ! empty( $group_ids ) ) {
		$retval['group_ids_sql'] = implode( ',', $group_ids );

		if ( $r['get_activity'] ) {
			// bp_has_activities() doesn't allow arrays of item_ids, so query manually
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$activities = $wpdb->get_results( "SELECT id,item_id, content FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id IN ( {$retval['group_ids_sql']} ) ORDER BY id DESC" );

			// Now walk down the list and try to match with a group. Once one is found, remove
			// that group from the stack
			$group_activity_items = array();
			foreach ( (array) $activities as $act ) {
				if ( ! empty( $act->content ) && in_array( (int) $act->item_id, $group_ids, true ) && ! isset( $group_activity_items[ $act->item_id ] ) ) {
					$group_activity_items[ $act->item_id ] = $act->content;
					$key                                   = array_search( (int) $act->item_id, $group_ids, true );
					unset( $group_ids[ $key ] );
				}
			}

			$retval['activity'] = $group_activity_items;
		}
	}

	return $retval;
}

function openlab_profile_group_type_activity_block( \CBOX\OL\GroupType $type ) {
	global $wpdb, $bp;

	$group_args = array(
		'user_id'     => bp_displayed_user_id(),
		'show_hidden' => false,
		'group_type'  => $type->get_slug(),
		'per_page'    => 20,
	);

	$exclude_groups = [];
	$private_groups = openlab_get_user_private_memberships( bp_displayed_user_id() );

	// Exclude private groups if not current user's profile or don't have moderate access.
	if ( ! bp_is_my_profile() && ! current_user_can( 'bp_moderate' ) ) {
		$exclude_groups += $private_groups;
	}

	$group_args['exclude'] = $exclude_groups;

	$title = $type->get_label( 'plural' );

	if ( bp_has_groups( $group_args ) ) {
		?>
		<div id="<?php echo esc_attr( $type->get_slug() ); ?>-activity-stream" class="<?php echo esc_attr( $type->get_slug() ); ?>-list activity-list item-list col-sm-8 col-xs-12">
			<?php
			$href = add_query_arg( 'group_type', $type->get_slug(), bp_displayed_user_url( bp_members_get_path_chunks( [ bp_get_groups_slug() ] ) ) );
			$x    = 0;
			?>
			<?php /* @todo font awesome is loaded from openlab-toolbar.php */ ?>
			<h2 class="title activity-title"><a class="no-deco" href="<?php echo esc_attr( $href ); ?>"><?php echo esc_html( $title ); ?><span class="fa fa-chevron-circle-right font-size font-18" aria-hidden="true"></span></a></h2>

			<?php
			while ( bp_groups() ) :
				bp_the_group();

				$group_avatar = bp_core_fetch_avatar(
					array(
						'item_id' => bp_get_group_id(),
						'object'  => 'group',
						'type'    => 'full',
						'html'    => false,
					)
				);
				?>

				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="activity-avatar col-sm-10 col-xs-8">
								<a href="<?php echo esc_url( bp_get_group_url( groups_get_current_group() ) ); ?>"><img class="img-responsive" src="<?php echo esc_attr( $group_avatar ); ?>" alt="<?php echo esc_attr( bp_get_group_name() ); ?>"/></a>
							</div>

							<div class="activity-content truncate-combo col-sm-14 col-xs-16">

								<p class="overflow-hidden h6">
									<a class="font-size font-14 no-deco truncate-name truncate-on-the-fly hyphenate" href="<?php echo esc_url( bp_get_group_url( groups_get_current_group() ) ); ?>" data-basevalue="34" data-minvalue="20" data-basewidth="143" data-srprovider="true"><?php echo esc_html( bp_get_group_name() ); ?></a>
									<span class="original-copy hidden"><?php echo esc_html( bp_get_group_name() ); ?></span>
								</p>

								<?php $activity = wp_strip_all_tags( bp_get_group_description() ); ?>
								<div class="truncate-wrapper overflow-hidden">
									<p class="truncate-on-the-fly hyphenate" data-link="<?php echo esc_attr( bp_get_group_url( groups_get_current_group() ) ); ?>" data-includename="<?php echo esc_attr( bp_get_group_name() ); ?>" data-basevalue="65" data-basewidth="143"><?php echo esc_html( $activity ); ?></p>
									<p class="original-copy hidden"><?php echo esc_html( $activity ); ?></p>
								</div>

							</div>

						</div>

					</div>
				</div>

				<?php
				/* Only show 5 items max */
				++$x;
				if ( 5 === $x ) {
					break;
				}
				?>

			<?php endwhile; ?>

		</div>
		<?php
	} else {
		?>
		<div id="<?php echo esc_attr( $type->get_slug() ); ?>-activity-stream" class="<?php echo esc_attr( $type->get_slug() ); ?>-list activity-list item-list col-sm-8 col-xs-12">
			<h4><?php echo esc_html( $title ); ?></h4>

			<div class="panel panel-default">
				<div class="panel-body">
					<p><?php esc_html_e( 'None found.', 'commons-in-a-box' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}
}

function cuny_member_profile_header() {
}

function openlab_custom_add_friend_button( $button ) {

	if ( 'not_friends' === $button['id'] ) {
		$button['link_text'] = '<span class="pull-left"><i class="fa fa-user no-margin no-margin-left" aria-hidden="true"></i> ' . esc_html__( 'Add Friend', 'commons-in-a-box' ) . '</span><i class="fa fa-plus-circle pull-right no-margin no-margin-right" aria-hidden="true"></i>';
		if ( bp_is_current_action( 'my-friends' ) ) {
			$button['link_class'] = $button['link_class'] . ' btn btn-primary btn-xs link-btn clearfix';
		} else {
			$button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
		}
	} elseif ( 'pending' === $button['id'] ) {
		$button['link_text'] = '<span class="pull-left"><i class="fa fa-user no-margin no-margin-left" aria-hidden="true"></i> ' . esc_html__( 'Pending Friend', 'commons-in-a-box' ) . '</span><i class="fa fa-clock-o pull-right no-margin no-margin-right" aria-hidden="true"></i>';
		if ( bp_is_current_action( 'my-friends' ) ) {
			$button['link_class'] = $button['link_class'] . ' btn btn-primary btn-xs link-btn clearfix';
		} else {
			$button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
		}
	} else {
		$button['link_text'] = '<span class="pull-left"><i class="fa fa-user" aria-hidden="true"></i> ' . esc_html__( 'Friend', 'commons-in-a-box' ) . '</span><i class="fa fa-check-circle pull-right" aria-hidden="true"></i>';
		if ( bp_is_current_action( 'my-friends' ) ) {
			$button['link_class'] = $button['link_class'] . ' btn btn-primary btn-xs link-btn clearfix';
		} else {
			$button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
		}
	}

	return $button;
}

add_filter( 'bp_get_add_friend_button', 'openlab_custom_add_friend_button' );

function openlab_member_header() {

	$account_type = cboxol_get_user_member_type_label( bp_displayed_user_id() );

	?>

	<div class="entry-title">
		<?php // translators: Profile owner's display name ?>
		<h1 class="profile-title clearfix"><span class="profile-name"><?php echo esc_html( sprintf( __( '%s&rsquo;s Profile', 'commons-in-a-box' ), bp_get_displayed_user_fullname() ) ); ?></span></h1>

		<div class="directory-title-meta">
			<span class="profile-type pull-right hidden-xs"><?php echo esc_html( $account_type ); ?></span>
			<button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
				<span class="sr-only"><?php esc_html_e( 'Toggle navigation', 'commons-in-a-box' ); ?></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
	</div>

	<?php if ( bp_is_user_activity() ) : ?>
		<div class="clearfix hidden-xs">
			<div class="info-line pull-right"><span class="timestamp info-line-timestamp"><span class="fa fa-undo" aria-hidden="true"></span> <?php bp_last_activity( bp_displayed_user_id() ); ?></span></div>
		</div>
	<?php endif; ?>
	<div class="clearfix visible-xs">
		<span class="profile-type pull-left"><?php echo esc_html( $account_type ); ?></span>
		<div class="info-line pull-right"><span class="timestamp info-line-timestamp"><span class="fa fa-undo" aria-hidden="true"></span> <?php bp_last_activity( bp_displayed_user_id() ); ?></span></div>
	</div>
	<?php
}
add_action( 'bp_before_member_body', 'openlab_member_header' );

function openlab_messages_pagination() {
	global $messages_template;

	if ( (int) $messages_template->total_thread_count && (int) $messages_template->pag_num ) {
		$pagination = paginate_links(
			array(
				'base'      => add_query_arg(
					array(
						'mpage' => '%#%',
					)
				),
				'format'    => '',
				'total'     => ceil( (int) $messages_template->total_thread_count / (int) $messages_template->pag_num ),
				'current'   => $messages_template->pag_page,
				'prev_text' => _x( '<i class="fa fa-angle-left" aria-hidden="true"></i>', 'Group pagination previous text', 'buddypress' ),
				'next_text' => _x( '<i class="fa fa-angle-right" aria-hidden="true"></i>', 'Group pagination next text', 'buddypress' ),
				'mid_size'  => 3,
				'type'      => 'list',
			)
		);
	}

	$pagination = str_replace( 'page-numbers', 'page-numbers pagination', $pagination );

	return $pagination;
}

function openlab_get_custom_activity_action( $activity = null ) {
	global $activities_template;

	if ( null === $activity ) {
		$activity = $activities_template->activity;
	}

	// the things we do...
	$action_output     = '';
	$action_output_raw = $activity->action;
	$action_output_ary = explode( '<a', $action_output_raw );
	$count             = 0;
	foreach ( $action_output_ary as $action_redraw ) {
		if ( ! ctype_space( $action_redraw ) ) {
			$class          = 0 === $count ? 'activity-user' : 'activity-action';
			$action_output .= '<a class="' . $class . '"' . $action_redraw;
			++$count;
		}
	}

	$time_since = apply_filters_ref_array( 'bp_activity_time_since', array( '<span class="time-since">' . bp_core_time_since( $activity->date_recorded ) . '</span>', &$activity ) );

	$title  = '<p class="item inline-links semibold hyphenate">' . $action_output . '</p>';
	$title .= '<p class="item timestamp"><span class="fa fa-undo" aria-hidden="true"></span> ' . $time_since . '</p>';

	return $title;
}

function openlab_trim_member_name( $name ) {
	global $post;

	$trim_switch = false;

	if ( 'people' === $post->post_name || bp_is_members_component() ) {
		$trim_switch = true;
	}

	if ( $trim_switch ) {
		$process_name = explode( ' ', $name );
		$new_name     = '';
		foreach ( $process_name as $process ) {
			$new_name .= ' ' . bp_create_excerpt( $process, 12 );
		}

		$name = $new_name;
	}

	return $name;
}
add_filter( 'bp_member_name', 'openlab_trim_member_name' );

function openlab_trim_message_subject( $subject ) {
	if ( bp_is_messages_component() && ( bp_is_current_action( 'inbox' ) || bp_is_current_action( 'sentbox' ) ) ) {
		$subject = bp_create_excerpt( $subject, 20 );
	}

	return $subject;
}
add_filter( 'bp_get_message_thread_subject', 'openlab_trim_message_subject' );

/**
 * Get profile field markup for registration.
 */
function openlab_get_register_fields( $account_type = '', $post_data = array() ) {
	// Fake it until you make it
	if ( ! empty( $post_data ) ) {
		foreach ( $post_data as $pdk => $pdv ) {
			$_POST[ $pdk ] = $pdv;
		}
	}

	$return = '';

	if ( ! function_exists( 'bp_has_profile' ) ) {
		return;
	}

	$args = bp_xprofile_signup_args(
		[
			'member_type' => $account_type,
		]
	);

	if ( bp_has_profile( $args ) ) {
		while ( bp_profile_groups() ) {
			bp_the_profile_group();
			while ( bp_profile_fields() ) {
				bp_the_profile_field();

				$required = bp_get_the_profile_field_is_required() ? 'required' : '';
				$return  .= '<div class="editfield form-group">';

				switch ( bp_get_the_profile_field_type() ) {
					case 'textbox':
						$return .= '<label class="control-label" for="' . bp_get_the_profile_field_input_name() . '">' . bp_get_the_profile_field_name();

						if ( bp_get_the_profile_field_is_required() ) {
							$return .= ' ' . __( '(required)', 'commons-in-a-box' );
						}

						$return .= '</label>';

						$return .= '<input class="form-control" type="text" name="' . bp_get_the_profile_field_input_name() . '" id="' . bp_get_the_profile_field_input_name() . '" value="' . bp_get_the_profile_field_edit_value() . '" ' . openlab_profile_field_input_attributes() . ' ' . $required . '
						/>';
						break;

					case 'textarea':
						$return .= '<label for="' . bp_get_the_profile_field_input_name() . '">' . bp_get_the_profile_field_name();
						if ( bp_get_the_profile_field_is_required() ) :
							$return .= ' (required)';
						endif;
						$return .= '</label>';
						$return .= '<textarea class="form-control" rows="5" cols="40" name="' . bp_get_the_profile_field_input_name() . '" id="' . bp_get_the_profile_field_input_name() . '">' . bp_get_the_profile_field_edit_value();
						$return .= '</textarea>';
						break;

					case 'selectbox':
						$return .= '<label class="control-label" for="' . bp_get_the_profile_field_input_name() . '">' . bp_get_the_profile_field_name();
						if ( bp_get_the_profile_field_is_required() ) :
							$return .= ' ' . esc_html__( '(required)', 'commons-in-a-box' );
						endif;
						$return .= '</label>';

						$return .= '<select class="form-control" name="' . bp_get_the_profile_field_input_name() . '" id="' . bp_get_the_profile_field_input_name() . '" ' . openlab_profile_field_input_attributes() . ' >';
						$return .= bp_get_the_profile_field_options();
						$return .= '</select>';
						break;

					case 'multiselectbox':
						$return .= '<label for="' . bp_get_the_profile_field_input_name() . '">' . bp_get_the_profile_field_name();
						if ( bp_get_the_profile_field_is_required() ) :
							$return .= ' ' . esc_html__( '(required)', 'commons-in-a-box' );
						endif;
						$return .= '</label>';
						$return .= '<select class="form-control" name="' . bp_get_the_profile_field_input_name() . '" id="' . bp_get_the_profile_field_input_name() . '" multiple="multiple">';
						$return .= bp_get_the_profile_field_options();
						$return .= '</select>';
						break;

					case 'radio':
						$return .= '<div class="radio">';
						$return .= '<span class="label">' . bp_get_the_profile_field_name();
						if ( bp_get_the_profile_field_is_required() ) :
							$return .= ' ' . esc_html__( '(required)', 'commons-in-a-box' );
						endif;
						$return .= '</span>';
						$return .= bp_get_the_profile_field_options();
						$return .= '</div>';
						break;

					case 'checkbox':
						$return .= '<div class="checkbox">';
						$return .= '<span class="label">' . bp_get_the_profile_field_name();
						if ( bp_get_the_profile_field_is_required() ) :
							$return .= ' ' . esc_html__( '(required)', 'commons-in-a-box' );
						endif;
						$return .= '</span>';
						$return .= bp_get_the_profile_field_options();
						$return .= '</div>';
						break;

					case 'datebox':
						$return .= '<div class="datebox">';
						$return .= '<label for="' . bp_get_the_profile_field_input_name() . '_day">' . bp_get_the_profile_field_name();
						if ( bp_get_the_profile_field_is_required() ) :
							$return .= ' (required)';
						endif;
						$return .= '</label>';
						$return .= '<select name="' . bp_get_the_profile_field_input_name() . '_day" id="' . bp_get_the_profile_field_input_name() . '_day">';
						$return .= bp_get_the_profile_field_options( 'type=day' );
						$return .= '</select>';
						$return .= '<select name="' . bp_get_the_profile_field_input_name() . '_month" id="' . bp_get_the_profile_field_input_name() . '_month">';
						$return .= bp_get_the_profile_field_options( 'type=month' );
						$return .= '</select>';
						$return .= '<select name="' . bp_get_the_profile_field_input_name() . '_year" id="' . bp_get_the_profile_field_input_name() . '_year">';
						$return .= bp_get_the_profile_field_options( 'type=year' );
						$return .= '</select>';
						$return .= '</div>';
						break;
				}

				$return .= do_action( 'bp_custom_profile_edit_fields' );
				$return .= '<p class="description">' . bp_get_the_profile_field_description() . '</p>';
				$return .= '</div>';
			}

			$profile_field_ids = bp_get_the_profile_group_field_ids();

			$pfids_a = array_map( 'intval', explode( ',', $profile_field_ids ) );
			if ( ! in_array( 1, $pfids_a, true ) ) {
				$pfids_a[]         = 1;
				$profile_field_ids = implode( ',', $pfids_a );
			}

			$return .= '<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="' . $profile_field_ids . '" />';

		}
	}

	return $return;
}

/**
 * Translate 'all' member type filter.
 *
 * It means the same thing as 'no filter', but we need to differentiate in the UI.
 */
function openlab_translate_all_member_type_filter( $r ) {
	if ( 'all' === $r['member_type'] || array( 'all' ) === $r['member_type'] ) {
		$r['member_type'] = '';
	}

	return $r;
}
add_filter( 'bp_after_has_members_parse_args', 'openlab_translate_all_member_type_filter' );

/**
 * Hide profile group tabs if there's only one to show.
 */
function openlab_hide_single_profile_group_tab( $tabs ) {
	if ( 1 === count( $tabs ) ) {
		return array();
	}

	return $tabs;
}
add_filter( 'xprofile_filter_profile_group_tabs', 'openlab_hide_single_profile_group_tab' );

/**
 * When setting the 'openlab_default_avatar' theme mod, mirror to a site option.
 *
 * This site option will then be used to populate the avatar across BP.
 *
 * @param int $value Value from set_theme_mod().
 * @return int
 */
function openlab_mirror_default_avatar( $value ) {
	$src = wp_get_attachment_image_src( $value, 'full' );

	if ( ! $src ) {
		$avatar_url = '';
	} else {
		$avatar_url = $src[0];
	}

	update_site_option( 'cboxol_default_avatar_full', $avatar_url );

	$thumb_src = wp_get_attachment_image_src( $value, 'thumbnail' );

	if ( ! $thumb_src ) {
		$thumb_url = '';
	} else {
		$thumb_url = $thumb_src[0];
	}

	update_site_option( 'cboxol_default_avatar_thumb', $thumb_url );

	return $value;
}
add_filter( 'pre_set_theme_mod_openlab_default_avatar', 'openlab_mirror_default_avatar' );

/**
 * AJAX callback for toggling group membership privacy.
 *
 * @since 1.6.0
 *
 * @return void
 */
function openlab_ajax_toggle_group_membership_privacy() {
	$retval = [
		'success' => false,
		'message' => '',
	];

	// Use the group ID from the POST data.
	if ( ! isset( $_POST['group_id'] ) ) {
		$retval['message'] = __( 'Group ID is missing.', 'commons-in-a-box' );
		wp_send_json( $retval );
	}

	$user_id  = bp_loggedin_user_id();
	$group_id = (int) $_POST['group_id'];

	if ( ! wp_verify_nonce( $_POST['nonce'], 'openlab_hide_membership_' . $group_id ) ) {
		wp_send_json( $retval );
	}

	$is_private = isset( $_POST['is_private'] ) ? 'true' === $_POST['is_private'] : false;

	$updated = openlab_update_group_membership_privacy( $user_id, $group_id, $is_private );

	if ( $updated ) {
		$retval['success'] = true;
		$retval['message'] = $is_private ? __( 'User membership set to private', 'commons-in-a-box' ) : __( 'User membership set to public', 'commons-in-a-box' );
	} else {
		$retval['message'] = __( 'There was an error updating membership privacy.', 'commons-in-a-box' );
	}

	wp_send_json( $retval );
}
add_action( 'wp_ajax_openlab_update_member_group_privacy', 'openlab_ajax_toggle_group_membership_privacy' );

/**
 * AJAX callback for 'openlab_portfolio_link_visibility'.
 *
 * @since 1.6.0
 *
 * @return void
 */
function openlab_portfolio_link_visibility_ajax_cb() {
	$verified = wp_verify_nonce( $_GET['nonce'], 'openlab_portfolio_link_visibility' );

	if ( ! $verified ) {
		wp_send_json_error( 'Invalid nonce' );
	}

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( 'Not logged in' );
	}

	$enabled = 'enabled' === $_GET['state'];
	openlab_save_show_portfolio_link_on_user_profile( get_current_user_id(), $enabled );

	wp_send_json_success();
}
add_action( 'wp_ajax_openlab_portfolio_link_visibility', 'openlab_portfolio_link_visibility_ajax_cb' );
