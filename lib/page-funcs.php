<?php
/**
 * 	Home page functionality
 */

function openlab_primary_skip_link() {
	$skip_link_out = '';

	$content_target = '#openlab-main-content';
	$content_text = 'main content';

	if ( is_user_logged_in() ) {
		$adminbar_target = '#wp-admin-bar-my-openlab';
		$adminbar_text = 'admin bar';
	} else {
		$adminbar_target = '#wp-admin-bar-bp-login';
		$adminbar_text = 'log in';
	}

	$skip_link_out = <<<HTML
            <a id="skipToContent" tabindex="0" class="sr-only sr-only-focusable skip-link" href="{$content_target}">Skip to {$content_text}</a>
            <a id="skipToAdminbar" tabindex="0" class="sr-only sr-only-focusable skip-link" href="{$adminbar_target}">Skip to {$adminbar_text}</a>
HTML;

	return $skip_link_out;
}
