<?php $welcome_email = groups_get_groupmeta( bp_get_current_group_id(), 'ass_welcome_email' ); ?>
<?php $welcome_email_enabled = isset( $welcome_email['enabled'] ) ? $welcome_email['enabled'] : ''; ?>


<div class="panel-button-group">
	<div class="panel panel-default">
		<div class="panel-heading semibold"><?php esc_html_e( 'Welcome Email', 'openlab-theme' ); ?></div>

		<div class="panel-body">
			<p><?php printf( esc_html__( 'Send an email when a new member joins "%s".', 'bp-ass' ), esc_html( bp_get_group_name() ) ); ?></p>

			<p class="checkbox">
				<label>
					<input<?php checked( $welcome_email_enabled, 'yes' ); ?> type="checkbox" name="ass_welcome_email[enabled]" id="ass-welcome-email-enabled" value="yes" />
					<?php esc_html_e( 'Enable welcome email', 'bp-ass' ); ?>
				</label>
			</p>

			<p class="ass-welcome-email-field<?php if ( $welcome_email_enabled != 'yes' ) echo ' hide-if-js'; ?>">
				<label for="ass-welcome-email-subject"><?php esc_html_e( 'Email Subject:', 'openlab-theme' ); ?></label>
				<input class="form-control" value="<?php echo isset( $welcome_email['subject'] ) ? $welcome_email['subject'] : ''; ?>" type="text" name="ass_welcome_email[subject]" id="ass-welcome-email-subject" />
			</p>

			<p class="ass-welcome-email-field<?php if ( $welcome_email_enabled != 'yes' ) echo ' hide-if-js'; ?>">
				<label for="ass-welcome-email-content"><?php esc_html_e( 'Email Content:', 'openlab-theme'); ?></label>
				<textarea class="form-control" name="ass_welcome_email[content]" id="ass-welcome-email-content"><?php echo isset( $welcome_email['content'] ) ? $welcome_email['content'] : ''; ?></textarea>
			</p>

			<p>
				<input class="btn btn-primary" type="submit" name="ass_welcome_email_submit" value="<?php esc_html_e( 'Save', 'openlab-theme' ); ?>" />
			</p>
		</div>
	</div>
</div>
