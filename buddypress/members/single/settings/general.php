<?php
/**
 * Members settings - general
 *
 * */
do_action( 'bp_before_member_settings_template' );
?>

<?php echo openlab_submenu_markup(); ?>


<div id="item-body" role="main">

	<?php do_action( 'bp_template_content' ) ?>

	<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form form-panel" id="settings-form">

		<div class="panel panel-default">
			<div class="panel-heading"><?php esc_html_e( 'Account Settings', 'openlab-theme' ); ?></div>
				<div class="panel-body">
	            	<?php do_action( 'template_notices' ); ?>

		<div class="form-group settings-section username-section">
			<label for="username">Username</label>
			<input class="form-control" type="text" id="username" disabled="disabled" value="<?php bp_displayed_user_username() ?>" />
			<p class="description"><?php esc_html_e( 'Your username cannot be changed.', 'openlab-theme' ); ?></p>
		</div>

		<div class="form-group settings-section email-section">
			<label for="email_visible"><?php esc_html_e( 'Account Email Address', 'openlab-theme' ); ?></label>
			<input class="form-control" type="text" name="email" id="email" value="<?php echo esc_attr( bp_get_displayed_user_email() ); ?>" class="settings-input" />
		</div>

		<div class="form-group settings-section current-pw-section">
			<label for="pwd"><?php esc_html_e( 'Current Password', 'openlab-theme' ); ?></label>
			<input class="form-control" type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" />

			<?php
			$account_type = openlab_get_displayed_user_account_type();
			$include_acct_type = in_array( $account_type, array( 'Student', 'Alumni' ) ) ? ' account type, ' : ' ';
			?>

			<p class="description"><?php esc_html_e( 'Required to change email address or password.', 'openlab-theme' ); ?> <a class="underline" href="<?php echo site_url( add_query_arg( array( 'action' => 'lostpassword' ), 'wp-login.php' ), 'login' ); ?>" title="<?php _e( 'Password Lost and Found', 'openlab-theme' ); ?>"><?php _e( 'Lost your password?', 'openlab-theme' ); ?></a></p>
		</div>

		<div class="form-group settings-section change-pw-section">
			<label for="pass1"><?php esc_html_e( 'Change Password', 'openlab-theme' ); ?></label>
			<input class="form-control" type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small" />

			<label for="pass1"><?php esc_html_e( 'Confirm Change Password', 'openlab-theme' ); ?></label>
			<input class="form-control" type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small" />

			<p class="description"><?php esc_html_e( 'Leave blank for no change.', 'openlab-theme' ); ?></p>
		</div>

			</div>
		</div>

		<?php if ( openlab_braille_is_enabled() ) : ?>
			<div class="panel panel-default">
				<div class="panel-heading"><?php esc_html_e( 'Braille Settings', 'openlab-theme' ); ?></div>

				<div class="panel-body">
					<p><?php esc_html_e( 'Enable the Braille toggle to allow you to your private messages in SimBraille, a visual representation of Braille text.', 'openlab-theme' ); ?></p>
					<div class="checkbox">
						<label for="bp-messages-braille">
							<?php $show_braille = (bool) bp_get_user_meta( bp_displayed_user_id(), 'bp_messages_braille', true ); ?>
							<input type="checkbox" name="bp-messages-braille" id="bp-messages-braille" value="1" <?php checked( $show_braille ); ?> />
							<?php esc_html_e( 'Show Braille toggle for private messages?', 'bp-braille' ); ?>
						</label>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php do_action( 'bp_core_general_settings_before_submit' ); ?>

		<div class="submit">
			<input class="btn btn-primary btn-margin btn-margin-top" type="submit" name="submit" value="<?php esc_html_e( 'Save Changes', 'openlab-theme' ); ?>" id="submit" class="auto" />
		</div>

		<?php do_action( 'bp_core_general_settings_after_submit' );
		wp_nonce_field( 'bp_settings_general' );
		?>
	</form>
<?php do_action( 'bp_after_member_body' ); ?>
</div><!-- #item-body -->
<?php
do_action( 'bp_after_member_settings_template' );
