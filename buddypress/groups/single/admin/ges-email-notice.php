<div class="panel-button-group form-panel">
	<div class="panel panel-default">
		<?php // translators: group name ?>
		<div class="panel-heading semibold"><?php printf( esc_html__( 'Send an email notice to all members of "%s"', 'commons-in-a-box' ), esc_html( bp_get_group_name() ) ); ?></div>

		<div class="panel-body">
			<?php // translators: group name ?>
			<p><?php printf( esc_html__( 'You can use the form below to send an email notice to all members of "%s".', 'commons-in-a-box' ), esc_html( bp_get_group_name() ) ); ?> <br>
			<b><?php esc_html_e( 'Members will receive the notice regardless of email settings, so please use with caution', 'commons-in-a-box' ); ?></b>.</p>

			<p>
				<label for="ass-admin-notice-subject"><?php esc_html_e( 'Email Subject:', 'commons-in-a-box' ); ?></label>
				<input type="text" class="form-control" name="ass_admin_notice_subject" id="ass-admin-notice-subject" value="" />
			</p>

			<p>
				<label for="ass-admin-notice-textarea"><?php esc_html_e( 'Email Content:', 'commons-in-a-box' ); ?></label>
				<textarea value="" class="form-control" name="ass_admin_notice" id="ass-admin-notice-textarea"></textarea>
			</p>

			<p>
				<input class="btn btn-primary" type="submit" name="ass_admin_notice_send" value="<?php esc_html_e( 'Send Notice', 'commons-in-a-box' ); ?>" />
			</p>

			<?php wp_nonce_field( 'bpges_admin_notice', 'bpges-admin-notice-nonce' ); ?>
		</div>
	</div>
</div>
