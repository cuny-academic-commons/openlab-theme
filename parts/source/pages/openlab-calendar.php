<div class="action-events">
	<div id="item-body">
        <div class="submenu submenu-sitewide-calendar">
            <div class="submenu-text pull-left bold"><?php esc_html_e( 'Calendar:', 'openlab-theme' ); ?></div>
            <ul class="nav nav-inline">
                <?php foreach ( $menu_items as $item ): ?>
                    <li id="<?php echo esc_attr( $item['slug'] ); ?>-groups-li" class="<?php echo esc_attr( $item['class'] ); ?>"><a href="<?php echo esc_url( $item['link'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

		<div id="openlabCalendar" class="calendar-wrapper">
		    <?php echo eo_get_event_fullcalendar( $args ); ?>
		</div>

		<div id="bpeo-ical-download">
		    <h3><?php esc_html_e( 'Subscribe', 'openlab-theme' ); ?></h3>
		    <li><a class="bpeo-ical-link" href="<?php echo esc_url( $link ); ?>"><span class="icon"></span><?php esc_html_e( 'Download iCalendar file (Public)', 'openlab-theme' ); ?></a></li>
		</div>
	</div>
</div>
