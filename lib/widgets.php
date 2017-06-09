<?php

/**
 * Load custom widgets.
 *
 * @since 1.0.0
 */

// Register custom widgets.
add_action( 'widgets_init', 'openlab_register_widgets' );

/**
 * Register custom widgets.
 *
 * @since 1.0.0
 */
function openlab_register_widgets() {
	$widgets_dir = get_template_directory() . '/lib/widgets/';

	if ( function_exists( 'buddypress' ) ) {
		require_once( $widgets_dir . 'whats-happening.php' );
		register_widget( 'OpenLab_WhatsHappening_Widget' );

		require_once( $widgets_dir . 'whos-online.php' );
		register_widget( 'OpenLab_WhosOnline_Widget' );

		require_once( $widgets_dir . 'new-members.php' );
		register_widget( 'OpenLab_NewMembers_Widget' );

		require_once( $widgets_dir . 'group-type.php' );
		register_widget( 'OpenLab_Group_Type_Widget' );
	}
}
