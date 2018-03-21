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

	if ( bp_is_group_create() || bp_is_group_admin_page() ) {
		wp_enqueue_script( 'openlab-group-create', get_template_directory_uri() . '/js/group-create.js', array( 'jquery' ) );

		wp_localize_script( 'openlab-group-create', 'OLGroupCreate', array(
			'strings' => array(
				'externalFeedsFieldLabelComments' => __( 'Comments:', 'openlab-theme' ),
				'externalFeedsFieldLabelPosts' => __( 'Posts:', 'openlab-theme' ),
				'externalFeedsFound' => __( 'We found the following feed URLs for your external site, which we\'ll use to pull posts and comments into your activity stream.', 'openlab-theme' ),
				'externalFeedsNotFound' => __( 'We couldn\'t find any feed URLs for your external site, which we use to pull posts and comments into your activity stream. If your site has feeds, you may enter the URLs below.', 'openlab-theme' ),
				'fieldCannotBeBlank' => __( 'This field cannot be blank.', 'openlab-theme' ),
				'incompleteCrop' => __( 'Please crop your image before continuing.', 'openlab-theme' ),
			),
		) );
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

	if ( bp_is_groups_directory() ) {
		$crumb = '';

		$group_type_slug = bp_get_current_group_directory_type();
		$group_type = cboxol_get_group_type( $group_type_slug );
		if ( ! is_wp_error( $group_type ) ) {
			$type_directory_url = bp_get_group_type_directory_permalink( $group_type->get_slug() );
			$crumb = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $type_directory_url ),
				esc_html( $group_type->get_label( 'plural' ) )
			);
		}
	}

	if ( bp_is_group() && ! bp_is_group_create() ) {
		$crumb = '';

		$group = groups_get_current_group();

		$group_type = cboxol_get_group_group_type( $group->id );
		$type_directory_url = '';
		if ( ! is_wp_error( $group_type ) ) {
			$type_directory_url = bp_get_group_type_directory_permalink( $group_type->get_slug() );
			if ( $type_directory_url ) {
				$crumb = '<a href="' . esc_url( $type_directory_url ) . '">' . $group_type->get_label( 'plural' ) . '</a> / ' . esc_html( $group->name );
			}
		}
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
