<?php
/**
 *  Home page functionality
 */

function openlab_primary_skip_link() {
	$skip_link_out = '';

	$content_target = '#openlab-main-content';
	$content_text   = __( 'Skip to main content', 'commons-in-a-box' );

	if ( is_user_logged_in() ) {
		$adminbar_target = '#wp-admin-bar-my-openlab';
		$adminbar_text   = __( 'Skip to admin bar', 'commons-in-a-box' );
	} else {
		$adminbar_target = '#wp-admin-bar-bp-login';
		$adminbar_text   = __( 'Skip to log in', 'commons-in-a-box' );

	}

	$skip_link_out = sprintf(
		'<a id="skipToContent" tabindex="0" class="sr-only sr-only-focusable skip-link" href="%s">%s</a>
		<a id="skipToAdminbar" tabindex="0" class="sr-only sr-only-focusable skip-link" href="%s">%s</a>',
		esc_attr( $content_target ),
		esc_html( $content_text ),
		esc_attr( $adminbar_target ),
		esc_html( $adminbar_text )
	);

	return $skip_link_out;
}
