<?php
/**
 * Custom functionality extrapolated from BuddyPress Group Email Subscription
 * See also: openlab/buddypress/groups/single/notifications.php for template overrides
 */

/**
 * show group subscription settings on the notification page.
 *
 * @global type $bp
 * @return boolean
 */
function openlab_ass_group_subscribe_settings() {
	global $bp;

	$group = groups_get_current_group();

	if ( ! is_user_logged_in() || ! empty( $group->is_banned ) || ! $group->is_member ) {
		return false;
	}

	$group_status = ass_get_group_subscription_status( bp_loggedin_user_id(), $group->id );

	$submit_link = bp_get_groups_action_link( 'notifications' );
	?>
	<div id="ass-email-subscriptions-options-page">
		<form action="<?php echo esc_attr( $submit_link ); ?>" method="post" class="form-panel">
			<div class="panel-button-group">
				<div class="panel panel-default">
					<div class="panel-heading"><?php esc_html_e( 'Email Subscription Options', 'commons-in-a-box' ); ?></div>
					<div class="panel-body">
						<input type="hidden" name="ass_group_id" value="<?php echo esc_attr( $group->id ); ?>"/>
						<?php wp_nonce_field( 'ass_subscribe' ); ?>

						<p>
							<?php // translators: group name ?>
							<b><?php printf( esc_html__( 'How do you want to be notified about activity in "%s"?', 'commons-in-a-box' ), esc_html( bp_get_group_name() ) ); ?></b>
						</p>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="no" <?php checked( 'no' === $group_status || 'un' === $group_status || ! $group_status ); ?> /><?php esc_html_e( 'No Email', 'commons-in-a-box' ); ?></label>
							<div class="ass-email-explain italics"><?php esc_html_e( 'I will read all content on the web', 'commons-in-a-box' ); ?></div>
						</div>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="sum" <?php checked( 'sum', $group_status ); ?> /><?php esc_html_e( 'Weekly Summary Email', 'commons-in-a-box' ); ?></label>
							<div class="ass-email-explain italics"><?php esc_html_e( 'Get a summary of new topics each week', 'commons-in-a-box' ); ?></div>
						</div>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="dig" <?php checked( 'dig', $group_status ); ?> /><?php esc_html_e( 'Daily Digest Email', 'commons-in-a-box' ); ?></label>
							<div class="ass-email-explain italics"><?php esc_html_e( 'Get all the day\'s activity bundled into a single email', 'commons-in-a-box' ); ?></div>
						</div>

						<?php if ( ass_get_forum_type() ) : ?>
							<div class="ass-email-type radio">
								<label><input type="radio" name="ass_group_subscribe" value="sub" <?php checked( 'sub', $group_status ); ?> /><?php esc_html_e( 'New Topics Email', 'commons-in-a-box' ); ?></label>
								<div class="ass-email-explain italics"><?php esc_html_e( 'Send new topics as they arrive (but don\'t send replies)', 'commons-in-a-box' ); ?></div>
							</div>
						<?php endif; ?>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="supersub" <?php checked( 'supersub', $group_status ); ?> /><?php esc_html_e( 'All Email', 'commons-in-a-box' ); ?></label>
							<div class="ass-email-explain italics"><?php esc_html_e( 'Send all activity as it arrives', 'commons-in-a-box' ); ?></div>
						</div>
					</div>
				</div>

				<input type="submit" value="<?php esc_html_e( 'Save Settings', 'commons-in-a-box' ); ?>" id="ass-save" name="ass-save" class="btn btn-primary">
			</div>
		</form>
	</div><!-- end ass-email-subscriptions-options-page -->
	<?php
}

// Add a notice at end of email notification about how to change group email subscriptions
function openlab_ass_add_notice_to_notifications_page() {
	?>
		<div id="group-email-settings">
			<table class="notification-settings zebra">
				<thead>
					<tr>
						<th class="icon">&nbsp;</th>
						<th class="title"><?php esc_html_e( 'Individual Group Email Settings', 'commons-in-a-box' ); ?></th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td>&nbsp;</td>
						<td>
							<p><?php esc_html_e( 'To change the email notification settings for your group:', 'commons-in-a-box' ); ?></p>

							<ol>
								<li><?php esc_html_e( "Visit the group's Profile page", 'commons-in-a-box' ); ?></li>
								<li><?php esc_html_e( "In the sidebar, click 'Membership'", 'commons-in-a-box' ); ?></li>
								<li><?php esc_html_e( "Select 'Your Email Options'", 'commons-in-a-box' ); ?></li>
							</ol>

							<?php if ( 'yes' === get_option( 'ass-global-unsubscribe-link' ) ) : ?>
								<p><a href="<?php echo esc_attr( wp_nonce_url( add_query_arg( 'ass_unsubscribe', 'all' ), 'ass_unsubscribe_all' ) ); ?>"><?php esc_html_e( "Or set all your group's email options to No Email", 'commons-in-a-box' ); ?></a></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php
}
add_action(
	'bp_screens',
	function() {
		remove_action( 'bp_notification_settings', 'ass_add_notice_to_notifications_page', 9000 );
		add_action( 'bp_notification_settings', 'openlab_ass_add_notice_to_notifications_page', 9000 );
	}
);
