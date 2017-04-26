<?php

class OpenLab_Group_Type_Widget extends WP_Widget {
	protected $group_type = null;

	public function __construct( \CBOX\OL\GroupType $group_type ) {
		$this->group_type = $group_type;

		$id = 'openlab_group_type_' . $group_type->get_slug();
		parent::__construct(
			$id,
			$group_type->get_label( 'plural' ),
			array(
				'description' => sprintf( __( 'Displays recently active groups of the "%s" type', 'openlab-theme' ), $group_type->get_name() ),
			)
		);
	}

	public function widget( $args, $instance ) {
		var_dump( $this->group_type->get_name() );
	}

	public function form( $instance ) {}

	public function update( $new_instance, $old_instance ) {}
}
