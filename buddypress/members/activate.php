<?php /* This template is only used on multisite installations */ ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_activation_page' ); ?>

		<div class="page row" id="activate-page">

			<div class="col-md-24">

				<div class="panel panel-default">

					<?php if ( bp_account_was_activated() ) : ?>

						<div class="panel-heading">
							<?php esc_html_e( 'Account Activated', 'commons-in-a-box' ); ?>
						</div>

						<div class="panel-body">
							<?php do_action( 'template_notices' ); ?>

							<?php do_action( 'bp_before_activate_content' ); ?>

							<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
							<?php if ( isset( $_GET['e'] ) ) : ?>
								<p class="bp-template-notice updated no-margin no-margin-bottom"><?php esc_html_e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'commons-in-a-box' ); ?></p>
							<?php else : ?>
								<p class="bp-template-notice updated no-margin no-margin-bottom"><?php esc_html_e( 'Your account was activated successfully! You can now log in with the username and password you provided when you signed up.', 'commons-in-a-box' ); ?></p>
							<?php endif; ?>
						</div>

					<?php else : ?>

						<div class="panel-heading">
							<?php esc_html_e( 'Activate your Account', 'commons-in-a-box' ); ?>
						</div>

						<div class="panel-body">
							<?php do_action( 'bp_before_activate_content' ); ?>

							<p><?php esc_html_e( 'Please provide a valid activation key.', 'commons-in-a-box' ); ?></p>

							<form action="" method="post" class="standard-form form" id="activation-form">
								<label for="key"><?php esc_html_e( 'Activation Key:', 'commons-in-a-box' ); ?></label>
								<input class="form-control" type="text" name="key" id="key" value="<?php echo esc_attr( bp_get_current_activation_key() ); ?>" />

								<p class="submit">
									<input class="btn btn-primary btn-margin btn-margin-top" type="submit" name="submit" value="<?php esc_html_e( 'Activate', 'commons-in-a-box' ); ?> &#xf138;" />
								</p>
							</form>
						</div>

					<?php endif; ?>

					<?php do_action( 'bp_after_activate_content' ); ?>

				</div>

			</div>

		</div><!-- .page -->

		<?php do_action( 'bp_after_activation_page' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->
