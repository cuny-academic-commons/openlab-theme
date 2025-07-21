<?php
/**
 * Username field for the signup form.
 *
 * @since 1.7.0
 */
?>

<div class="form-group">
	<label class="control-label" for="signup_username"><?php esc_html_e( 'Username', 'commons-in-a-box' ); ?> <?php esc_html_e( '(required)', 'commons-in-a-box' ); ?> <?php esc_html_e( '(lowercase & no special characters)', 'commons-in-a-box' ); ?></label>
	<?php do_action( 'bp_signup_username_errors' ); ?>
	<?php
	$login_check_url = add_query_arg(
		array(
			'action' => 'openlab_unique_login_check',
			'login'  => '{value}',
		),
		bp_core_ajax_url()
	);
	?>
	<input
		class="form-control"
		type="text"
		name="signup_username"
		id="signup_username"
		value="<?php esc_attr( bp_signup_username_value() ); ?>"
		data-parsley-lowercase
		data-parsley-nospecialchars
		data-parsley-required
		data-parsley-minlength="4"
		data-parsley-remote="<?php echo esc_attr( $login_check_url ); ?>"
		data-parsley-remote-message="<?php esc_attr_e( 'That username is already taken.', 'commons-in-a-box' ); ?>"
	/>
</div>
