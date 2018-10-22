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
		<form action="<?php echo $submit_link ?>" method="post" class="form-panel">
			<div class="panel-button-group">
				<div class="panel panel-default">
					<div class="panel-heading"><?php _e( 'Email Subscription Options', 'bp-ass' ) ?></div>
					<div class="panel-body">
						<input type="hidden" name="ass_group_id" value="<?php echo $group->id; ?>"/>
						<?php wp_nonce_field( 'ass_subscribe' ); ?>

						<p>
							<b><?php printf( esc_html__( 'How do you want to be notified about activity in "%s"?', 'openlab-theme' ), esc_html( bp_get_group_name() ) ); ?></b>
						</p>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="no" <?php if ( $group_status == 'no' || $group_status == 'un' || ! $group_status ) { echo 'checked="checked"';} ?>><?php _e( 'No Email', 'bp-ass' ); ?></label>
							<div class="ass-email-explain italics"><?php esc_html_e( 'I will read all content on the web', 'bp-ass' ); ?></div>
						</div>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="sum" <?php if ( $group_status == 'sum' ) { echo 'checked="checked"';} ?>><?php _e( 'Weekly Summary Email', 'bp-ass' ); ?></label>
							<div class="ass-email-explain italics"><?php _e( 'Get a summary of new topics each week', 'bp-ass' ); ?></div>
						</div>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="dig" <?php if ( $group_status == 'dig' ) { echo 'checked="checked"';} ?>><?php _e( 'Daily Digest Email', 'bp-ass' ); ?></label>
							<div class="ass-email-explain italics"><?php _e( 'Get all the day\'s activity bundled into a single email', 'bp-ass' ); ?></div>
						</div>

						<?php if ( ass_get_forum_type() ) : ?>
							<div class="ass-email-type radio">
								<label><input type="radio" name="ass_group_subscribe" value="sub" <?php if ( $group_status == 'sub' ) { echo 'checked="checked"';} ?>><?php _e( 'New Topics Email', 'bp-ass' ); ?></label>
								<div class="ass-email-explain italics"><?php _e( 'Send new topics as they arrive (but don\'t send replies)', 'bp-ass' ); ?></div>
							</div>
						<?php endif; ?>

						<div class="ass-email-type radio">
							<label><input type="radio" name="ass_group_subscribe" value="supersub" <?php if ( $group_status == 'supersub' ) { echo 'checked="checked"';} ?>><?php _e( 'All Email', 'bp-ass' ); ?></label>
							<div class="ass-email-explain italics"><?php _e( 'Send all activity as it arrives', 'openlab-theme' ); ?></div>
						</div>
					</div>
				</div>

				<input type="submit" value="<?php _e( 'Save Settings', 'bp-ass' ) ?>" id="ass-save" name="ass-save" class="btn btn-primary">
			</div>

			<?php if ( ass_get_forum_type() == 'buddypress' ) : ?>
				<p class="ass-sub-note italics"><?php _e( 'Note: Normally, you receive email notifications for topics you start or comment on. This can be changed at', 'bp-ass' ); ?> <a href="<?php echo bp_loggedin_user_domain() . BP_SETTINGS_SLUG . '/notifications/' ?>"><?php _e( 'email notifications', 'bp-ass' ); ?></a>.</p>
			<?php endif; ?>

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
						<th class="title"><?php _e( 'Individual Group Email Settings', 'bp -ass' ); ?></th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td>&nbsp;</td>
						<td>
							<p><?php _e( 'To change the email notification settings for { your Courses, Projects, Clubs and Portfolio:','bp -ass' ); ?></p>
																			<ol>
																				<li>Visit the group's Profile page </li>
																				<li>In the sidebar, click 'Membership'</li>
																				<li>Select 'Your Email Options'</li>
																			</ol>

							<?php if ( get_option( 'ass-global-unsubscribe-link' ) == 'yes' ) : ?>
								<p><a href="<?php echo wp_nonce_url( add_query_arg( 'ass_unsubscribe', 'all' ), 'ass_unsubscribe_all' ); ?>"><?php _e( "Or set all your group's email options to No Email", 'bp-ass' ); ?></a></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
<?php
}

remove_action( 'bp_notification_settings', 'ass_add_notice_to_notifications_page', 9000 );
add_action( 'bp_notification_settings', 'openlab_ass_add_notice_to_notifications_page', 9000 );
