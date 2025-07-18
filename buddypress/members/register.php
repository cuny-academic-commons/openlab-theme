<?php
/**
 * Member registration template.
 */
?>

<div class="col-sm-18">
	<?php
	do_action( 'bp_before_register_page' );

	$registration_form_settings = cboxol_get_registration_form_settings();

	$site_name = bp_get_option( 'blogname' );

	$member_types = cboxol_get_member_types();

	?>

	<div class="page" id="register-page">

		<div id="openlab-main-content"></div>

		<div class="entry-title">
			<h1><?php esc_html_e( 'Create an Account', 'commons-in-a-box' ); ?></h1>
		</div>

		<form action="" name="signup_form" id="signup_form" class="standard-form form-panel" method="post" enctype="multipart/form-data" data-parsley-trigger="blur">

			<?php if ( 'request-details' === bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><?php esc_html_e( 'Account Details', 'commons-in-a-box' ); ?></div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ); ?>

						<?php // translators: site name ?>
						<p><?php printf( esc_html__( 'Registering for %s is easy. Just fill in the fields below and we\'ll get a new account set up for you in no time.', 'commons-in-a-box' ), esc_html( $site_name ) ); ?></p>

						<?php do_action( 'bp_before_account_details_fields' ); ?>

						<div class="register-section" id="basic-details-section">

							<?php bp_get_template_part( 'members/register-parts/username' ); ?>
							<?php bp_get_template_part( 'members/register-parts/email' ); ?>
							<?php bp_get_template_part( 'members/register-parts/password' ); ?>

						</div><!-- #basic-details-section -->
					</div>
				</div><!--.panel-->

				<?php do_action( 'bp_after_account_details_fields' ); ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<div class="panel panel-default">
						<div class="panel-heading semibold"><?php esc_html_e( 'Public Profile Details', 'commons-in-a-box' ); ?></div>
						<div class="panel-body">

							<?php do_action( 'bp_before_signup_profile_fields' ); ?>

							<div class="register-section" id="profile-details-section">

								<p><?php esc_html_e( 'Your responses in the form fields below will be displayed on your profile page, which is open to the public. You can always add, edit, or remove information at a later date.', 'commons-in-a-box' ); ?></p>

								<?php
								// phpcs:disable WordPress.Security.NonceVerification.Missing
								$selected_account_type = isset( $_POST['account-type'] ) ? wp_unslash( $_POST['account-type'] ) : '';
								$entered_signup_code   = isset( $_POST['account-type-signup-code'] ) ? wp_unslash( $_POST['account-type-signup-code'] ) : '';
								// phpcs:enable WordPress.Security.NonceVerification.Missing
								?>
								<div class="editfield form-group account-type-select-ui">
									<?php do_action( 'bp_account_type_errors' ); ?>
									<label class="control-label" for="account-type"><?php esc_html_e( 'Account Type', 'commons-in-a-box' ); ?> <?php esc_html_e( '(required)', 'commons-in-a-box' ); ?></label>
									<div class="col-md-24">
										<div class="col-md-8">
											<select name="account-type" class="form-control" id="account-type" required>
												<option value=""><?php esc_html_e( '- Select Account Type -', 'commons-in-a-box' ); ?></option>
												<?php foreach ( $member_types as $member_type ) : ?>
													<option value="<?php echo esc_attr( $member_type->get_slug() ); ?>" data-requires-signup-code="<?php echo intval( $member_type->get_requires_signup_code() ); ?>" <?php selected( $selected_account_type, $member_type->get_slug() ); ?> ><?php echo esc_html( $member_type->get_label( 'singular' ) ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>

										<div class="col-md-8">
											<input class="form-control" name="account-type-signup-code" id="account-type-signup-code" placeholder="<?php esc_attr_e( 'Please enter a sign up code', 'commons-in-a-box' ); ?>" value="<?php echo esc_attr( $entered_signup_code ); ?>" />
										</div>

										<div class="col-md-8 signup-code-message" id="signup-code-message"></div>
									</div>
								</div>

								<div id="openlab-profile-fields"></div>

								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo cboxol_get_academic_unit_selector(
									array(
										'entity_type' => 'user',
									)
								);
								?>

								<?php do_action( 'bp_after_signup_profile_fields' ); ?>

							</div><!-- #profile-details-section -->
						</div>
					</div><!--.panel-->



				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ); ?>

				<p class="sign-up-terms">
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo $registration_form_settings['confirmationText']; ?>
				</p>

				<p id="submitSrMessage" class="sr-only submit-alert" aria-live="polite"></p>

				<div class="submit">
					<input type="submit" name="signup_submit" id="signup_submit" class="btn btn-primary btn-disabled" value="<?php esc_html_e( 'Please Complete Required Fields', 'commons-in-a-box' ); ?>" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ); ?>

				<?php wp_nonce_field( 'bp_new_signup' ); ?>

			<?php endif; // request-details signup step ?>

			<?php if ( 'completed-confirmation' === bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><?php esc_html_e( 'Sign Up Complete!', 'commons-in-a-box' ); ?></div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ); ?>

						<?php if ( bp_registration_needs_activation() ) : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php esc_html_e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'commons-in-a-box' ); ?></p>
						<?php else : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php esc_html_e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'commons-in-a-box' ); ?></p>
						<?php endif; ?>

					</div>
				</div><!--.panel-->

			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'bp_custom_signup_steps' ); ?>

		</form>

	</div>

	<?php do_action( 'bp_after_register_page' ); ?>

	<?php do_action( 'bp_after_directory_activity_content' ); ?>

	<script type="text/javascript">
		jQuery(document).ready(function () {
			if (jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show'))
				jQuery('div#blog-details').toggle();

			jQuery('input#signup_with_blog').click(function () {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
</div><!--content-->
