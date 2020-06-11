<div class="sidebar-widget" id="group-member-portfolio-sidebar-widget">
	<h2 class="sidebar-header"><?php esc_html_e( 'Upcoming Events', 'commons-in-a-box' ); ?></h2>

	<div class="sidebar-block">

		<ul class="group-event-activity inline-element-list group-data-list sidebar-sublinks">
			<?php if ( ! empty( $events ) ) : ?>
				<?php
				foreach ( $events as $event_post ) {
					?>
					<li class="bpeo-upcoming-event-<?php echo esc_attr( $event_post->ID ); ?>">
						<a class="bpeo-upcoming-event-title" href="<?php echo esc_url( apply_filters( 'eventorganiser_calendar_event_link', get_permalink( $event_post->ID ), $event_post->ID ) ); ?>"><?php echo esc_html( $event_post->post_title ); ?></a><br />
						<?php // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase ?>
						<span class="bpeo-upcoming-event-date"><?php echo esc_html( gmdate( 'M j, Y', strtotime( $event_post->StartDate ) ) ); ?></span>&nbsp;<span class="bpeo-upcoming-event-time"><?php echo esc_html( gmdate( 'g:ia', strtotime( $event_post->StartTime ) ) ); ?></span>
					</li>
					<?php
				}
				?>

			<?php else : ?>
				<li><?php esc_html_e( 'No upcoming events found.', 'commons-in-a-box' ); ?></li>
			<?php endif; ?>
		</ul>

	</div>
</div>
