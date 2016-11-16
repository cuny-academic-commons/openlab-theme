<?php

// Enqueue BP-dependent scripts.
add_action( 'wp_enqueue_scripts', 'openlab_bp_load_scripts' );

/**
 * Enqueue front-end script that are BP-dependent.
 *
 * @since 1.0.0
 */
function openlab_bp_enqueue_scripts() {
	if ( bp_is_register_page() ) {
		wp_enqueue_script( 'password-strength-meter' );
	}

	if ( ( bp_is_group_create() && bp_is_action_variable( 'group-details', 1 ) ) ||
	     ( bp_is_group_create() && bp_is_action_variable( 'invite-anyone', 1 ) ) ||
	     ( bp_is_group_admin_page() && bp_is_action_variable( 'edit-details', 0 ) ) ) {
		wp_enqueue_script( 'openlab-group-create', get_stylesheet_directory_uri() . '/js/group-create.js', array( 'jquery' ) );
	}

	if ( bp_is_register_page() ) {
		wp_enqueue_script( 'openlab-registration', get_stylesheet_directory_uri() . '/js/register.js', array( 'jquery', 'parsley' ) );

		wp_localize_script('openlab-registration', 'OLReg', array(
			'post_data' => $_POST,
			'account_type_field' => xprofile_get_field_id_from_name( 'Account Type ' ),
		) );
	}
}
