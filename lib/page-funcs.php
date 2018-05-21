<?php
/**
 * 	Home page functionality
 */

/**
 * 	Home page login box layout
 */
function cuny_home_login() {
	if ( ! function_exists( 'buddypress' ) ) {
		return;
	}

	if ( is_user_logged_in() ) :

		echo '<div id="open-lab-login" class="log-box">';
		echo '<h1 class="title inline-element semibold">Welcome,</h1><h2 class="title inline-element">' . bp_core_get_user_displayname( bp_loggedin_user_id() ) . '</h2>';
		do_action( 'bp_before_sidebar_me' )
		?>

		<div id="sidebar-me" class="clearfix">
			<div id="user-info">
				<a class="avatar" href="<?php echo bp_loggedin_user_domain() ?>">
					<img class="img-responsive" src="<?php bp_loggedin_user_avatar( array( 'type' => 'full', 'html' => false ) ); ?>" alt="Avatar for <?php echo bp_core_get_user_displayname( bp_loggedin_user_id() ); ?>" />
				</a>

				<div class="welcome-link-my-profile">
					<a href="<?php echo	esc_url( bp_loggedin_user_domain() ); ?>"><?php esc_html_e( 'My Profile', 'openlab-theme' ); ?></a>
				</div>

				<ul class="content-list">
					<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>">Not <?php echo bp_core_get_username( bp_loggedin_user_id() ); ?>?</a></li>
					<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a></li>
				</ul>
				</span><!--user-info-->
			</div>
			<?php do_action( 'bp_sidebar_me' ) ?>
		</div><!--sidebar-me-->

		<?php do_action( 'bp_after_sidebar_me' ) ?>

		<?php echo '</div>'; ?>

		<div id="login-help" class="log-box">
			<h4 class="title">Need Help?</h4>
			<p class="font-size font-14">Visit the <a class="roll-over-loss" href="<?php echo site_url(); ?>/blog/help/openlab-help/">Help section</a> or <a class="roll-over-loss" href='<?php echo site_url(); ?>/about/contact-us/'>contact us</a> with a question.</p>
		</div><!--login-help-->

	<?php else : ?>
		<?php echo '<div id="open-lab-join" class="log-box">'; ?>
		<?php echo '<h2 class="title"><span class="fa fa-plus-circle flush-left"></span> ' . esc_html__( 'Sign Up', 'openlab-theme' ) . '</h2>'; ?>
		<?php _e( '<p><a class="btn btn-default btn-primary link-btn pull-right semibold" href="' . site_url() . '/register/">Sign up</a> <span class="font-size font-14">Need an account?<br />Sign Up to become a member!</span></p>', 'buddypress' ) ?>
		<?php echo '</div>'; ?>

		<?php echo '<div id="open-lab-login" class="log-box">'; ?>
		<?php do_action( 'bp_after_sidebar_login_form' ) ?>
		<?php echo '</div>'; ?>

		<div id="user-login" class="log-box">

			<?php echo '<h2 class="title"><span class="fa fa-arrow-circle-right"></span> Log in</h2>'; ?>
			<?php do_action( 'bp_before_sidebar_login_form' ) ?>

			<form name="login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
				<label class="sr-only" for="sidebar-user-login">Username</label>
				<input class="form-control input" type="text" name="log" id="sidebar-user-login" value="" placeholder="Username" tabindex="0" />

				<label class="sr-only" for="sidebar-user-pass">Password</label>
				<input class="form-control input" type="password" name="pwd" id="sidebar-user-pass" value="" placeholder="Password" tabindex="0" />

				<div id="keep-logged-in" class="small-text clearfix">
					<div class="password-wrapper">
						<a class="forgot-password-link small-text roll-over-loss" href="<?php echo site_url( 'wp-login.php?action=lostpassword', 'login' ) ?>">Forgot Password?</a>
						<span class="keep-logged-in-checkbox"><input class="no-margin no-margin-top" name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="0" /><label class="regular no-margin no-margin-bottom" for="sidebar-rememberme"><?php _e( 'Keep me logged in', 'buddypress' ) ?></label></span>
					</div>
					<input class="btn btn-default btn-primary link-btn pull-right semibold" type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e( 'Log In' ); ?>" tabindex="0" />
				</div>
				<?php /*<input type="hidden" name="redirect_to" value="<?php echo bp_get_root_domain(); ?>" /> */ ?>

				<?php do_action( 'bp_sidebar_login_form' ) ?>

			</form>
		</div>
	<?php
	endif;
}

/**
 * 	Registration page layout
 */
function openlab_registration_page() {
	do_action( 'bp_before_register_page' );

	$registration_form_settings = cboxol_get_registration_form_settings();

	$ajaxurl = bp_core_ajax_url();
	$site_name = bp_get_option( 'blogname' );

	$limited_email_domains_message = '';
	$limited_email_domains = get_site_option( 'limited_email_domains' );
	if ( $limited_email_domains ) {
		$led = array();
		foreach ( $limited_email_domains as $d ) {
			$led[] = sprintf( '<span class="limited-email-domain">' . esc_html( $d ) . '</span>' );
		}
		$limited_email_domains_message = sprintf(
			esc_html__( 'Allowed email domains: %s', 'openlab-theme' ),
			implode( ', ', $led )
		);
	}

	$member_types = cboxol_get_member_types();

	?>

	<div class="page" id="register-page">

		<div id="openlab-main-content"></div>

		<div class="entry-title">
			<h1><?php _e( 'Create an Account', 'openlab-theme' ) ?></h1>
		</div>

		<form action="" name="signup_form" id="signup_form" class="standard-form form-panel" method="post" enctype="multipart/form-data" data-parsley-trigger="blur">

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><?php esc_html_e( 'Account Details', 'openlab-theme' ); ?></div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ) ?>

						<p><?php printf( esc_html__( 'Registering for %s is easy. Just fill in the fields below and we\'ll get a new account set up for you in no time.', 'openlab-theme' ), esc_html( $site_name ) ); ?></p>

						<?php do_action( 'bp_before_account_details_fields' ) ?>

						<div class="register-section" id="basic-details-section">

							<div class="form-group">
								<label class="control-label" for="signup_username"><?php esc_html_e( 'Username', 'openlab-theme' ) ?> <?php esc_html_e( '(required)', 'openlab-theme' ) ?> <?php esc_html_e( '(lowercase & no special characters)', 'openlab-theme' ); ?></label>
								<?php do_action( 'bp_signup_username_errors' ) ?>
								<input
									class="form-control"
									type="text"
									name="signup_username"
									id="signup_username"
									value="<?php esc_attr( bp_signup_username_value() ) ?>"
									data-parsley-lowercase
									data-parsley-nospecialchars
									data-parsley-required
									data-parsley-minlength="4"
									data-parsley-remote="<?php echo add_query_arg( array(
										'action' => 'openlab_unique_login_check',
										'login' => '{value}',
									), $ajaxurl ); ?>"
									data-parsley-remote-message="<?php esc_attr_e( 'That username is already taken.', 'openlab-theme' ); ?>"
								/>
							</div>

							<div class="form-group">
								<label class="control-label" for="signup_email"><?php esc_html_e( 'Email Address (required)', 'openlab-theme' ); ?> <?php if ( $limited_email_domains_message ) : ?><div class="email-requirements"><?php echo $limited_email_domains_message; ?></div><?php endif; ?></label>
								<?php do_action( 'bp_signup_email_errors' ) ?>
								<input
									class="form-control"
									type="text"
									name="signup_email"
									id="signup_email"
									value="<?php echo esc_attr( openlab_post_value( 'signup_email' ) ) ?>"
									data-parsley-trigger="blur"
									data-parsley-required
									data-parsley-type="email"
									data-parsley-group="email"
									data-parsley-iff="#signup_email_confirm"
									data-parsley-iff-message=""
								/>

								<label class="control-label" for="signup_email_confirm"><?php esc_html_e( 'Confirm Email Address (required)', 'openlab-theme' ); ?></label>
								<input
								class="form-control"
								type="text"
								name="signup_email_confirm"
								id="signup_email_confirm"
								value="<?php echo esc_attr( openlab_post_value( 'signup_email_confirm' ) ); ?>"
								data-parsley-trigger="blur"
								data-parsley-required
								data-parsley-type="email"
								data-parsley-iff="#signup_email"
								data-parsley-iff-message="<?php esc_attr_e( 'Email addresses must match.', 'openlab-theme' ); ?>"
								data-parsley-group="email"
								/>
							</div>

							<div data-parsley-children-should-match class="form-group">
								<label class="control-label" for="signup_password"><?php _e( 'Choose a Password', 'openlab-theme' ) ?> <?php _e( '(required)', 'openlab-theme' ) ?></label>
								<?php do_action( 'bp_signup_password_errors' ) ?>
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

								<label class="control-label" for="signup_password_confirm"><?php _e( 'Confirm Password', 'openlab-theme' ) ?> <?php _e( '(required)', 'openlab-theme' ) ?></label>
								<?php do_action( 'bp_signup_password_confirm_errors' ) ?>
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
									data-parsley-iff-message="<?php esc_attr_e( 'Passwords must match.', 'openlab-theme' ); ?>"
								/>
							</div>

						</div><!-- #basic-details-section -->
					</div>
				</div><!--.panel-->

				<?php do_action( 'bp_after_account_details_fields' ) ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<div class="panel panel-default">
						<div class="panel-heading semibold"><?php esc_html_e( 'Public Profile Details', 'openlab-theme' ); ?></div>
						<div class="panel-body">

							<?php do_action( 'bp_before_signup_profile_fields' ) ?>

							<div class="register-section" id="profile-details-section">

								<p><?php esc_html_e( 'Your responses in the form fields below will be displayed on your profile page, which is open to the public. You can always add, edit, or remove information at a later date.', 'openlab-theme' ); ?></p>

								<?php /* @todo Abstract selector? */ ?>
								<div class="editfield form-group account-type-select-ui">
									<?php do_action( 'bp_account_type_errors' ) ?>
									<label class="control-label" for="account-type"><?php esc_html_e( 'Account Type', 'openlab-theme' ); ?> <?php esc_html_e( '(required)', 'openlab-theme' ); ?></label>
									<div class="col-md-24">
										<div class="col-md-8">
											<select name="account-type" class="form-control" id="account-type">
												<option value=""><?php esc_html_e( '- Select Account Type -', 'openlab-theme' ); ?></option>
												<?php foreach ( $member_types as $member_type ) : ?>
													<option value="<?php echo esc_attr( $member_type->get_slug() ); ?>" data-requires-signup-code="<?php echo intval( $member_type->get_requires_signup_code() ); ?>"><?php echo esc_html( $member_type->get_label( 'singular' ) ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>

										<div class="col-md-8">
											<input class="form-control" name="account-type-signup-code" id="account-type-signup-code" placeholder="<?php esc_attr_e( 'Please enter a sign up code', 'openlab-theme' ); ?>" />
										</div>

										<div class="col-md-8 signup-code-message" id="signup-code-message"></div>
									</div>
								</div>

								<div id="openlab-profile-fields"></div>

								<?php echo cboxol_get_academic_unit_selector( array(
									'entity_type' => 'user',
								) ); ?>

								<?php do_action( 'bp_after_signup_profile_fields' ) ?>

							</div><!-- #profile-details-section -->
						</div>
					</div><!--.panel-->



				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ) ?>

				<p class="sign-up-terms">
					<?php echo $registration_form_settings['confirmationText']; ?>
				</p>

				<p id="submitSrMessage" class="sr-only submit-alert" aria-live="polite"></p>

				<div class="submit">
					<input type="submit" name="signup_submit" id="signup_submit" class="btn btn-primary btn-disabled" value="<?php _e( 'Please Complete Required Fields', 'buddypress' ) ?>" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ) ?>

				<?php wp_nonce_field( 'bp_new_signup' ) ?>

			<?php endif; // request-details signup step    ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><?php _e( 'Sign Up Complete!', 'buddypress' ) ?></div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ) ?>

						<?php if ( bp_registration_needs_activation() ) : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ) ?></p>
						<?php else : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ) ?></p>
						<?php endif; ?>

					</div>
				</div><!--.panel-->

			<?php endif; // completed-confirmation signup step    ?>

			<?php do_action( 'bp_custom_signup_steps' ) ?>

		</form>

	</div>

	<?php do_action( 'bp_after_register_page' ) ?>

	<?php do_action( 'bp_after_directory_activity_content' ) ?>

	<script type="text/javascript">
		jQuery(document).ready(function () {
			if (jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show'))
				jQuery('div#blog-details').toggle();

			jQuery('input#signup_with_blog').click(function () {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
	<?php
}

function openlab_primary_skip_link() {
	$skip_link_out = '';

	$content_target = '#openlab-main-content';
	$content_text = 'main content';

	if ( is_user_logged_in() ) {
		$adminbar_target = '#wp-admin-bar-my-openlab';
		$adminbar_text = 'admin bar';
	} else {
		$adminbar_target = '#wp-admin-bar-bp-login';
		$adminbar_text = 'log in';
	}

	$skip_link_out = <<<HTML
            <a id="skipToContent" tabindex="0" class="sr-only sr-only-focusable skip-link" href="{$content_target}">Skip to {$content_text}</a>
            <a id="skipToAdminbar" tabindex="0" class="sr-only sr-only-focusable skip-link" href="{$adminbar_target}">Skip to {$adminbar_text}</a>
HTML;

	return $skip_link_out;
}
