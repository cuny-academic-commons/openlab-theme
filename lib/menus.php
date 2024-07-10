<?php
/*
 * menu functions - current includes
 * -register_nav_menus for custom menu locations
 * -help pages menu - adding categories
 * -profile pages sub menus
 */

// custom menu locations for OpenLab
register_nav_menus(
	array(
		'main'        => __( 'Main Menu', 'commons-in-a-box' ),
		'aboutmenu'   => __( 'About Menu', 'commons-in-a-box' ),
		'helpmenu'    => __( 'Help Menu', 'commons-in-a-box' ),
		'helpmenusec' => __( 'Help Menu Secondary', 'commons-in-a-box' ),
	)
);

/**
 * Using @wp_nav_menu_objects for fine-grained menu customizations
 *
 * @global type $post
 * @param type $items
 * @param type $args
 * @return type
 */
function openlab_wp_menu_customizations( $items, $args ) {
	global $post;

	if ( false !== strpos( $args->theme_location, 'about' ) ) {

		$calendar_page_obj = get_page_by_path( 'about/calendar' );
		$upcoming_page_obj = get_page_by_path( 'about/calendar/upcoming' );

		// default order is at the end of the current set of items
		$order     = count( $items );
		$new_items = array();

		// add a mobile verison of the OpenLab Calendar menu item
		// first iterate through the current menu items and figure out where this new mobile menu item will go
		foreach ( $items as $key => $item ) {

			if ( false === strpos( $item->url, bp_get_root_url() ) ) {
				$items[ $key ]->classes[] = 'external-link';
			}

			if ( __( 'Calendar', 'commons-in-a-box' ) === $item->title ) {

				$items[ $key ]->classes[] = 'hidden-xs';

				if ( $post->post_parent === $calendar_page_obj->ID || 'event' === $post->post_type ) {
					$items[ $key ]->classes[] = 'current-menu-item';
				}

				$order = $item->menu_order + 1;
			}

			if ( $item->menu_order >= $order ) {
				$items[ $key ]->menu_order = $item->menu_order + 1;
				$new_items[ $key + 1 ]     = $item;
			} else {
				$new_items[ $key ] = $item;
			}
		}

		// then we create the menu item and inject it into the menu items array
		$new_menu_item = openlab_custom_nav_menu_item( __( 'Calendar', 'commons-in-a-box' ), get_permalink( $upcoming_page_obj->ID ), $order, 0, array( 'visible-xs' ) );

		$new_items[ $order ] = $new_menu_item;
		ksort( $new_items );
		$items = $new_items;
	}

	return $items;
}

add_filter( 'wp_nav_menu_objects', 'openlab_wp_menu_customizations', 11, 2 );

/**
 * Reach into the item nav menu and remove stuff as necessary
 *
 * Hooked to bp_screens at 1 because apparently BP is broken??
 */
function openlab_modify_options_nav() {
	if ( bp_is_group() && ! bp_is_group_create() ) {
		$group_type = cboxol_get_group_group_type( bp_get_current_group_id() );
		if ( ! is_wp_error( $group_type ) ) {
			buddypress()->groups->nav->edit_nav(
				array(
					'name' => $group_type->get_label( 'group_home' ),
				),
				'home',
				bp_get_current_group_slug()
			);
		}

		if ( cboxol_is_portfolio() ) {
			// Keep the following tabs as-is
			$keepers   = array( 'home', 'admin', 'members' );
			$nav_items = buddypress()->groups->nav->get_secondary( array( 'parent_slug' => bp_get_current_group_slug() ) );
			foreach ( $nav_items as $nav_item ) {
				if ( ! in_array( $nav_item->slug, $keepers, true ) ) {
					buddypress()->groups->nav->delete_nav( $nav_item->slug, bp_get_current_group_slug() );
				}
			}
		}
	}

	if ( bp_is_group() && ! bp_is_group_create() ) {
		buddypress()->groups->nav->edit_nav(
			array(
				'position' => 95,
			),
			'admin',
			bp_get_current_group_slug()
		);

		buddypress()->groups->nav->edit_nav(
			array(
				'name' => 'Settings',
			),
			'admin',
			bp_get_current_group_slug()
		);

		$files_item = buddypress()->groups->nav->get_secondary(
			array(
				'slug'        => 'documents',
				'parent_slug' => bp_get_current_group_slug(),
			)
		);

		if ( $files_item ) {
			$first_files_item = reset( $files_item );
			if ( preg_match( '/<span>([0-9]+)<\/span>/', $first_files_item['name'], $matches ) ) {
				$files_name = sprintf(
					/* translators: 1. count span */
					__( 'File Library %1$s', 'commons-in-a-box' ),
					sprintf( '<span class="mol-count pull-right count-%d gray">%d</span>', $matches[1], $matches[1] )
				);
			} else {
				$files_name = __( 'File Library', 'commons-in-a-box' );
			}

			buddypress()->groups->nav->edit_nav(
				array(
					'name' => $files_name,
				),
				'documents',
				bp_get_current_group_slug()
			);
		}

		$nav_items     = buddypress()->groups->nav->get_secondary( array( 'parent_slug' => bp_get_current_group_slug() ) );
		$current_group = groups_get_current_group();

		// Docs should have count.
		$doc_item = buddypress()->groups->nav->get_secondary(
			array(
				'slug'        => 'docs',
				'parent_slug' => bp_get_current_group_slug(),
			)
		);
		if ( $doc_item ) {
			$group_doc_count = openlab_get_group_doc_count( $current_group->id );
			$docs_name       = sprintf(
				/* translators: 1. count span */
				__( 'Docs %1$s', 'commons-in-a-box' ),
				sprintf( '<span class="mol-count pull-right count-%d gray">%d</span>', $group_doc_count, $group_doc_count )
			);
			buddypress()->groups->nav->edit_nav(
				array(
					'name' => $docs_name,
				),
				'docs',
				bp_get_current_group_slug()
			);
		}

		foreach ( $nav_items as $nav_item ) {

			if ( 'events' === $nav_item->slug ) {

				$new_option_args = array(
					'name'            => $nav_item->name,
					'slug'            => $nav_item->slug . '-mobile',
					'parent_slug'     => $nav_item->parent_slug,
					'parent_url'      => trailingslashit( bp_get_group_permalink( $current_group ) ),
					'link'            => trailingslashit( $nav_item->link ) . 'upcoming/',
					'position'        => intval( $nav_item->position ) + 1,
					'item_css_id'     => $nav_item->css_id . '-mobile',
					'screen_function' => $nav_item->screen_function,
					'user_has_access' => $nav_item->user_has_access,
					'no_access_url'   => $nav_item->no_access_url,
				);

				$status = bp_core_create_subnav_link( $new_option_args, 'groups' );
			}
		}
	}
}

add_action( 'bp_screens', 'openlab_modify_options_nav', 1 );

/**
 * Help Sidebar menu: includes categories and sub-categories.
 *
 * @global type $post
 * @param string $items
 * @param type   $args
 * @return string
 */
function openlab_help_categories_menu( $items, $args ) {
	global $post;

	if ( 'helpmenu' === $args->theme_location ) {
		$term         = get_query_var( 'term' );
		$parent_term  = get_term_by( 'slug', $term, 'help_category' );
		$current_term = false;

		if ( false === $parent_term ) {
			$child_terms = get_the_terms( $post->ID, 'help_category' );
			$term        = array();

			if ( ! empty( $child_terms ) ) {
				foreach ( $child_terms as $child_term ) {
					$term[] = $child_term;
				}

				$parent_term  = get_term_by( 'id', $term[0]->parent, 'help_category' );
				$current_term = get_term_by( 'id', $term[0]->term_id, 'help_category' );
			}
		}

		// for child term archive pages
		if ( false !== $parent_term && 0 !== $parent_term->parent ) {
			$current_term = $parent_term;
			$parent_term  = get_term_by( 'id', $current_term->parent, 'help_category' );
		}

		$help_args = array(
			'taxonomy'   => 'help_category',
			'hide_empty' => false,
			'orderby'    => 'term_order',
		);

		$help_cats = get_terms( $help_args );

		// for post level identifying of current menu item
		$post_cats_array = array();

		if ( 'help' === $post->post_type ) {
			$post_cats = get_the_terms( $post->id, 'help_category' );

			if ( $post_cats ) {
				foreach ( $post_cats as $post_cat ) {
					// no children cats in menu
					if ( 0 === $post_cat->parent ) {
						$post_cats_array[] = $post_cat->term_id;
					}
				}
			}
		}

		$help_cat_list = '';
		foreach ( $help_cats as $help_cat ) {
			// eliminate children cats from the menu list
			if ( 0 === $help_cat->parent ) {
				$help_classes = 'help-cat menu-item';

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$highlight_active_state = 'help_tags' === get_query_var( 'taxonomy' ) && empty( $_GET['help-search'] );

				// see if this is the current menu item; if not, this could be a post,
				// so we'll check against an array of cat ids for this post
				if ( $highlight_active_state ) {
					if ( false !== $parent_term && $help_cat->term_id === $parent_term->term_id ) {
						$help_classes .= ' current-menu-item';
					} elseif ( 'help' === $post->post_type ) {
						if ( in_array( $help_cat->term_id, $post_cats_array, true ) ) {
							$help_classes .= ' current-menu-item';
						}
					}
				}

				// a special case just for the glossary page
				if ( 'Help Glossary' === $help_cat->name ) {
					$help_cat->name = 'Glossary';
				}

				$help_cat_list .= '<li class="' . $help_classes . '"><a href="' . get_term_link( $help_cat ) . '">' . $help_cat->name . '</a>';

				// check for child terms
				$child_cat_check = get_term_children( $help_cat->term_id, 'help_category' );

				// list child terms, if any
				if ( count( $child_cat_check ) > 0 ) {
					$help_cat_list .= '<ul>';

					$child_args = array(
						'taxonomy'   => 'help_category',
						'hide_empty' => false,
						'orderby'    => 'term_order',
						'parent'     => $help_cat->term_id,
					);

					$child_cats = get_terms( $child_args );

					foreach ( $child_cats as $child_cat ) {

						$child_classes = 'help-cat menu-item';
						if ( $highlight_active_state ) {
							if ( false !== $current_term && $child_cat->term_id === $current_term->term_id ) {
								$child_classes .= ' current-menu-item';
							} elseif ( 'help' === $post->post_type ) {
								if ( in_array( $child_cat->term_id, $post_cats_array, true ) ) {
									$child_classes .= ' current-menu-item';
								}
							}
						}

						$help_cat_list .= '<li class="' . $child_classes . '"><a href="' . get_term_link( $child_cat ) . '">' . $child_cat->name . '</a></li>';
					}

					$help_cat_list .= '</ul>';
				}

				$help_cat_list .= '</li>';
			}
		}

		$items = $items . $help_cat_list;
	}

	return $items;
}

add_filter( 'wp_nav_menu_items', 'openlab_help_categories_menu', 10, 2 );

/**
 * For a single help post: get the primary term for that post
 *
 * @global type $post
 * @return type
 */
function openlab_get_primary_help_term_name() {
	global $post;
	$child_terms = get_the_terms( $post->ID, 'help_category' );
	$term        = array();
	foreach ( $child_terms as $child_term ) {
		$term[] = $child_term;
	}

	$current_term = get_term_by( 'id', $term[0]->term_id, 'help_category' );
	return $current_term;
}

/**
 * Getting all of the submenu wrapper markup in one place
 *
 * @param type $type
 * @param type $opt_var
 * @return string
 */
function openlab_submenu_markup( $type = '', $opt_var = null, $row_wrapper = true ) {
	$submenu_text = '';

	$width = 'col-md-24';

	switch ( $type ) {
		case 'invitations':
			$submenu_text = esc_html__( 'My Invitations', 'commons-in-a-box' ) . '<span aria-hidden="true">:</span> ';
			$menu         = openlab_my_invitations_submenu();
			break;
		case 'friends':
			$friends_menu = openlab_my_friends_submenu( false );
			if ( ! $friends_menu ) {
				return '';
			}

			$menu         = $friends_menu['menu'];
			$submenu_text = $friends_menu['submenu_text'];

			$width = 'col-sm-24 has-menu-items is-mol-menu';

			break;
		case 'messages':
			$submenu_text = esc_html__( 'My Messages', 'commons-in-a-box' ) . '<span aria-hidden="true">:</span> ';
			$menu         = openlab_my_messages_submenu();
			break;
		case 'groups':
			$group_menu   = openlab_my_groups_submenu( $opt_var );
			$menu         = $group_menu['menu'];
			$submenu_text = $group_menu['submenu_text'];

			$width = 'col-sm-19 is-mol-menu';

			if ( '' !== $menu ) {
				$width .= ' has-menu-items group-item';
			}

			break;
		case 'group-files':
			// translators: aria-hidden span containing a colon.
			$submenu_text = sprintf( __( 'File Library%s', 'commons-in-a-box' ), '<span aria-hidden="true">:</span>' );
			$menu         = openlab_group_files_submenu();
			break;

		default:
			$submenu_text = esc_html__( 'My Settings', 'commons-in-a-box' ) . '<span aria-hidden="true">:</span> ';
			$menu         = openlab_profile_settings_submenu();
	}

	$extras = openlab_get_submenu_extras();

	$submenu  = '<div class="' . $width . '">';
	$submenu .= '<div class="submenu"><div class="submenu-text pull-left bold"><h2>' . $submenu_text . '</h2></div>' . $extras . $menu . '</div>';
	$submenu .= '</div>';

	if ( $row_wrapper ) {
		$submenu = '<div class="row">' . $submenu . '</div>';
	}

	return $submenu;
}

/**
 * Submenu for group File Library.
 *
 * @since 1.5.0
 *
 * @return array
 */
function openlab_group_files_submenu() {
	$base_url     = bp_get_group_permalink( groups_get_current_group() ) . BP_GROUP_DOCUMENTS_SLUG;
	$current_item = $base_url;

	$menu_list = [
		$base_url                          => __( 'All Files', 'commons-in-a-box' ),
		$base_url . '?action=add_new_file' => __( 'Add New File', 'commons-in-a-box' ),
	];

	return openlab_submenu_gen( $menu_list, false, $current_item );
}

/**
 * Extra items that need to be on the same line as the submenu
 */
function openlab_get_submenu_extras() {
	global $bp;
	$extras = '';

	if ( bp_is_current_action( 'my-friends' ) ) :
		if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) :
			$count = '<div class="pull-left">' . bp_get_members_pagination_count() . '</div>';

			$extras = <<<HTML
            <div class="pull-right">
                <div class="clearfix">
                    {$count}
                </div>
            </div>
HTML;

		endif;
	endif;

	return $extras;
}

// sub-menus for profile pages - a series of functions, but all here in one place
// sub-menu for profile pages
function openlab_profile_settings_submenu() {
	global $bp;

	$dud = bp_displayed_user_domain();
	if ( ! $dud ) {
		$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}

	$settings_slug = $dud . bp_get_settings_slug();
	$menu_list     = array(
		$dud . 'profile/edit'           => __( 'Edit Profile', 'commons-in-a-box' ),
		$dud . 'profile/change-avatar'  => __( 'Change Avatar', 'commons-in-a-box' ),
		$settings_slug                  => __( 'Account Settings', 'commons-in-a-box' ),
		$dud . 'settings/notifications' => __( 'Email Notifications', 'commons-in-a-box' ),
	);

	/** This filter is documented in /wp-content/plugins/buddypress/bp-settings/classes/class-bp-settings-component.php */
	$show_data_page = apply_filters( 'bp_settings_show_user_data_page', true );

	// Export Data - only available for BuddyPress 4.0.0.
	if ( true === $show_data_page && function_exists( 'bp_signup_requires_privacy_policy_acceptance' ) ) {
		$menu_list[ $dud . 'settings/data' ] = __( 'Export Data', 'commons-in-a-box' );
	}

	if ( ! is_super_admin( bp_displayed_user_id() ) && ( ( ! bp_disable_account_deletion() && bp_is_my_profile() ) || bp_current_user_can( 'delete_users' ) ) ) {

		$menu_list[ $dud . 'settings/delete-account' ] = __( 'Delete Account', 'commons-in-a-box' );
	}
	return openlab_submenu_gen( $menu_list, true );
}

/**
 * Markup for Groups submenu.
 *
 * @param \CBOX\OL\GroupType $group_type Group type object.
 * @return string
 */
function openlab_my_groups_submenu( \CBOX\OL\GroupType $group_type ) {
	global $bp;
	$menu_out  = array();
	$menu_list = array();

	$create_link = add_query_arg(
		[
			'group_type' => $group_type->get_slug(),
			'new'        => 'true',
		],
		bp_get_group_create_url( [ 'group-details' ] )
	);

	$submenu_text = $group_type->get_label( 'my_groups' );

	$show_create_link = ! $group_type->get_is_course() || cboxol_user_can_create_courses( bp_loggedin_user_id() );
	if ( $show_create_link ) {
		if ( $group_type->get_can_be_cloned() ) {
			$menu_list = array(
				$create_link => __( 'Create / Clone', 'commons-in-a-box' ),
			);
		} else {
			$menu_list = array(
				$create_link => __( 'Create New', 'commons-in-a-box' ),
			);
		}
	}

	$menu_out['menu']         = openlab_submenu_gen( $menu_list );
	$menu_out['submenu_text'] = $submenu_text;

	return $menu_out;
}

/**
 * Get the group creation menu corresponding to a group type.
 *
 * @param \CBOX\OL\GroupType $group_type
 */
function openlab_create_group_menu( \CBOX\OL\GroupType $group_type ) {
	global $bp;

	switch ( bp_get_groups_current_create_step() ) {
		case 'site-details':
			$step_name = __( 'Step Two: Associated Site Creation', 'commons-in-a-box' );
			break;
		case 'invite-anyone':
			// translators: Group creation step name
			$step_name = sprintf( __( 'Step Three: %s', 'commons-in-a-box' ), esc_html( $group_type->get_label( 'invite_members_to_group' ) ) );
			break;
		case 'group-details':
		default:
			// translators: Group creation step name
			$step_name = sprintf( __( 'Step One: %s', 'commons-in-a-box' ), esc_html( $group_type->get_label( 'item_creation' ) ) );
			break;
	}

	$step_name = esc_html( $step_name );

	$menu_mup = <<<HTML
		<div class="submenu create-group-submenu">
			<ul class="nav nav-inline">
			<li class="submenu-item item-create-clone-a-course current-menu-item bold">{$step_name}</li>
			</ul>
		</div>
HTML;

	return $menu_mup;
}

// sub-menus for my-friends pages
function openlab_my_friends_submenu( $count = true ) {
	global $bp;
	$menu_out = array();

	$dud = bp_displayed_user_domain();
	if ( ! $dud ) {
		$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}

	$request_ids   = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
	$request_count = intval( count( (array) $request_ids ) );

	$my_friends      = $dud . 'friends/';
	$friend_requests = $dud . 'friends/requests/';

	$action    = $bp->current_action;
	$item      = $bp->current_item;
	$component = $bp->current_component;

	$count_span = '';
	if ( $count ) {
		$count_span = openlab_get_menu_count_mup( $count );
	}

	if ( $bp->is_item_admin ) {
		$menu_list = array(
			$friend_requests => __( 'Requests Received', 'commons-in-a-box' ) . ' ' . $count_span,
		);
	} else {
		return '';
	}

	$submenu_class = 'no-deco';

	if ( 'my-friends' !== $action ) {
		$submenu_class = 'display-as-menu-item';
	}

	$menu_out['menu'] = openlab_submenu_gen( $menu_list );

	// translators: display name of user
	$label                    = bp_is_my_profile() ? __( 'My Friends', 'commons-in-a-box' ) : sprintf( __( '%s\'s Friends', 'commons-in-a-box' ), bp_core_get_user_displayname( bp_displayed_user_id() ) );
	$menu_out['submenu_text'] = '<a class="' . esc_attr( $submenu_class ) . '" href="' . esc_url( $my_friends ) . '">' . esc_html( $label ) . '</a>';

	return $menu_out;
}

// sub-menus for my-messages pages
function openlab_my_messages_submenu() {
	$dud = bp_displayed_user_domain();
	if ( ! $dud ) {
		$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}

	$menu_list = array(
		$dud . 'messages/inbox/'   => __( 'Inbox', 'commons-in-a-box' ),
		$dud . 'messages/sentbox/' => __( 'Sent', 'commons-in-a-box' ),
		$dud . 'messages/compose'  => __( 'Compose', 'commons-in-a-box' ),
	);
	return openlab_submenu_gen( $menu_list );
}

// sub-menus for my-invites pages
function openlab_my_invitations_submenu() {
	$dud = bp_displayed_user_domain();
	if ( ! $dud ) {
		$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}

	$menu_list = array(
		$dud . 'groups/invites/'             => __( 'Invitations Received', 'commons-in-a-box' ),
		$dud . 'invite-anyone/'              => __( 'Invite New Members', 'commons-in-a-box' ),
		$dud . 'invite-anyone/sent-invites/' => __( 'Sent Invitations', 'commons-in-a-box' ),
	);
	return openlab_submenu_gen( $menu_list );
}

function openlab_submenu_gen( $items, $timestamp = false ) {
	global $bp, $post;

	if ( empty( $items ) ) {
		return '';
	}

	// get $items length so we know how many menu items there are ( for tagging the "last-item" class )
	$item_count = count( $items );

	// determining if this is the current page or not - checks to see if this is an action page first; if not, checks the component of the page
	$action    = $bp->current_action;
	$component = $bp->current_component;
	$page_slug = $post->post_name;

	if ( $action ) {
		$page_identify = $action;
	} elseif ( $component ) {
		$page_identify = $component;
	} elseif ( $page_slug ) {
		$page_identify = $page_slug;
	}

	// counter
	$i = 1;

	$submenu_classes = 'nav nav-inline';

	$submenu = '<ul class="' . $submenu_classes . '"><!--';

	foreach ( $items as $item => $title ) {
		$slug = strtolower( $title );
		$slug = preg_replace( '/[^A-Za-z0-9-]+/', '-', $slug );
		// class variable for each item
		$item_classes = 'submenu-item item-' . $slug;

		// now search the slug for this item to see if the page identifier is there - if it is, this is the current page
		$current_check = false;

		if ( $page_identify ) {
			$current_check = strpos( $item, $page_identify );
		}

		// special case for send invitations page hitting the same time as invitations received
		if ( 'invites' === $page_identify && __( 'Sent Invitations', 'commons-in-a-box' ) === $title ) {
			$current_check = false;
		}

		// Another special case for /documents/ - 'Add New File' doesn't have its own slug.
		if ( BP_GROUP_DOCUMENTS_SLUG === $page_identify ) {
			$is_add_new = false;

			$url_query = wp_parse_url( $item, PHP_URL_QUERY );
			if ( $url_query ) {
				parse_str( $url_query, $query_parts );
				$is_add_new = isset( $query_parts['action'] ) && 'add_new_file' === $query_parts['action'];
			}

			// We always add the class dynamically in JS.
			if ( $is_add_new ) {
				$current_check = false;
			}
		}

		// adding the current-menu-item class - also includes special cases, parsed out to make them easier to identify
		if ( false !== $current_check ) {
			$item_classes .= ' current-menu-item';
		} elseif ( 'general' === $page_identify && __( 'Account Settings', 'commons-in-a-box' ) === $title ) {
			// special case just for account settings page
			$item_classes .= ' current-menu-item';
		} elseif ( 'my-friends' === $page_identify && __( 'My Friends', 'commons-in-a-box' ) === $title ) {
			// special case just for my friends page
			$item_classes .= ' current-menu-item bold';
		} elseif ( 'invite-new-members' === $page_identify && __( 'Invite New Members', 'commons-in-a-box' ) === $title ) {
			// special case just for Invite New Members page
			$item_classes .= ' current-menu-item';
		}

		// checks to see if this is the last item or first item
		if ( $item_count === $i ) {
			$item_classes .= ' last-item';
		} elseif ( 1 === $i ) {
			$item_classes .= ' first-item';
		}

		// this is just to make styling the "delete" and "create" buttons easier
		// also added a class for the "no-link" submenu items that indicate the step in group creation
		if ( strpos( $item_classes, 'delete' ) ) {
			$item_classes .= ' delete-button';
		} elseif ( strpos( $item_classes, 'create' ) ) {
			$item_classes .= ' create-button';
		} elseif ( 'no-link' === $item ) {
			$item_classes .= ' no-link';
		}

		$submenu .= '--><li class="' . esc_attr( $item_classes ) . '">';

		// for delete
		$submenu .= ( strstr( $slug, 'delete-' ) > -1 ? '<span class="fa fa-minus-circle"></span>' : '' );
		$submenu .= ( strstr( $slug, 'create-' ) > -1 ? '<span class="fa fa-plus-circle"></span>' : '' );

		$submenu .= 'no-link' === $item ? '' : '<a href="' . esc_attr( $item ) . '">';
		$submenu .= esc_html( $title );
		$submenu .= 'no-link' === $item ? '' : '</a>';
		$submenu .= '</li><!--';

		// increment counter
		++$i;
	}

	if ( $timestamp ) {
		$submenu .= '--><li class="info-line pull-right visible-lg"><span class="timestamp info-line-timestamp">' . bp_get_last_activity( bp_displayed_user_id() ) . '</span></li><!--';
	}

	$submenu .= '--></ul>';

	return $submenu;
}

/**
 * bp_get_options_nav filtering
 */
// submenu nav renaming

function openlab_filter_subnav_home( $subnav_item ) {
	global $bp;

	$displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();

	// Intentionally use the 'buddypress' text domain.
	if ( bp_is_group() ) {
		$new_item = $subnav_item;
	} else {
		$new_item = str_replace( __( 'Home', 'buddypress' ), esc_html__( 'Profile', 'commons-in-a-box' ), $subnav_item );
	}

	// update "current" class to "current-menu-item" to unify site identification of current menu page
	$new_item = str_replace( 'current selected', 'current-menu-item', $new_item );

	// for mobile menu add course site and site dashboard (if available)
	$group_id = bp_get_current_group_id();

	$group_site_settings = openlab_get_group_site_settings( $group_id );

	$site_link = '';

	if ( ! empty( $group_site_settings['site_url'] ) && $group_site_settings['is_visible'] ) {
		$site_link = '<li id="site-groups-li" class="visible-xs"><a href="' . trailingslashit( esc_attr( $group_site_settings['site_url'] ) ) . '" id="site">' . esc_html__( 'Site', 'commons-in-a-box' ) . '</a></li>';

		if ( $group_site_settings['is_local'] && ( ( cboxol_is_portfolio() && openlab_is_my_portfolio() ) || ( ! cboxol_is_portfolio() && groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() ) ) || $bp->is_item_admin || is_super_admin() ) ) {

			$site_link .= '<li id="site-admin-groups-li" class="visible-xs"><a href="' . trailingslashit( esc_attr( $group_site_settings['site_url'] ) ) . 'wp-admin/" id="site-admin">' . esc_html__( 'Site Dashboard', 'commons-in-a-box' ) . '</a></li>';
		}
	}

	return $new_item . $site_link;
}
add_filter( 'bp_get_options_nav_home', 'openlab_filter_subnav_home' );

function openlab_filter_subnav_admin( $subnav_item ) {
	global $bp;
	$new_item = $subnav_item;
	// this is to stop the course settings menu item from getting a current class on membership pages
	if ( bp_action_variable( 0 ) ) {
		if ( bp_is_action_variable( 'manage-members', 0 ) || bp_is_action_variable( 'notifications', 0 ) || bp_is_action_variable( 'membership-requests', 0 ) ) {
			$new_item = str_replace( 'current selected', ' ', $new_item );
		} else {
			// update "current" class to "current-menu-item" to unify site identification of current menu page
			$new_item = str_replace( 'current selected', 'current-menu-item', $new_item );
		}
	}

	return $new_item;
}
add_filter( 'bp_get_options_nav_admin', 'openlab_filter_subnav_admin' );

/**
 * Modifies the 'Members' subnav item.
 *
 * - Changes the name to 'Membership'.
 * - Adds a count of total members.
 * - Adds a 'current-menu-item' class to the 'Membership' subnav item when on the 'Membership' page.
 * - Swaps URLs based on user role.
 *
 * @param string $subnav_item The subnav item.
 * @return string
 */
function openlab_filter_subnav_members( $subnav_item ) {
	global $bp;
	global $wp_query;

	// string replace menu name
	$new_item = str_replace( 'Members', 'Membership', $subnav_item );

	// switch slugs based on user role
	if ( $bp->is_item_admin ) {
		$new_item = str_replace( '/members/', '/admin/manage-members', $new_item );
	}

	$uri                 = $bp->unfiltered_uri;
	$check_uri           = array( 'groups', 'notifications' );
	$notification_status = false;
	if ( count( array_intersect( $uri, $check_uri ) ) === count( $check_uri ) ) {
		$notification_status = true;
	}

	// filtering for current status on membership menu item when in membership submenu
	if ( bp_is_action_variable( 'manage-members', 0 ) || bp_is_action_variable( 'notifications', 0 ) || bp_is_current_action( 'notifications' ) || bp_is_action_variable( 'membership-requests', 0 ) || 'invite-anyone' === $wp_query->query_vars['pagename'] || $notification_status ) {
		$new_item = str_replace( 'id="members-groups-li"', 'id="members-groups-li" class="current-menu-item"', $new_item );
	} else {
		// update "current" class to "current-menu-item" to unify site identification of current menu page
		$new_item = str_replace( 'current selected', 'current-menu-item', $new_item );
	}

	// Get a member count for formatting.
	$total_mem = (int) groups_get_groupmeta( bp_get_current_group_id(), 'total_member_count' );
	if ( ! current_user_can( 'bp_moderate' ) ) {
		$private_users = openlab_get_private_members_of_group( bp_get_current_group_id() );
		if ( $private_users ) {
			$total_mem -= count( $private_users );
		}
	}

	// Added classes to member count span.
	$member_count_formatted = bp_core_number_format( $total_mem );
	if ( $total_mem > 0 ) {
		$new_item = preg_replace( '|<span>[^<]+</span>|', '<span class="mol-count pull-right count-' . $total_mem . ' gray">' . $member_count_formatted . '</span>', $new_item );
	} else {
		$new_item = preg_replace( '|<span>[^<]+</span>|', '', $new_item );
	}

	return $new_item;
}
add_filter( 'bp_get_options_nav_members', 'openlab_filter_subnav_members' );

function openlab_filter_subnav_docs( $subnav_item ) {
	global $bp;

	// no docs if we're on the portfolio page
	if ( cboxol_is_portfolio() ) {
		return '';
	}

	$group_slug = bp_get_group_slug();

	$docs_arg = array(
		'posts_per_page' => '3',
		'post_type'      => 'bp_doc',
		'tax_query'      =>
		array(
			array(
				'taxonomy' => 'bp_docs_associated_item',
				'field'    => 'slug',
				'terms'    => $group_slug,
			),
		),
	);
	$query    = new WP_Query( $docs_arg );

	$total_doc_count = ! empty( $query->found_posts ) ? $query->found_posts : 0;

	// legacy issue - some DB entries list doc_count as greater than 0 when in fact it is 0
	// if that's the case, the search replace below will not work properly
	$doc_count = groups_get_groupmeta( $bp->groups->current_group->id, 'bp-docs-count' );

	if ( (int) $doc_count === (int) $total_doc_count ) {
		$span_count = $total_doc_count;
	} else {
		$span_count = $doc_count;
	}

	$query->reset_postdata();

	if ( $total_doc_count > 0 ) {
		$new_item = str_replace( '<span>' . $span_count . '</span>', '<span class="mol-count pull-right count-' . $total_doc_count . ' gray">' . $total_doc_count . '</span>', $subnav_item );
	} else {
		$new_item = str_replace( '<span>' . $span_count . '</span>', '', $subnav_item );
	}

	// update "current" class to "current-menu-item" to unify site identification of current menu page
	$new_item = str_replace( 'current selected', 'current-menu-item', $new_item );

	return $new_item;
}
add_filter( 'bp_get_options_nav_nav-docs', 'openlab_filter_subnav_docs' );

/**
 * Modify the Documents subnav item in group contexts.
 */
function openlab_filter_subnav_nav_group_documents( $subnav_item ) {
	if ( ! openlab_is_files_enabled_for_group( bp_get_current_group_id() ) ) {
		return '';
	}

	// no files if we're on the portfolio page
	if ( cboxol_is_portfolio() ) {
		return '';
	} else {
		// update "current" class to "current-menu-item" to unify site identification of current menu page
		$subnav_item = str_replace( 'current selected', 'current-menu-item', $subnav_item );
		return $subnav_item;
	}
}
add_filter( 'bp_get_options_nav_nav-documents', 'openlab_filter_subnav_nav_group_documents' );


add_filter( 'bp_get_options_nav_nav-forum', 'openlab_filter_subnav_forums' );

/**
 * Modify the Discussion subnav item in group contexts.
 */
function openlab_filter_subnav_forums( $subnav_item ) {
	// update "current" class to "current-menu-item" to unify site identification of current menu page
	$subnav_item = str_replace( 'current selected', 'current-menu-item', $subnav_item );
	$subnav_item = str_replace( 'Forum', 'Discussion', $subnav_item );

	// Add count.
	$count     = 0;
	$forum_ids = bbp_get_group_forum_ids( bp_get_current_group_id() );
	if ( $forum_ids ) {
		// bbPress function bbp_get_forum_topic_count is broken. @todo fix or cache.
		$topic_ids = get_posts(
			array(
				'post_type'      => bbp_get_topic_post_type(),
				'post_parent'    => $forum_ids[0],
				'fields'         => 'ids',
				'posts_per_page' => -1,
			)
		);
		$count     = count( $topic_ids );
	}

	if ( $count ) {
		$span        = sprintf( '<span class="mol-count pull-right count-%s gray">%s</span>', intval( $count ), esc_html( number_format_i18n( $count ) ) );
		$subnav_item = str_replace( '</a>', ' ' . $span . '</a>', $subnav_item );
	}

	return $subnav_item;
}

// Disable menu items.
add_filter( 'bp_get_options_nav_nav-invite-anyone', '__return_empty_string' );
add_filter( 'bp_get_options_nav_nav-notifications', '__return_empty_string' );
add_filter( 'bp_get_options_nav_request-membership', '__return_empty_string' );

add_filter( 'bp_get_options_nav_nav-events', 'openlab_filter_subnav_nav_events' );
add_filter( 'bp_get_options_nav_nav-events-mobile', 'openlab_filter_subnav_nav_events' );

function openlab_filter_subnav_nav_events( $subnav_item ) {
	$subnav_item = str_replace( 'Events', 'Calendar', $subnav_item );

	// for some reason group events page is not registering this nav element as current
	$current = '';
	if ( bp_current_action() === 'events' || bp_current_component() === 'events' ) {
		$current = ' current-menu-item';
	}

	if ( strpos( $subnav_item, 'nav-events-mobile' ) !== false ) {
		$class = "visible-xs$current";
	} else {
		$class = "hidden-xs$current";
	}

	$subnav_item = str_replace( '<li', "<li class='$class'", $subnav_item );

	return $subnav_item;
}

add_filter( 'bp_get_options_nav_calendar', 'openlab_filter_subnav_nav_calendar' );

function openlab_filter_subnav_nav_calendar( $subnav_item ) {
	$subnav_item = str_replace( 'Calendar', 'All Events', $subnav_item );

	$subnav_item = str_replace( 'current selected', 'current-menu-item', $subnav_item );

	return $subnav_item;
}

add_filter( 'bp_get_options_nav_upcoming', 'openlab_filter_subnav_nav_upcoming' );

function openlab_filter_subnav_nav_upcoming( $subnav_item ) {

	$subnav_item = str_replace( 'current selected', 'current-menu-item', $subnav_item );

	return $subnav_item;
}

add_filter( 'bp_get_options_nav_new-event', 'openlab_filter_subnav_nav_new_event' );

function openlab_filter_subnav_nav_new_event( $subnav_item ) {

	$subnav_item = str_replace( 'current selected', 'current-menu-item', $subnav_item );

	// check the group calendar access setting to see if the current user has the right privileges
	$event_create_access = openlab_get_group_event_create_access_setting( bp_get_current_group_id() );

	if ( 'admin' === $event_create_access && ! bp_is_item_admin() && ! bp_is_item_mod() ) {
		return '';
	}

	return $subnav_item;
}

// submenu navigation re-ordering
function openlab_group_submenu_nav() {
	if ( ! bp_is_group() || bp_is_group_create() ) {
		return;
	}

	$positions = array(
		'home'                  => 10,
		'nav-forum'             => 25,
		'members'               => 35,
		BP_GROUP_DOCUMENTS_SLUG => 60,
		'admin'                 => 95,
	);

	foreach ( $positions as $slug => $position ) {
		buddypress()->groups->nav->edit_nav(
			array(
				'position' => $position,
			),
			$slug,
			bp_get_current_group_slug()
		);
	}
}

add_action( 'bp_screens', 'openlab_group_submenu_nav', 1 );

/**
 * Markup for group admin tabs
 */
function openlab_group_admin_tabs( $group = false ) {
	global $bp, $groups_template;

	if ( ! $group ) {
		$group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;
	}

	$current_tab = bp_action_variable( 0 );

	$group_type = cboxol_get_group_group_type( $group->id );

	// Portfolio tabs look different from other groups
	?>
	<?php if ( cboxol_is_portfolio() ) : ?>
		<?php if ( bp_is_item_admin() || bp_is_item_mod() ) { ?>
			<li class="<?php ( 'edit-details' === $current_tab || empty( $current_tab ) ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'edit-details' ], 'manage' ) ) ); ?>"><?php echo esc_html( $group_type->get_label( 'group_details' ) ); ?></a></li>
		<?php } ?>

		<li class="<?php echo 'site-details' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'site-details' ], 'manage' ) ) ); ?>"><?php echo esc_html_x( 'Site', 'Group admin nav item', 'commons-in-a-box' ); ?></a></li>

		<li class="<?php echo 'group-settings' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'group-settings' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Settings', 'commons-in-a-box' ); ?></a></li>

		<li class="delete-button <?php echo 'delete-group' === $current_tab ? 'current-menu-item' : ''; ?>"><span class="fa fa-minus-circle"></span><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'delete-group' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Delete Portfolio', 'commons-in-a-box' ); ?></a></li>

	<?php else : ?>

		<?php if ( bp_is_item_admin() || bp_is_item_mod() ) { ?>
			<li class="<?php ( 'edit-details' === $current_tab || empty( $current_tab ) ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'edit-details' ], 'manage' ) ) ); ?>"><?php echo esc_html( $group_type->get_label( 'group_details' ) ); ?></a></li>
		<?php } ?>

		<?php
		if ( ! bp_is_item_admin() ) {
			return false;
		}
		?>

		<li class="<?php echo 'site-details' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'site-details' ], 'manage' ) ) ); ?>"><?php echo esc_html_x( 'Site', 'Group admin nav item', 'commons-in-a-box' ); ?></a></li>

		<li class="<?php echo 'group-settings' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'group-settings' ], 'manage' ) ) ); ?>"><?php esc_attr_e( 'Settings', 'commons-in-a-box' ); ?></a></li>

		<?php if ( $group_type->get_can_be_cloned() ) : ?>
			<?php
			$clone_link = add_query_arg(
				array(
					'group_type' => $group_type->get_slug(),
					'clone'      => bp_get_current_group_id(),
				),
				bp_get_groups_directory_permalink() . 'create/step/group-details/'
			);
			?>

			<li class="clone-button <?php 'clone-group' === $current_tab ? 'current-menu-item' : ''; ?>"><span class="fa fa-plus-circle"></span><a href="<?php echo esc_url( $clone_link ); ?>"><?php esc_html_e( 'Clone', 'commons-in-a-box' ); ?></a></li>
		<?php endif ?>

		<li class="delete-button last-item <?php echo 'delete-group' === $current_tab ? 'current-menu-item' : ''; ?>"><span class="fa fa-minus-circle"></span><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'delete-group' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Delete', 'commons-in-a-box' ); ?></a></li>

		<?php if ( $group_type->get_is_portfolio() ) : ?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<li class="portfolio-displayname pull-right"><span class="highlight"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?></span></li>
		<?php else : ?>
			<?php // translators: last active timestamp ?>
			<li class="info-line pull-right"><span class="timestamp info-line-timestamp visible-lg"><span class="fa fa-undo"></span> <?php echo esc_html( sprintf( __( 'active %s', 'commons-in-a-box' ), bp_get_group_last_active() ) ); ?></span></li>
		<?php endif; ?>

	<?php endif; ?>
	<?php
}

/**
 * Markup for Member Tabs
 */
function openlab_group_membership_tabs( $group = false ) {
	global $bp, $groups_template;

	if ( ! $group ) {
		$group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;
	}

	$current_tab = bp_action_variable( 0 );

	?>

	<?php if ( bp_is_item_admin() ) : ?>
		<li class="<?php echo 'manage-members' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'manage-members' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Membership', 'commons-in-a-box' ); ?></a></li>

		<?php if ( 'private' === $group->status ) : ?>
			<li class="<?php echo 'membership-requests' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'membership-requests' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Member Requests', 'commons-in-a-box' ); ?></a></li>
		<?php endif; ?>
	<?php else : ?>
		<li class="<?php echo bp_is_current_action( 'members' ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_url( $group, bp_groups_get_path_chunks( [ 'members' ] ) ) ); ?>"><?php esc_html_e( 'Membership', 'commons-in-a-box' ); ?></a></li>
	<?php endif; ?>

	<?php if ( bp_group_is_member() && invite_anyone_access_test() && openlab_is_admin_truly_member() ) : ?>
		<li class="<?php echo bp_is_current_action( 'invite-anyone' ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_url( $group, bp_groups_get_path_chunks( [ 'invite-anyone' ] ) ) ); ?>"><?php esc_html_e( 'Invite New Members', 'commons-in-a-box' ); ?></a></li>
	<?php endif; ?>

	<?php if ( bp_is_item_admin() ) : ?>
		<li class="<?php echo 'notifications' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'notifications' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Email Members', 'commons-in-a-box' ); ?></a></li>
	<?php endif; ?>

	<?php if ( bp_group_is_member() && openlab_is_admin_truly_member() ) : ?>
		<li class="<?php echo bp_is_current_action( 'notifications' ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_url( $group, bp_groups_get_path_chunks( [ 'notifications' ] ) ) ); ?>"><?php esc_html_e( 'Your Email Options', 'commons-in-a-box' ); ?></a></li>
	<?php endif; ?>

	<?php
}

/**
 * Tabs for BuddyPress Docs navigation.
 */
function openlab_docs_tabs() {
	global $bp, $groups_template;

	$group = null;
	if ( bp_is_group() ) {
		$group = groups_get_current_group();
	} elseif ( ! empty( $groups_template->group ) ) {
		$group = $groups_template->group;
	}

	$group_permalink = bp_get_group_permalink( $group );

	?>

	<li <?php echo ( 'list' === bp_docs_current_view() ? 'class="current-menu-item"' : '' ); ?> ><a href="<?php echo esc_url( $group_permalink . bp_docs_get_docs_slug() ); ?>/"><?php esc_html_e( 'View Docs', 'commons-in-a-box' ); ?></a></li>
	<?php if ( current_user_can( 'bp_docs_create' ) && current_user_can( 'bp_docs_associate_with_group', bp_get_current_group_id() ) ) : ?>
		<li <?php echo ( 'create' === bp_docs_current_view() ? 'class="current-menu-item"' : '' ); ?> ><a href="<?php echo esc_url( $group_permalink . bp_docs_get_docs_slug() ); ?>/create/"><?php esc_html_e( 'New Doc', 'commons-in-a-box' ); ?></a></li>
	<?php endif; ?>
	<?php if ( ( 'edit' === bp_docs_current_view() || 'single' === bp_docs_current_view() ) && bp_docs_is_existing_doc() ) : ?>
		<?php $doc_obj = bp_docs_get_current_doc(); ?>
		<li class="current-menu-item"><?php echo esc_html( $doc_obj->post_title ); ?></li>
	<?php endif; ?>
	<?php
}

function openlab_forum_tabs() {
	global $bp, $groups_template, $wp_query;
	$group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;
	// Load up bbPress once
	$bbp = bbpress();

	/** Query Resets ***************************************************** */
	// Forum data
	$forum_ids = bbp_get_group_forum_ids( bp_get_current_group_id() );
	$forum_id  = array_shift( $forum_ids );
	$offset    = 0;

	$bbp->current_forum_id = $forum_id;

	bbp_set_query_name( 'bbp_single_forum' );

	// Get the topic
	bbp_has_topics(
		array(
			'name'           => bp_action_variable( $offset + 1 ),
			'posts_per_page' => 1,
			'show_stickies'  => false,
		)
	);

	// Setup the topic
	while ( bbp_topics() ) {
		bbp_the_topic();
	}

	$group_forum_permalink = bp_get_group_url( $group->id, bp_groups_get_path_chunks( [ 'forum' ] ) );
	?>

	<li <?php echo ( ! bp_action_variable() ? 'class="current-menu-item"' : '' ); ?> ><a href="<?php echo esc_attr( $group_forum_permalink ); ?>"><?php esc_html_e( 'Discussion', 'commons-in-a-box' ); ?></a></li><!--
	<?php if ( bp_is_action_variable( 'topic' ) ) : ?>
		--><li class="current-menu-item hyphenate"><span><?php bbp_topic_title(); ?></span></li><!--
			<?php endif; ?>
	-->
	<?php
}

function openlab_is_create_group( $group_type ) {
	global $bp;

	if ( ! bp_is_group_create() ) {
		return false;
	}

	$return = null;

	$group_id = bp_get_current_group_id();
	if ( ! $group_id ) {
		$group_id = bp_get_new_group_id();
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$current_group_type = null;
	if ( $group_id ) {
		$current_group_type = cboxol_get_group_group_type( $group_id );
	} elseif ( isset( $_GET['group_type'] ) ) {
		$current_group_type = cboxol_get_group_type( wp_unslash( urldecode( $_GET['group_type'] ) ) );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	return $current_group_type && ! is_wp_error( $current_group_type ) && $group_type === $current_group_type->get_slug();
}

function openlab_get_group_profile_mobile_anchor_links() {
	$links    = '';
	$group_id = bp_get_current_group_id();

	// Non-public groups shouldn't show this to non-members.
	$group = groups_get_current_group();
	if ( 'public' !== $group->status && empty( $group->user_has_access ) ) {
		return $links;
	}

	$related_links = openlab_get_group_related_links( $group_id );
	if ( ! empty( $related_links ) ) {

		$heading = groups_get_groupmeta( $group_id, 'openlab_related_links_list_heading' );
		$links  .= '<li id="related-links-groups-li" class="visible-xs mobile-anchor-link"><a href="#group-related-links-sidebar-widget" id="related-links">' . esc_html( $heading ) . '</a></li>';
	}

	if ( openlab_portfolio_list_enabled_for_group() ) {
		$portfolio_data = openlab_get_group_member_portfolios( $group_id );
		if ( ! empty( $portfolio_data ) ) {
			$links .= '<li id="portfolios-groups-li" class="visible-xs mobile-anchor-link"><a href="#group-member-portfolio-sidebar-widget" id="portfolios">' . esc_html( openlab_portfolio_list_group_heading() ) . '</a></li>';
		}
	}

	return $links;
}

function openlab_calendar_submenu() {
	global $post;

	$links_out = array(
		array(
			'name'  => 'All Events',
			'slug'  => 'calendar',
			'link'  => get_site_url() . '/about/calendar/',
			'class' => 'calendar' === $post->post_name ? 'current-menu-item' : '',
		),
		array(
			'name'  => 'Upcoming',
			'slug'  => 'upcoming',
			'link'  => get_site_url() . '/about/calendar/upcoming/',
			'class' => 'upcoming' === $post->post_name ? 'current-menu-item' : '',
		),
	);

	return $links_out;
}

/**
 * Function for dynamically injection menu items
 *
 * @param type $title
 * @param type $url
 * @param type $order
 * @param type $item_parent
 * @return \stdClass
 */
function openlab_custom_nav_menu_item( $title, $url, $order, $item_parent = 0, $classes = array() ) {
	// Detect whether to set 'current' flag based on $url.
	$current = false;
	if ( ! empty( $url ) ) {
		$current = ( $url === $_SERVER['REQUEST_URI'] );
	}

	$item                   = new stdClass();
	$item->ID               = 1000000 + $order + $item_parent;
	$item->db_id            = $item->ID;
	$item->title            = $title;
	$item->url              = $url;
	$item->current          = $current;
	$item->menu_order       = $order;
	$item->menu_item_parent = $item_parent;
	$item->type             = '';
	$item->object           = '';
	$item->object_id        = '';
	$item->classes          = $classes;
	$item->target           = '';
	$item->attr_title       = '';
	$item->description      = '';
	$item->xfn              = '';
	$item->status           = '';
	return $item;
}
