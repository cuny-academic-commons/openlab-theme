<?php

/**
 * What's Happening widget.
 *
 * @since 1.0.0
 */
class OpenLab_WhatsHappening_Widget extends WP_Widget {
	protected $default_args;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->default_args = array(
			'title' => __( 'What\'s Happening?', 'openlab-theme' ),
		);

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
		$cached = wp_cache_get( 'whats_happening', 'openlab' );
		if ( $cached ) {
			return $cached;
		}

		$whats_happening_out = '';

		$tomorrow = new DateTime( 'tomorrow' );

		$activity_args = array(
			'per_page' => 10,
			'action' => openlab_whats_happening_activity_types(),
			'update_meta_cache' => false,
			'date_query' => array(
				'before' => $tomorrow->format( 'Y-m-d' ),
			),
		);

		$r = array_merge( $this->default_args, $instance );

		ob_start(); ?>

<?php echo $args['before_widget']; ?>
	<h2 class="title uppercase clearfix"><i id="refreshActivity" class="fa fa-refresh pull-right" aria-hidden="true"></i><?php echo esc_html( $r['title'] ); ?></h2>
	<div id="whatsHappening" class="left-block-content whats-happening-wrapper">
		<div class="activity-list item-list inline-element-list sidebar-sublinks">
			<?php $activities = openlab_whats_happening_activity_items(); ?>

			<?php if ( $activities ) : ?>

				<?php foreach ( $activities as $activity ) : ?>

					<div class="sidebar-block activity-block">
						<div class="activity-row clearfix">
							<div class="activity-avatar pull-left">
								<a href="<?php echo openlab_activity_group_link( $activity ) ?>"><?php echo openlab_activity_group_avatar( $activity ); ?></a>
							</div>

							<div class="activity-content overflow-hidden">
								<div class="activity-header">
									<?php echo openlab_get_custom_activity_action( $activity ); ?>
								</div>
							</div>
						</div>
					</div>

				<?php endforeach; ?>
			<?php else : ?>
				<div class="sidebar-block activity-block">
					<div class="row activity-row">
						<div class="activity-avatar col-sm-24">
							<div class="activity-header">
								<p><?php esc_html_e( 'No recent activity', 'openlab-theme' ); ?></p>
							</div>
						</div>
					</div>
				</div>
			<?php endif; /* bp_has_activites() */ ?>

		</div><!-- .activity-list -->
	</div><!-- #whatsHappening -->
<?php echo $args['after_widget']; ?>
<?php
		$whats_happening_out = ob_get_clean();

		wp_cache_set( 'whats_happening', $whats_happening_out, 'openlab', 5 * 60 );

		echo $whats_happening_out;
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
