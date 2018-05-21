<?php

class OpenLab_Group_Type_Widget extends WP_Widget {
	protected $group_type = null;

	public function __construct() {
		$this->default_args = array(
			'title' => '',
			'group_type' => '',
		);

		parent::__construct(
			'openlab_group_type',
			__( 'Group Type', 'openlab-theme' ),
			array(
				'description' => __( 'Displays recently active groups of a specific Group Type', 'openlab-theme' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		$r = array_merge( $this->default_args, $instance );

		if ( ! bp_is_active( 'groups' ) ) {
			return;
		}

		$i = 1;

		$type = cboxol_get_group_type( $r['group_type'] );
		if ( is_wp_error( $type ) ) {
			return;
		}

		$groups_args = array(
			'max' => 4,
			'type' => 'active',
			'user_id' => 0,
			'show_hidden' => false,
			'group_type' => $type->get_slug(),
		);

		ob_start();

		?>
		<div class="col-sm-6 activity-list <?php echo $type->get_slug(); ?>-list">
			<div class="activity-wrapper">
				<div class="title-wrapper">
					<h2 class="title activity-title"><a class="no-deco" href="<?php echo bp_get_group_type_directory_permalink( $type->get_slug() ); ?>"><?php echo esc_html( $r['title'] ); ?><span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
				</div><!--title-wrapper-->

				<?php if ( bp_has_groups( $groups_args ) ) : ?>
					<?php
					global $groups_template;
					while ( bp_groups() ) : bp_the_group();
						$group = $groups_template->group;

						$activity = stripslashes( $group->description );
						echo '<div class="box-1 row-' . $i . ' activity-item type-' . esc_attr( $type->get_slug() ) . '">';
						?>
						<div class="item-avatar">
							<a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar( array( 'item_id' => $group->id, 'object' => 'group', 'type' => 'full', 'html' => false ) ) ?>" alt="<?php echo $group->name; ?>"/></a>
						</div>
						<div class="item-content-wrapper">
							<h4 class="group-title overflow-hidden">
								<a class="no-deco truncate-on-the-fly hyphenate" href="<?php echo bp_get_group_permalink() ?>" data-basevalue="40" data-minvalue="15" data-basewidth="145"><?php echo bp_get_group_name() ?></a>
								<span class="original-copy hidden"><?php echo bp_get_group_name() ?></span>
							</h4>

							<p class="hyphenate overflow-hidden">
								<?php echo bp_create_excerpt( $activity, 150, array( 'ending' => __( '&hellip;', 'buddypress' ), 'html' => false ) ) ?>
							</p>
							<p class="see-more">
								<a class="semibold" href="<?php echo bp_get_group_permalink() ?>">See More<span class="sr-only"> <?php echo bp_get_group_name() ?></span></a>
							</p>
						</div>
					</div>
					<?php $i++; ?>
					<?php endwhile; ?>
				<?php else : ?>
					<p class="group-widget-empty"><?php esc_html_e( 'Nothing to show.', 'openlab-theme' ); ?></p>
				<?php endif; ?>
			</div>
		</div><!--activity-list-->
		<?php

		$html = ob_get_clean();

		echo $html;
	}

	public function form( $instance ) {
		$r = array_merge( $this->default_args, $instance );

		$group_types = cboxol_get_group_types();

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'openlab-theme' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $r['title'] ); ?>" style="width: 100%" /></label></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'group_type' ); ?>"><?php esc_html_e( 'Group Type:', 'openlab-theme' ); ?>
				<select class="widefat" id="<?php echo $this->get_field_id( 'group_type' ); ?>" name="<?php echo $this->get_field_name( 'group_type' ); ?>" style="width: 100%">
					<option value="" <?php selected( ! $r['group_type'] ); ?>><?php esc_html_e( '- Select Group Type -', 'openlab-theme' ); ?></option>

					<?php foreach ( $group_types as $group_type ) : ?>
						<option value="<?php echo esc_attr( $group_type->get_slug() ); ?>" <?php selected( $r['group_type'], $group_type->get_slug() ); ?>><?php echo esc_html( $group_type->get_label( 'plural' ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}
