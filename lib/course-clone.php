<?php
/**
 * Course cloning
 */

/**
 * Get "Clonable" groups for the user.
 * Usually based on the group type.
 *
 * @param array $args
 * @return array $user_groups
 */
function openlab_get_groups_owned_by_user( $args = array() ) {
	$user_groups = array(
		'groups' => array(),
		'total'  => 0,
	);

	$defaults = array(
		'show_hidden'     => true,
		'user_id'         => bp_loggedin_user_id(),
		'include'         => array(),
		'group_type'      => null,
		'clone_id'        => null,
		'per_page'        => 1000,
		'populate_extras' => false,
	);

	$r = wp_parse_args( $args, $defaults );

	$groups          = groups_get_groups( $r );
	$is_admin_of     = BP_Groups_Member::get_is_admin_of( $r['user_id'] );
	$is_admin_of_ids = wp_list_pluck( $is_admin_of['groups'], 'id' );
	$is_admin_of_ids = array_map( 'absint', $is_admin_of_ids );

	// Get only the groups user is administrator of.
	$user_groups['groups'] = array_filter(
		$groups['groups'],
		function ( $group ) use ( $is_admin_of_ids ) {
			return in_array( intval( $group->id ), $is_admin_of_ids, true );
		}
	);
	$user_groups['total']  = count( $user_groups['groups'] );

	if ( ! $r['clone_id'] ) {
		return $user_groups;
	}

	$group_id_to_clone = (int) $r['clone_id'];
	if ( ! openlab_group_can_be_cloned( $group_id_to_clone ) ) {
		return $user_groups;
	}

	// Groups with "Shared Cloning" enabled should be added to list if not present.
	$in_list = false;
	foreach ( $user_groups['groups'] as $g ) {
		if ( $group_id_to_clone === $g->id ) {
			$in_list = true;
			break;
		}
	}

	if ( ! $in_list ) {
		$user_groups['groups'][] = groups_get_group( $group_id_to_clone );
		++$user_groups['total'];
	}

	return $user_groups;
}

/**
 * Catch form submits and save to the new group
 */
function openlab_clone_create_form_catcher() {
	$new_group_id = bp_get_new_group_id();

	// phpcs:disable WordPress.Security.NonceVerification.Missing

	switch ( bp_get_groups_current_create_step() ) {
		case 'group-details':
			if ( isset( $_POST['create-or-clone'] ) && 'clone' === $_POST['create-or-clone'] ) {
				$clone_source_group_id = isset( $_POST['group-to-clone'] ) ? (int) $_POST['group-to-clone'] : 0;

				if ( ! $clone_source_group_id ) {
					return;
				}

				if ( ! openlab_user_can_clone_group( $clone_source_group_id ) ) {
					return;
				}

				// Don't do anything if this is a reprocess of an existing group.
				if ( ! empty( $_POST['existing-group-id'] ) ) {
					return;
				}

				groups_update_groupmeta( $new_group_id, 'clone_source_group_id', $clone_source_group_id );

				$change_authorship = ! empty( $_POST['change-cloned-content-attribution'] );
				groups_update_groupmeta( $new_group_id, 'change_cloned_content_attribution', $change_authorship );

				// Bust ancestor cache.
				openlab_invalidate_ancestor_clone_cache( $new_group_id );

				openlab_clone_course_group( $new_group_id, $clone_source_group_id );

				/**
				 * Fires after the initial group clone is complete.
				 *
				 * @since 1.3.0
				 *
				 * @param int $new_group_id
				 * @param int $clone_source_group_id
				 */
				do_action( 'openlab_after_group_clone', $new_group_id, $clone_source_group_id );
			}
			break;

		case 'site-details':
			$clone_source_group_id = intval( groups_get_groupmeta( $new_group_id, 'clone_source_group_id' ) );

			if ( ! $clone_source_group_id ) {
				return;
			}

			// @todo Move
			if ( isset( $_POST['new_or_old'] ) && ( 'clone' === $_POST['new_or_old'] ) && isset( $_POST['blog-id-to-clone'] ) && isset( $_POST['set-up-site-toggle'] ) ) {
				$clone_source_blog_id = cboxol_get_group_site_id( $clone_source_group_id );

				// @todo validation
				$clone_destination_path = wp_unslash( $_POST['clone-destination-path'] );
				groups_update_groupmeta( $new_group_id, 'clone_destination_path', $clone_destination_path );

				openlab_clone_course_site( $new_group_id, $clone_source_group_id, $clone_source_blog_id, $clone_destination_path );
			}

			break;
	}

	// phpcs:enable WordPress.Security.NonceVerification.Missing
}
add_action( 'groups_create_group_step_complete', 'openlab_clone_create_form_catcher' );

/** FILTERS ***********************************************************/

/**
 * No longer used.
 *
 * @deprecated 1.3.0
 */
function openlab_clone_bp_get_new_group_status( $status ) {
	return $status;
}

/**
 * AJAX handler for fetching group details
 */
function openlab_group_clone_fetch_details() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
	if ( ! openlab_user_can_clone_group( $group_id ) ) {
		$group_id = 0;
	}

	$retval = openlab_group_clone_details( $group_id );

	die( wp_json_encode( $retval ) );
}
add_action( 'wp_ajax_openlab_group_clone_fetch_details', 'openlab_group_clone_fetch_details' );

function openlab_group_clone_details( $group_id ) {
	$group_admin_ids = cboxol_get_all_group_contact_ids( $group_id );
	$is_shared_clone = ! in_array( bp_loggedin_user_id(), $group_admin_ids, true );

	$retval = array(
		'group_id'               => $group_id,
		'is_shared_clone'        => $is_shared_clone,
		'name'                   => '',
		'description'            => '',
		'status'                 => '',
		'schools'                => array(),
		'departments'            => array(),
		'course_code'            => '',
		'section_code'           => '',
		'additional_description' => '',
		'categories'             => array(),
		'site_id'                => '',
		'site_url'               => '',
		'site_path'              => '',
		'term'                   => '',
		'academic_units'         => [],
		'academic_unit_types'    => [],
	);

	if ( $group_id ) {
		$group = groups_get_group( $group_id );

		$retval['name']        = $group->name;
		$retval['description'] = $group->description;
		$retval['status']      = $group->status;

		// Must be sorted so that parents come before children.
		$type_map  = [];
		$all_types = cboxol_get_academic_unit_types();

		foreach ( $all_types as $type ) {
			$parent = $type->get_parent();
			if ( ! $parent ) {
				$parent = '_null';
			}

			$type_map[ $parent ][] = $type->get_slug();
		}

		$sorted_types  = [];
		$level_parents = [ '_null' ];

		do {
			$level_children = [];
			foreach ( $level_parents as $level_parent ) {
				if ( isset( $type_map[ $level_parent ] ) ) {
					$level_children = array_merge( $level_children, $type_map[ $level_parent ] );
				}
			}

			if ( $level_children ) {
				$sorted_types = array_merge( $sorted_types, $level_children );
			}

			$level_parents = $level_children;
		} while ( $level_parents );

		$retval['academic_unit_types'] = $sorted_types;

		$academic_units = cboxol_get_object_academic_units(
			array(
				'object_id'   => $group->id,
				'object_type' => 'group',
			)
		);

		$academic_unit_data = array();
		foreach ( $academic_units as $academic_unit ) {
			$academic_unit_type = $academic_unit->get_type();
			if ( ! isset( $academic_unit_data[ $academic_unit_type ] ) ) {
				$academic_unit_data[ $academic_unit_type ] = array();
			}

			$academic_unit_data[ $academic_unit_type ][] = $academic_unit->get_slug();
		}

		$retval['academic_units'] = $academic_unit_data;

		$retval['course_code']            = esc_attr( groups_get_groupmeta( $group_id, 'cboxol_course_code' ) );
		$retval['section_code']           = esc_attr( groups_get_groupmeta( $group_id, 'cboxol_section_code' ) );
		$retval['additional_description'] = esc_attr( groups_get_groupmeta( $group_id, 'cboxol_additional_desc_html' ) );

		$retval['categories'] = wp_list_pluck( bpcgc_get_group_selected_terms( $group_id ), 'term_id' );

		$retval['site_id']   = cboxol_get_group_site_id( $group_id );
		$retval['site_url']  = get_blog_option( $retval['site_id'], 'home' );
		$retval['site_path'] = str_replace( bp_get_root_url(), '', $retval['site_url'] );

		$retval['term'] = openlab_get_group_term( $group_id );
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

/** CREATE / EDIT *************************************************************/

/**
 * Outputs the markup for the Sharing Settings panel.
 *
 * @param \CBOX\OL\GroupType $group_type Group type object.
 */
function openlab_group_sharing_settings_markup( \CBOX\OL\GroupType $group_type ) {
	$sharing_enabled = openlab_group_can_be_cloned();
	$group_label     = $group_type->get_label( 'singular' );
	?>

	<div class="panel panel-default sharing-settings-panel">
		<div class="panel-heading semibold"><?php esc_html_e( 'Sharing Settings', 'commons-in-a-box' ); ?></div>
		<div class="panel-body">
			<p><?php echo esc_html( $group_type->get_label( 'settings_help_text_sharing' ) ); ?></p>

			<div class="checkbox">
				<label><input type="checkbox" name="openlab-enable-sharing" id="openlab-enable-sharing" value="1"<?php checked( $sharing_enabled ); ?> /> <?php esc_html_e( 'Enable shared cloning', 'commons-in-a-box' ); ?></label>
			</div>
		</div>

		<?php wp_nonce_field( 'openlab_sharing_settings', 'openlab_sharing_settings_nonce', false ); ?>
	</div>

	<?php
}

/**
 * Processes Sharing Settings on create/edit.
 *
 * @param BP_Groups_Group $group Group object.
 */
function openlab_sharing_settings_save( $group ) {
	$nonce = '';

	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST['openlab_sharing_settings_nonce'] ) ) {
		$nonce = urldecode( $_POST['openlab_sharing_settings_nonce'] );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	if ( ! wp_verify_nonce( $nonce, 'openlab_sharing_settings' ) ) {
		return;
	}

	// Admins only.
	if ( ! current_user_can( 'bp_moderate' ) && ! groups_is_user_admin( bp_loggedin_user_id(), $group->id ) ) {
		return;
	}

	$enable_sharing = ! empty( $_POST['openlab-enable-sharing'] );

	if ( $enable_sharing ) {
		groups_update_groupmeta( $group->id, 'enable_sharing', 1 );

		$site_id = openlab_get_site_id_by_group_id( $group->id );
		if ( $site_id ) {
			switch_to_blog( $site_id );
			cboxol_register_clone_widgets();
			openlab_add_widget_to_main_sidebar( 'openlab_shareable_content_widget' );
			restore_current_blog();
		}
	} else {
		groups_delete_groupmeta( $group->id, 'enable_sharing' );
	}
}
add_action( 'groups_group_after_save', 'openlab_sharing_settings_save' );

/**
 * Adds 'Clone this {Group Type}' button to group profile.
 */
function openlab_add_clone_button_to_profile() {
	$group_id   = bp_get_current_group_id();
	$group_type = cboxol_get_group_group_type( $group_id );

	if ( is_wp_error( $group_type ) ) {
		return;
	}

	if ( ! openlab_user_can_clone_group( $group_id ) ) {
		return;
	}

	$clone_link = add_query_arg(
		array(
			'group_type' => $group_type->get_slug(),
			'clone'      => bp_get_current_group_id(),
		),
		bp_get_groups_directory_permalink() . 'create/step/group-details/'
	);

	?>
	<?php // translators: Group type ?>
	<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo esc_url( $clone_link ); ?>"><i class="fa fa-clone" aria-hidden="true"></i> <?php printf( esc_html__( 'Clone this %s', 'commons-in-a-box' ), esc_html( $group_type->get_label( 'singular' ) ) ); ?></a>
	<?php
}
add_action( 'bp_group_header_actions', 'openlab_add_clone_button_to_profile', 50 );

/**
 * 'descendant-of' parameter support for group directories.
 *
 * @since 1.3.0
 *
 * @param array $args
 * @return array
 */
add_filter(
	'bp_before_groups_get_groups_parse_args',
	function ( $args ) {
		$group_id = openlab_get_current_filter( 'descendant-of' );
		if ( ! $group_id ) {
			return $args;
		}

		$group = groups_get_group( $group_id );

		$exclude_hidden = ! current_user_can( 'bp_moderate' );
		$descendant_ids = openlab_get_clone_descendants_of_group( $group_id, [ $group->creator_id ], $exclude_hidden );
		if ( ! $descendant_ids ) {
			$descendant_ids = [ 0 ];
		}

		$args['include'] = $descendant_ids;

		return $args;
	}
);

/** CLASSES ******************************************************************/
require_once __DIR__ . '/class-openlab-clone-course-group.php';
require_once __DIR__ . '/class-openlab-clone-course-site.php';


