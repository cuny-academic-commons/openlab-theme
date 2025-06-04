<?php

/**
 * Enqueues the JS for generating the expiration warning notice.
 */
function openlab_enqueue_sitewide_notice_js() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	if ( ! bp_is_root_blog() ) {
		return;
	}

	$notice_enabled        = get_theme_mod( 'sitewide_notice_toggle', false );
	$is_customizer_preview = is_customize_preview();
	if ( ! $notice_enabled && ! $is_customizer_preview ) {
		return;
	}

	$color_scheme = openlab_get_color_scheme();

	switch ( $color_scheme ) {
		case 'red' :
			$background_color = '#ff7a7a';
			$text_color       = '#000';
			break;

		case 'blue' :
			$background_color = '#8ccae4';
			$text_color       = '#333';
			break;

		case 'green' :
		default :
			$background_color = '#b6d498';
			$text_color       = '#333';
			break;
	}

	// Check whether the user has dismissed this notice.
	$last_updated           = openlab_get_sitewide_notice_last_updated();
	$user_dismissed_notices = get_user_meta( get_current_user_id(), 'sitewide_notices_dismissed', true );
	$user_has_dismissed     = is_array( $user_dismissed_notices ) && in_array( $last_updated, array_map( 'intval', $user_dismissed_notices ), true );

	wp_enqueue_script(
		'openlab-sitewide-notice',
		get_stylesheet_directory_uri() . '/js/sitewide-notice.js',
		array( 'jquery' ),
		OPENLAB_VERSION,
		true
	);

	wp_add_inline_script(
		'openlab-sitewide-notice',
		'var openlabSitewideNotice = ' . wp_json_encode(
			[
				'colors'             => [
					'background' => $background_color,
					'text'       => $text_color,
				],
				'dismissUrl'         => wp_nonce_url( admin_url( 'admin-ajax.php?action=dismiss_sitewide_notice' ), 'dismiss_sitewide_notice' ),
				'isDismissedForUser' => $user_has_dismissed,
				'noticeDismissable'  => get_theme_mod( 'sitewide_notice_dismissable_toggle' ),
				'noticeText'         => get_theme_mod( 'sitewide_notice_text' ),
				'strings'            => [
					'dismiss' => __( 'Dismiss', 'commons-in-a-box' ),
				],
			]
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'openlab_enqueue_sitewide_notice_js' );

/**
 * Gets the timestamp of the most recent notice.
 *
 * @return int
 */
function openlab_get_sitewide_notice_last_updated() {
	return (int) get_option( 'sitewide_notice_last_updated' );
}

/**
 * AJAX handler for dismissing the sitewide notice.
 */
function openlab_dismiss_sitewide_notice() {
	check_ajax_referer( 'dismiss_sitewide_notice' );

	$user_dismissed_notices = get_user_meta( get_current_user_id(), 'sitewide_notices_dismissed', true );

	if ( ! is_array( $user_dismissed_notices ) ) {
		$user_dismissed_notices = [];
	}

	$user_dismissed_notices[] = openlab_get_sitewide_notice_last_updated();

	update_user_meta( get_current_user_id(), 'sitewide_notices_dismissed', array_unique( $user_dismissed_notices ) );
}
add_action( 'wp_ajax_dismiss_sitewide_notice', 'openlab_dismiss_sitewide_notice' );

/**
 * Bump the 'last updated' option when the notice text is changed.
 */
add_action(
	'customize_save_after',
	function ( $customizer ) {
		$new_text = $customizer->get_setting( 'sitewide_notice_text' )->post_value();

		if ( ! empty( $new_text ) ) {
			update_option( 'sitewide_notice_last_updated', time() );
		}
	}
);
