<?php

/**
 * Login/signup panel on home page.
 *
 * @since 1.0.0
 */

?>

<?php if ( is_user_logged_in() ) :

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

			<?php do_action( 'bp_sidebar_login_form' ) ?>

		</form>
	</div>
<?php endif; ?>
