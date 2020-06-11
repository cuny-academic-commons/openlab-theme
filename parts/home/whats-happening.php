<div class="activity-list item-list inline-element-list sidebar-sublinks">

	<?php $activities = openlab_whats_happening_activity_items(); ?>

	<?php if ( $activities ) : ?>

		<?php foreach ( $activities as $activity ) : ?>

			<div class="sidebar-block activity-block">
				<div class="activity-row clearfix">
					<div class="activity-avatar pull-left">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_attr( openlab_activity_group_link( $activity ) ); ?>"><?php echo openlab_activity_group_avatar( $activity ); ?></a>
					</div>

					<div class="activity-content overflow-hidden">

						<div class="activity-header">
							<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
						<p><?php esc_html_e( 'No recent activity', 'commons-in-a-box' ); ?></p>
					</div>
				</div>
			</div>
		</div>

	<?php endif; ?>

</div><!-- .activity-list -->
