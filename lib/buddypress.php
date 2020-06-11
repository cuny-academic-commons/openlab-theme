<?php

// Enqueue BP-dependent scripts.
add_action( 'wp_enqueue_scripts', 'openlab_bp_enqueue_scripts', 20 );

// BP-specific breadcrumb overrides.
add_filter( 'openlab_page_crumb', 'openlab_page_crumb_overrides', 10 );

/**
 * Enqueue front-end script that are BP-dependent.
 *
 * @since 1.0.0
 */
function openlab_bp_enqueue_scripts() {
	$ver = openlab_get_asset_version();

	if ( bp_is_register_page() ) {
		wp_enqueue_script( 'password-strength-meter' );
	}

	if ( bp_is_group_create() || bp_is_group_admin_page() ) {
		wp_enqueue_script( 'openlab-group-create', get_template_directory_uri() . '/js/group-create.js', array( 'jquery' ), $ver, true );

		wp_localize_script(
			'openlab-group-create',
			'OLGroupCreate',
			array(
				'strings' => array(
					'externalFeedsFieldLabelComments' => __( 'Comments:', 'commons-in-a-box' ),
					'externalFeedsFieldLabelPosts'    => __( 'Posts:', 'commons-in-a-box' ),
					'externalFeedsFound'              => __( 'We found the following feed URLs for your external site, which we\'ll use to pull posts and comments into your activity stream.', 'commons-in-a-box' ),
					'externalFeedsNotFound'           => __( 'We couldn\'t find any feed URLs for your external site, which we use to pull posts and comments into your activity stream. If your site has feeds, you may enter the URLs below.', 'commons-in-a-box' ),
					'fieldCannotBeBlank'              => __( 'This field cannot be blank.', 'commons-in-a-box' ),
					'incompleteCrop'                  => __( 'Please crop your image before continuing.', 'commons-in-a-box' ),
				),
			)
		);
	}

	if ( bp_is_register_page() ) {
		wp_enqueue_script( 'openlab-registration', get_template_directory_uri() . '/js/register.js', array( 'jquery', 'parsley' ), $ver, true );

		wp_localize_script(
			'openlab-registration',
			'OLReg',
			array(
				'account_type_field'    => xprofile_get_field_id_from_name( 'Account Type ' ),
				'limited_email_domains' => get_site_option( 'limited_email_domains' ),
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				'post_data'             => $_POST,
				'strings'               => array(
					'completeSignUp'              => esc_html__( 'Complete Sign Up', 'commons-in-a-box' ),
					'dashChecking'                => esc_html__( '&mdash; Checking', 'commons-in-a-box' ),
					'dashOK'                      => esc_html__( '&mdash; OK!', 'commons-in-a-box' ),
					'enterEmailAddressToContinue' => esc_html__( 'Enter Email Address to Continue', 'commons-in-a-box' ),
					'dashInvalidCode'             => esc_html__( '&mdash; Invalid code', 'commons-in-a-box' ),
				),
			)
		);
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
function openlab_page_crumb_overrides( $crumb ) {
	global $post, $bp;

	if ( bp_is_groups_directory() ) {
		$crumb = '';

		$group_type_slug = bp_get_current_group_directory_type();
		$group_type      = cboxol_get_group_type( $group_type_slug );
		if ( ! is_wp_error( $group_type ) ) {
			$type_directory_url = bp_get_group_type_directory_permalink( $group_type->get_slug() );
			$crumb              = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $type_directory_url ),
				esc_html( $group_type->get_label( 'plural' ) )
			);
		}
	}

	if ( bp_is_group() && ! bp_is_group_create() ) {
		$crumb = '';

		$group = groups_get_current_group();

		$group_type         = cboxol_get_group_group_type( $group->id );
		$type_directory_url = '';
		if ( ! is_wp_error( $group_type ) ) {
			$type_directory_url = bp_get_group_type_directory_permalink( $group_type->get_slug() );
			if ( $type_directory_url ) {
				$crumb = '<a href="' . esc_url( $type_directory_url ) . '">' . $group_type->get_label( 'plural' ) . '</a> <span class="breadcrumb-sep">/</span> ' . esc_html( $group->name );
			}
		}
	}

	if ( bp_is_user() ) {
		$members_page_id = bp_core_get_directory_page_id( 'members' );

		$b1 = sprintf( '<a href="%s">%s</a>', bp_get_members_directory_permalink(), get_the_title( $members_page_id ) );
		$b2 = bp_core_get_user_displayname( bp_displayed_user_id() );

		$crumb = $b1 . ' <span class="breadcrumb-sep">/</span> ' . $b2;
	}

	return $crumb;
}
