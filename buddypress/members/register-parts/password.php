<?php
/**
 * Password field for the signup form.
 *
 * @since 1.7.0
 */
?>

<div data-parsley-children-should-match class="form-group">
	<label class="control-label" for="signup_password"><?php esc_html_e( 'Choose a Password', 'commons-in-a-box' ); ?> <?php esc_html_e( '(required)', 'commons-in-a-box' ); ?></label>
	<?php do_action( 'bp_signup_password_errors' ); ?>
	<div class="password-field">
		<input
			class="form-control"
			type="password"
			name="signup_password"
			id="signup_password"
			value=""
			data-parsley-trigger="blur"
			data-parsley-required
			data-parsley-group="password"
			data-parsley-iff="#signup_password_confirm"
			data-parsley-iff-message=""
		/>

		<div id="password-strength-notice" class="password-strength-notice"></div>
	</div>

	<label class="control-label" for="signup_password_confirm"><?php esc_html_e( 'Confirm Password', 'commons-in-a-box' ); ?> <?php esc_html_e( '(required)', 'commons-in-a-box' ); ?></label>
	<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
	<input
		class="form-control password-field"
		type="password"
		name="signup_password_confirm"
		id="signup_password_confirm"
		value=""
		data-parsley-trigger="blur"
		data-parsley-required
		data-parsley-group="password"
		data-parsley-iff="#signup_password"
		data-parsley-iff-message="<?php esc_attr_e( 'Passwords must match.', 'commons-in-a-box' ); ?>"
	/>
</div>

