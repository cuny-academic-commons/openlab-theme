<form action="<?php bp_messages_form_action( 'compose' ); ?>" method="post" id="send_message_form" class="standard-form form-panel">

	<div class="panel panel-default">
		<div class="panel-heading semibold"><?php esc_html_e( 'Compose Message', 'commons-in-a-box' ); ?></div>
		<div class="panel-body">

	<?php do_action( 'bp_before_messages_compose_content' ); ?>

	<label for="send-to-input"><?php esc_html_e( "Send To (Username or Friend's Name)", 'commons-in-a-box' ); ?> &nbsp; <span class="ajax-loader"></span></label>
	<ul class="first acfb-holder">
		<li>
			<?php bp_message_get_recipient_tabs(); ?>
			<input type="text" name="send-to-input" class="send-to-input form-control" id="send-to-input" />
		</li>
	</ul>

	<?php if ( is_super_admin() ) : ?>
		<div class="checkbox">
			<label><input type="checkbox" id="send-notice" name="send-notice" value="1" /><?php esc_html_e( 'This is a notice to all users.', 'commons-in-a-box' ); ?></label>
		</div>
	<?php endif; ?>

	<label for="subject"><?php esc_html_e( 'Subject', 'commons-in-a-box' ); ?></label>
	<input class="form-control" type="text" name="subject" id="subject" value="<?php bp_messages_subject_value(); ?>" />

	<label for="content"><?php esc_html_e( 'Message', 'commons-in-a-box' ); ?></label>
	<textarea class="form-control" name="content" id="message_content" rows="15" cols="40"><?php bp_messages_content_value(); ?></textarea>

	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames(); ?>" />

	<?php do_action( 'bp_after_messages_compose_content' ); ?>
		</div>
	</div>

	<div class="submit">
		<input class="btn btn-primary" type="submit" value="<?php esc_html_e( 'Send Message', 'commons-in-a-box' ); ?> &rarr;" name="send" id="send" />
		<span class="ajax-loader"></span>
	</div>

	<?php wp_nonce_field( 'messages_send_message' ); ?>
</form>

<script type="text/javascript">
	document.getElementById("send-to-input").focus();
</script>

