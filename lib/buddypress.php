<?php

// Enqueue BP-dependent scripts.
add_action( 'wp_enqueue_scripts', 'openlab_bp_enqueue_scripts', 20 );

// BP-specific breadcrumb overrides.
add_filter( 'openlab_page_crumb', 'openlab_page_crumb_overrides', 10, 2 );

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
		wp_enqueue_script( 'openlab-group-create', get_template_directory_uri() . '/js/group-create.js', array( 'jquery' ) );
	}

	if ( bp_is_register_page() ) {
		wp_enqueue_script( 'openlab-registration', get_template_directory_uri() . '/js/register.js', array( 'jquery', 'parsley' ) );

		wp_localize_script('openlab-registration', 'OLReg', array(
			'account_type_field' => xprofile_get_field_id_from_name( 'Account Type ' ),
			'limited_email_domains' => get_site_option( 'limited_email_domains' ),
			'post_data' => $_POST,
			'strings' => array(
				'completeSignUp' => esc_html__( 'Complete Sign Up', 'openlab-theme' ),
				'dashChecking' => esc_html__( '&mdash; Checking', 'openlab-theme' ),
				'dashOK' => esc_html__( '&mdash; OK!', 'openlab-theme' ),
				'enterEmailAddressToContinue' => esc_html__( 'Enter Email Address to Continue', 'openlab-theme' ),
				'dashInvalidCode' => esc_html__( '&mdash; Invalid code', 'openlab-theme' ),
			),
		) );
	}
}


/**
 * Override breadcrumb info based on BuddyPress content.
 *
 * @since 1.0.0
 *
 * @param string $crumb HTML of breadcrumb.
 * @param array  $args  Argument array.
 * @return string
 */
function openlab_page_crumb_overrides( $crumb, $args ) {
	global $post, $bp;

	if ( bp_is_group() && ! bp_is_group_create() ) {

		$group_type = openlab_get_group_type();
		$crumb = '<a href="' . site_url() . '/' . $group_type . 's/">' . ucfirst( $group_type ) . 's</a> / ' . bp_get_group_name();
	}

	if ( bp_is_user() ) {

		$account_type = xprofile_get_field_data( 'Account Type', $bp->displayed_user->id );
		// @todo Account type switch does not need to be hardcoded.
		if ( 'Staff' === $account_type ) {
			$b1 = '<a href="' . site_url() . '/people/">People</a> / <a href="' . site_url() . '/people/staff/">Staff</a>';
		} elseif ( 'Faculty' === $account_type ) {
			$b1 = '<a href="' . site_url() . '/people/">People</a> / <a href="' . site_url() . '/people/faculty/">Faculty</a>';
		} elseif ( 'Student' === $account_type ) {
			$b1 = '<a href="' . site_url() . '/people/">People</a> / <a href="' . site_url() . '/people/students/">Students</a>';
		} else {
			$b1 = '<a href="' . site_url() . '/people/">People</a>';
		}
		$last_name = xprofile_get_field_data( 'Last Name', $bp->displayed_user->id );
		$b2 = ucfirst( $bp->displayed_user->fullname ); //.''.ucfirst( $last_name )

		$crumb = $b1 . ' / ' . $b2;
	}
	return $crumb;
}
