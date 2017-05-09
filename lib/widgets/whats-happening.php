<?php

/**
 * What's Happening widget.
 *
 * @since 1.0.0
 */
class OpenLab_WhatsHappening_Widget extends WP_Widget {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'whats-happening',
			'description' => __( 'A list of recent activity items from around the site.', 'openlab-theme' ),
		);
		parent::__construct( 'openlab-whats-happening', __( 'What\'s Happening', 'openlab-theme' ), $widget_ops );
	}

	/**
	 * Widget output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Array of arguments.
	 * @param array $instance Instance details.
	 */
	public function widget( $args, $instance ) {
		echo 'ok';
	}

	/**
	 * Outputs the options form on the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Instance details.
	 */
	public function form( $instance ) {
		echo 'ok';
	}

	/**
	 * Processes widget options on save.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New options.
	 * @param array $old_instance Old options.
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}
