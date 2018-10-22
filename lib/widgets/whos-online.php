<?php

/**
 * Who's Online widget.
 *
 * @since 1.0.0
 */
class OpenLab_WhosOnline_Widget extends WP_Widget {
	protected $default_args;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->default_args = array(
			'title' => __( 'Who\'s Online?', 'openlab-theme' ),
		);

		$widget_ops = array(
			'classname'   => 'whos-online',
			'description' => __( 'Avatars of recently active members.', 'openlab-theme' ),
		);
		parent::__construct( 'openlab-whos-online', __( 'Who\'s Online?', 'openlab-theme' ), $widget_ops );
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
		global $wpdb, $bp;
		$avatar_args = array(
			'type' => 'full',
			'width' => 45,
			'height' => 45,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Member avatar', 'buddypress' ),
		);

		$r = array_merge( $this->default_args, $instance );

		$rs = wp_cache_get( 'whos_online', 'openlab' );
		if ( ! $rs ) {
			$sql = "SELECT user_id FROM {$bp->members->table_name_last_activity} where component = 'members' AND type ='last_activity' and date_recorded >= DATE_SUB( UTC_TIMESTAMP(), INTERVAL 1 HOUR ) order by date_recorded desc limit 20";
			$rs = $wpdb->get_col( $sql );
			wp_cache_set( 'whos_online', $rs, 'openlab', 5 * 60 );
		}

		$ids = array_map( 'intval', $rs );

		echo $args['before_widget'];

		if ( $ids ) {
			$members_args = array(
				'include' => $ids,
				'type' => 'active',
			);

			$x = 0;
            ?><h2 class="title uppercase"><?php echo esc_html( $r['title'] ); ?></h2><?php
			if ( bp_has_members( $members_args ) ) :
				$x += 1;
				?>

				<div class="avatar-block left-block-content clearfix">
					<?php
					while ( bp_members() ) : bp_the_member();
						global $members_template;
						$member = $members_template->member;

						$member_type = cboxol_get_user_member_type( $member->ID );
						$member_type_label = ! is_wp_error( $member_type ) ? $member_type->get_label( 'singular' ) : '';
						?>

						<?php ?>
						<div class="cuny-member">
							<div class="item-avatar">
								<a href="<?php bp_member_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar( array( 'item_id' => $member->ID, 'object' => 'member', 'type' => 'full', 'html' => false ) ) ?>" alt="<?php echo $member->fullname; ?>"/></a>
							</div>
							<div class="cuny-member-info">
								<a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a><br />
								<?php
								do_action( 'bp_directory_members_item' );
								?><?php echo esc_html( $member_type_label ); ?>,
								<?php bp_member_last_active() ?>
							</div>
						</div>

					<?php endwhile; ?>
				</div>
				<?php
			endif;
		}

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Instance details.
	 */
	public function form( $instance ) {
		$r = array_merge( $this->default_args, $instance );

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'openlab-theme' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $r['title'] ); ?>" style="width: 100%" /></label></p>
		<?php
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
		return array(
			'title' => sanitize_text_field( strip_tags( $new_instance['title'] ) ),
		);
	}
}
