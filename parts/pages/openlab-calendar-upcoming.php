<div id="openlabUpcomingEvents" class="calendar-wrapper action-events">
	<div id="item-body">
		<div class="submenu submenu-sitewide-calendar">
			<div class="submenu-text pull-left bold">Calendar:</div>
			<ul class="nav nav-inline">
				<?php foreach ( $menu_items as $item ) : ?>
					<li class="<?php echo esc_attr( $item['class'] ); ?>" id="<?php echo esc_attr( $item['slug'] ); ?>-groups-li"><a href="<?php echo esc_attr( $item['link'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<h2><?php esc_html_e( 'Upcoming Events', 'commons-in-a-box' ); ?></h2>
		<?php if ( ! empty( $events ) ) : ?>
			<ul class="bpeo-upcoming-events">
				<?php
				foreach ( $events as $event_post ) {
					?>
					<li class="bpeo-upcoming-event-<?php echo esc_attr( $event_post->ID ); ?>">
						<div class="bpeo-upcoming-event-datetime">
							<?php // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase ?>
							<span class="bpeo-upcoming-event-date"><?php echo esc_attr( gmdate( 'M j, Y', strtotime( $event_post->StartDate ) ) ); ?></span> &middot;&nbsp; <span class="bpeo-upcoming-event-time"><?php echo esc_html( gmdate( 'g:ia', strtotime( $event_post->StartTime ) ) ); ?></span>
						</div>

						<a class="bpeo-upcoming-event-title" href="<?php echo esc_url( apply_filters( 'eventorganiser_calendar_event_link', get_permalink( $event_post->ID ), $post->ID ) ); ?>"><?php echo esc_html( $event_post->post_title ); ?></a>

					</li>
					<?php
				}
				?>
			</ul>
		<?php else : ?>
			<p><?php esc_html_e( 'No upcoming events found.', 'commons-in-a-box' ); ?></p>
		<?php endif; ?>
	</div>
</div>
