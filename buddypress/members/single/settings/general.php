<?php
/**
 * Members settings - general
 *
 * */
do_action('bp_before_member_settings_template');
?>

<?php echo openlab_submenu_markup(); ?>


<div id="item-body" role="main">

    <?php do_action('bp_template_content') ?>

    <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form form-panel" id="settings-form">

        <div class="panel panel-default">
            <div class="panel-heading">Update Settings</div>
                <div class="panel-body">
	            	<?php do_action( 'template_notices' ); ?>

        <div class="form-group settings-section username-section">
            <label for="username">Username</label>
            <input class="form-control" type="text" id="username" disabled="disabled" value="<?php bp_displayed_user_username() ?>" />
            <p class="description">Your username cannot be changed.</p>
        </div>

        <div class="form-group settings-section email-section">
            <label for="email_visible">Account Email Address</label>
            <input class="form-control" type="text" name="email_visible" id="email_visible" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" disabled="disabled" />
            <input type="hidden" name="email" value="<?php echo bp_get_displayed_user_email() ?>" />
            <p class="description">Your email address cannot be changed. If your City Tech email address has changed, <a class="underline" href="<?php bp_get_root_domain(); ?>/about/contact-us">contact us</a> for assistance.</p>
        </div>

        <div class="form-group settings-section current-pw-section">
            <label for="pwd">Current Password</label>
            <input class="form-control" type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" />

	    <?php
	    $selectable_types = cboxol_get_selectable_member_types_for_user( bp_displayed_user_id() );
	    $current_type = bp_get_member_type( bp_displayed_user_id() );

	    $selectable_types_plus_current = array_merge( array( $current_type ), $selectable_types );
	    $selectable_type_objects = array_map( 'cboxol_get_member_type', $selectable_types_plus_current );
	    ?>

	    <p class="description">
		    <?php if ( $selectable_types ) : ?>
			    <?php esc_html_e( 'Required to change account type, current password, first name, or last name.' ); ?>
		    <?php else : ?>
			    <?php esc_html_e( 'Required to change current password, first name, or last name.' ); ?>
		    <?php endif; ?>

		    <a class="underline" href="<?php echo site_url( add_query_arg( array( 'action' => 'lostpassword' ), 'wp-login.php' ), 'login' ); ?>" title="<?php _e( 'Password Lost and Found', 'openlab-theme' ); ?>"><?php _e( 'Lost your password?', 'openlab-theme' ); ?></a>
	    </p>
        </div>

        <div class="form-group settings-section change-pw-section">
            <label for="pass1">Change Password</label>
            <input class="form-control" type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small" />

            <label for="pass1">Confirm Change Password</label>
            <input class="form-control" type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small" />

            <p class="description">Leave blank for no change</p>
        </div>

        <div class="form-group settings-section name-section">
            <label for="fname">First Name (required)</label>
            <input class="form-control" type="text" name="fname" id="fname" value="<?php echo bp_get_profile_field_data(array('field' => 'First Name')) ?>" />

            <label for="lname">Last Name (required)</label>
            <input class="form-control" type="text" name="lname" id="lname" value="<?php echo bp_get_profile_field_data(array('field' => 'Last Name')) ?>" />
        </div>

	<?php if ( $selectable_types ) : ?>
            <div class="form-group settings-section account-type-section">
                <label for="account_type">Account Type</label>
                <select class="form-control" name="account_type">
			<?php foreach ( $selectable_type_objects as $selectable_type ) : ?>
				<option value="<?php echo esc_attr( $selectable_type->get_slug() ); ?>" <?php selected( $current_type, $selectable_type->get_slug() ) ?>><?php echo esc_html( $selectable_type->get_label( 'singular' ) ); ?></option>
			<?php endforeach; ?>
		</select>
            </div>
	<?php endif; ?>

            </div>
        </div>
        <?php do_action('bp_core_general_settings_before_submit'); ?>

        <div class="submit">
            <input class="btn btn-primary btn-margin btn-margin-top" type="submit" name="submit" value="<?php _e('Save Changes', 'buddypress'); ?>" id="submit" class="auto" />
        </div>

        <?php do_action('bp_core_general_settings_after_submit');
        wp_nonce_field('bp_settings_general');
        ?>
    </form>
<?php do_action('bp_after_member_body'); ?>
</div><!-- #item-body -->
<?php
do_action('bp_after_member_settings_template');
