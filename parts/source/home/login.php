<?php

/**
 * Login/signup panel on home page.
 *
 * @since 1.0.0
 */

?>

<?php
if ( is_user_logged_in() ) :

	echo '<div id="open-lab-login" class="log-box">';
	echo '<h1 class="title inline-element semibold">Welcome,</h1><h2 class="title inline-element">' . bp_core_get_user_displayname( bp_loggedin_user_id() ) . '</h2>';
	do_action( 'bp_before_sidebar_me' )
	?>

	<?php
	$brand_pages = cboxol_get_brand_pages();

	$help_link = '';
	if ( isset( $brand_pages['help'] ) ) {
		$help_link = $brand_pages['help']['preview_url'];
	}

	$contact_link = '';
	if ( isset( $brand_pages['contact-us'] ) ) {
		$contact_link = $brand_pages['contact-us']['preview_url'];
	}

	?>

	<div id="sidebar-me" class="clearfix">
		<div id="user-info">
			<a class="avatar" href="<?php echo bp_loggedin_user_domain(); ?>">
				<?php /* translators: user display name */ ?>
				<img class="img-responsive" src="
				<?php
				bp_loggedin_user_avatar(
					array(
						'type' => 'full',
						'html' => false,
					)
				);
				?>
													" alt="<?php echo esc_attr( sprintf( __( 'Avatar for %s', 'openlab-theme' ), bp_core_get_user_displayname( bp_loggedin_user_id() ) ) ); ?>" />
			</a>

			<div class="welcome-link-my-profile">
				<a href="<?php echo esc_url( bp_loggedin_user_domain() ); ?>"><?php esc_html_e( 'My Profile', 'openlab-theme' ); ?></a>
			</div>

			<ul class="content-list">
				<?php /* translators: logged-in user display name */ ?>
				<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ); ?>"><?php printf( esc_html__( 'Not %s?', 'openlab-theme' ), esc_html( bp_core_get_username( bp_loggedin_user_id() ) ) ); ?></a></li>
				<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ); ?>"><?php _e( 'Log Out', 'openlab-theme' ); ?></a></li>
			</ul>
			</span><!--user-info-->
		</div>
		<?php do_action( 'bp_sidebar_me' ); ?>
	</div><!--sidebar-me-->

	<?php do_action( 'bp_after_sidebar_me' ); ?>

	<?php echo '</div>'; ?>

	<div id="login-help" class="log-box">
		<h4 class="title"><?php esc_html_e( 'Need Help?', 'openlab-theme' ); ?></h4>
		<?php /* translators: 1. help link, 2. contact link */ ?>
		<p class="font-size font-14"><?php printf( 'Visit the <a class="roll-over-loss" href="%1$s">Help section</a> or <a class="roll-over-loss" href="%2$s">contact us</a> with a question.', esc_attr( $help_link ), esc_attr( $contact_link ) ); ?></p>
	</div><!--login-help-->

<?php else : ?>
	<?php echo '<div id="open-lab-join" class="log-box">'; ?>
	<?php echo '<h2 class="title"><span class="fa fa-plus-circle flush-left"></span> ' . esc_html__( 'Sign Up', 'openlab-theme' ) . '</h2>'; ?>
	<?php
	printf(
		'<p><a class="btn btn-default btn-primary link-btn pull-right semibold" href="%s">%s</a> <span class="font-size font-14">%s<br />%s</span></p>',
		esc_attr( bp_get_signup_page() ),
		esc_html__( 'Sign up', 'openlab-theme' ),
		esc_html__( 'Need an account?', 'openlab-theme' ),
		esc_html__( 'Sign Up to become a member!', 'openlab-theme' )
	);
	?>
	<?php echo '</div>'; ?>
	<?php echo '<div id="open-lab-login" class="log-box">'; ?>
	<?php do_action( 'bp_after_sidebar_login_form' ); ?>
	<?php echo '</div>'; ?>

	<div id="user-login" class="log-box">

		<?php echo '<h2 class="title"><span class="fa fa-arrow-circle-right"></span> Log in</h2>'; ?>
		<?php do_action( 'bp_before_sidebar_login_form' ); ?>

		<form name="login-form" class="standard-form" action="<?php echo esc_attr( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
			<label class="sr-only" for="sidebar-user-login"><?php esc_html_e( 'Username', 'openlab-theme' ); ?></label>
			<input class="form-control input" type="text" name="log" id="sidebar-user-login" value="" placeholder="Username" tabindex="0" />

			<label class="sr-only" for="sidebar-user-pass"><?php esc_html_e( 'Password', 'openlab-theme' ); ?></label>
			<input class="form-control input" type="password" name="pwd" id="sidebar-user-pass" value="" placeholder="Password" tabindex="0" />

			<div id="keep-logged-in" class="small-text clearfix">
				<div class="password-wrapper">
					<a class="forgot-password-link small-text roll-over-loss" href="<?php echo site_url( 'wp-login.php?action=lostpassword', 'login' ); ?>"><?php esc_html_e( 'Forgot Password?', 'openlab-theme' ); ?></a>
					<span class="keep-logged-in-checkbox"><input class="no-margin no-margin-top" name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="0" /><label class="regular no-margin no-margin-bottom" for="sidebar-rememberme"><?php esc_html_e( 'Keep me logged in', 'openlab-theme' ); ?></label></span>
				</div>
				<input class="btn btn-default btn-primary link-btn pull-right semibold" type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php esc_html_e( 'Log In', 'openlab-theme' ); ?>" tabindex="0" />
			</div>

			<?php do_action( 'bp_sidebar_login_form' ); ?>

		</form>
	</div>
<?php endif; ?>
