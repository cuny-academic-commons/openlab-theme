<?php $link_data = openlab_process_footer_links(); ?>

<h2><?php esc_html_e( 'Footer Links', 'commons-in-a-box' ); ?></h2>

<?php if ( ! empty( $link_data['error_message'] ) ) : ?>

	<div id="message" class="notice notice-warning"><p><?php echo esc_html( $link_data['error_message'] ); ?></p></div>

<?php elseif ( ! empty( $link_data['sucess_message'] ) ) : ?>

	<div id="message" class="notice notice-success"><p><?php echo esc_html( $link_data['sucess_message'] ); ?></p></div>

<?php endif; ?>

<div class="form-wrap">
	<form id="updateFooterLinks" method="post" action="<?php echo esc_attr( admin_url( 'edit.php?post_type=help&page=help-footer-links' ) ); ?>">
		<div class="ui-widget form-field">
			<label for="accessibility_info"><?php esc_html_e( 'Link for Accessibility info:', 'commons-in-a-box' ); ?></label>
			<input class="help-post-autocomplete" size="30" id="accessibility_info" type="text" name="accessibility_info_name" data-target="accessibility_info_val" value="<?php echo isset( $link_data['accessibility_info_title'] ) ? esc_html( $link_data['accessibility_info_title'] ) : ''; ?>"/>
			<p><?php esc_html_e( 'Set this field to assign a Help Post as the accessibility info link in the network footer. This is an autocomplete field, so just begin typing to see a list of possible Help posts.', 'commons-in-a-box' ); ?></p>
		</div>

		<input id="accessibility_info_val" name="accessibility_info_val" type="hidden" value="<?php echo isset( $link_data['accessibility_info_id'] ) ? esc_html( $link_data['accessibility_info_id'] ) : 0; ?>" />
		<p class="submit"><input type="submit" name="submit" id="submitLinks" class="button button-primary" value="<?php esc_attr_e( 'Update Footer Links', 'commons-in-a-box' ); ?>"></p>
	</form>
</div>
