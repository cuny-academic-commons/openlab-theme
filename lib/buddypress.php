<?php

function openlab_bp_enqueue_scripts() {
	if ( bp_is_register_page() ) {
		wp_enqueue_script( 'password-strength-meter' );
	}
}
add_action( 'wp_enqueue_scripts', 'openlab_bp_load_scripts' );
