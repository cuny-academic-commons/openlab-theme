<?php $welcome_email = groups_get_groupmeta( bp_get_current_group_id(), 'ass_welcome_email' ); ?>
<?php $welcome_email_enabled = isset( $welcome_email['enabled'] ) ? $welcome_email['enabled'] : ''; ?>


<div class="panel-button-group">
	<div class="panel panel-default">
		<div class="panel-heading semibold"><?php esc_html_e( 'Welcome Email', 'commons-in-a-box' ); ?></div>

		<div class="panel-body">
			<?php // translators: group name ?>
			<p><?php printf( esc_html__( 'Send an email when a new member joins "%s".', 'commons-in-a-box' ), esc_html( bp_get_group_name() ) ); ?></p>

			<p class="checkbox">
				<label>
					<input<?php checked( $welcome_email_enabled, 'yes' ); ?> type="checkbox" name="ass_welcome_email[enabled]" id="ass-welcome-email-enabled" value="yes" />
					<?php esc_html_e( 'Enable welcome email', 'commons-in-a-box' ); ?>
				</label>
			</p>

			<?php $hide_if_js = 'yes' !== $welcome_email_enabled ? 'hide-if-js' : ''; ?>

			<p class="ass-welcome-email-field <?php echo esc_attr( $hide_if_js ); ?>">
				<label for="ass-welcome-email-subject"><?php esc_html_e( 'Email Subject:', 'commons-in-a-box' ); ?></label>
				<input class="form-control" value="<?php echo isset( $welcome_email['subject'] ) ? esc_attr( $welcome_email['subject'] ) : ''; ?>" type="text" name="ass_welcome_email[subject]" id="ass-welcome-email-subject" />
			</p>

			<p class="ass-welcome-email-field <?php echo esc_attr( $hide_if_js ); ?>">
				<label for="ass-welcome-email-content"><?php esc_html_e( 'Email Content:', 'commons-in-a-box' ); ?></label>
				<textarea class="form-control" name="ass_welcome_email[content]" id="ass-welcome-email-content"><?php echo isset( $welcome_email['content'] ) ? esc_textarea( $welcome_email['content'] ) : ''; ?></textarea>
			</p>

			<p>
				<input class="btn btn-primary" type="submit" name="ass_welcome_email_submit" value="<?php esc_html_e( 'Save', 'commons-in-a-box' ); ?>" />
			</p>
		</div>
	</div>
</div>
