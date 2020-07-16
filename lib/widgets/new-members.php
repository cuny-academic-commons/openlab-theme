<?php

/**
 * New Members widget.
 *
 * @since 1.0.0
 */
class OpenLab_NewMembers_Widget extends WP_Widget {
	protected $default_args;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->default_args = array(
			'title' => __( 'New Members', 'commons-in-a-box' ),
		);

		$widget_ops = array(
			'classname'   => 'new-members',
			'description' => __( 'Newest members of the site.', 'commons-in-a-box' ),
		);
		parent::__construct( 'openlab-new-members', __( 'New Members', 'commons-in-a-box' ), $widget_ops );
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
		$r = array_merge( $this->default_args, $instance );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'];

		echo '<h2 class="title uppercase">' . esc_html( $r['title'] ) . '</h2>';
		echo '<div class="left-block-content new-members-wrapper">'
		?>
		<div id="new-members-top-wrapper">
			<div id="new-members-text">
				<p><span class="new-member-navigation pull-right">
						<button class="prev btn btn-link">
							<i class="fa fa-chevron-circle-left" aria-hidden="true"></i><span class="sr-only"><?php esc_html_e( 'Previous New Members', 'commons-in-a-box' ); ?></span></button>
						<button class="next btn btn-link" href="#">
							<i class="fa fa-chevron-circle-right" aria-hidden="true"></i><span class="sr-only"><?php esc_html_e( 'Next New Members', 'commons-in-a-box' ); ?></span></button>
					</span>
					<?php esc_html_e( 'Browse through and say "Hello!" to the newest members of the site.', 'commons-in-a-box' ); ?></p>
			</div>
			<div class="clearfloat"></div>
		</div><!--members-top-wrapper-->
		<?php
		if ( bp_has_members( 'type=newest&max=5' ) ) :
			$avatar_args = array(
				'type'   => 'full',
				'width'  => 121,
				'height' => 121,
				'class'  => 'avatar',
				'id'     => false,
				'alt'    => __( 'Member avatar', 'buddypress' ),
			);
			echo '<div id="home-new-member-wrap"><ul>';
			while ( bp_members() ) :
				bp_the_member();
				$user_id     = bp_get_member_user_id();
				$firstname   = bp_core_get_user_displayname( $user_id );
				$user_avatar = bp_core_fetch_avatar(
					array(
						'item_id' => $user_id,
						'object'  => 'member',
						'type'    => 'full',
						'html'    => false,
					)
				);
				?>
				<li class="home-new-member">
					<div class="home-new-member-avatar">
						<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $user_avatar ); ?>" alt="<?php echo esc_html( $firstname ); ?>"/></a>
					</div>
					<div class="home-new-member-info">
						<h2 class="truncate-on-the-fly load-delay" data-basevalue="16" data-minvalue="11" data-basewidth="164"><?php echo esc_html( $firstname ); ?></h2>
						<span class="original-copy hidden"><?php echo esc_html( $firstname ); ?></span>
						<div class="registered timestamp"><?php bp_member_registered(); ?></div>
					</div>
				</li>
				<?php
			endwhile;
			echo '</ul></div>';
		endif;
		echo '</div>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'commons-in-a-box' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $r['title'] ); ?>" style="width: 100%" /></label></p>
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
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
	public function update( $new_instance, $old_instance ) {
		return array(
			'title' => sanitize_text_field( wp_strip_all_tags( $new_instance['title'] ) ),
		);
	}
}
