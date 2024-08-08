<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php echo openlab_submenu_markup(); ?>

<?php
if ( bp_get_user_has_avatar() ) {
	$avatar_url = bp_core_fetch_avatar(
		array(
			'item_id' => bp_displayed_user_id(),
			'object'  => 'user',
			'type'    => 'full',
			'html'    => false,
		)
	);
} else {
	$avatar_url = get_template_directory_uri() . '/images/avatar_blank.png';
}
?>

<div id="item-body" role="main">
<?php do_action( 'bp_before_profile_avatar_upload_content' ); ?>

<?php if ( ! (int) bp_get_option( 'bp-disable-avatar-uploads' ) ) : ?>

	<form action="" method="post" id="avatar-upload-form" enctype="multipart/form-data" class="form-inline form-panel">

		<?php if ( 'upload-image' === bp_get_avatar_admin_step() ) : ?>
			<div class="panel panel-default">
				<div class="panel-heading"><?php esc_html_e( 'Upload Avatar', 'commons-in-a-box' ); ?></div>
				<div class="panel-body">
					<?php do_action( 'template_notices' ); ?>
					<div class="row">
						<div class="col-sm-8">
							<div id="avatar-wrapper">
								<div class="padded-img">
									<img class="img-responsive padded" src="<?php echo esc_attr( $avatar_url ); ?>" alt="" />
								</div>
							</div>
						</div>
						<div class="col-sm-16">

							<p class="italics"><?php esc_html_e( 'Your avatar will be used on your profile and throughout the site. If there is a Gravatar associated with your account email we will use that, or you can upload an image from your computer. Click below to select a JPG, GIF or PNG format photo from your computer and then click "Upload Image" to proceed.', 'buddypress' ); ?></p>

							<p id="avatar-upload">
								<div class="form-group form-inline">
									<div class="form-control type-file-wrapper">
										<input type="file" name="file" id="file" />
									</div>

									<input class="btn btn-primary top-align" type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'buddypress' ); ?>" />
									<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
								</div>
							</p>

							<?php if ( bp_get_user_has_avatar() ) : ?>
								<p class="italics"><?php esc_html_e( "If you'd like to delete your current avatar but not upload a new one, please use the delete avatar button.", 'buddypress' ); ?></p>
								<a class="btn btn-primary no-deco" href="<?php bp_avatar_delete_link(); ?>" title="<?php esc_attr_e( 'Delete Avatar', 'buddypress' ); ?>"><?php esc_html_e( 'Delete My Avatar', 'buddypress' ); ?></a>
							<?php endif; ?>

							<?php wp_nonce_field( 'bp_avatar_upload' ); ?>
						</div>
					</div>
				</div>
			</div>

			<?php wp_enqueue_script( 'openlab-avatar-privacy' ); ?>

			<div class="panel panel-default panel-avatar-privacy" id="panel-avatar-privacy">
				<div class="panel-heading"><?php esc_html_e( 'Avatar Privacy', 'commons-in-box' ); ?></div>

				<div class="panel-body">
					<fieldset>
						<legend><?php esc_html_e( 'Who can see your avatar?', 'commons-in-box' ); ?></legend>

						<div class="radio">
							<?php foreach ( bp_xprofile_get_visibility_levels() as $level ) : ?>
								<label for="avatar-visibility-level-<?php echo esc_attr( $level['id'] ); ?>">
									<input type="radio" class="avatar-visibility-radio" name="avatar-privacy" id="avatar-visibility-level-<?php echo esc_attr( $level['id'] ); ?>" value="<?php echo esc_attr( $level['id'] ); ?>"<?php checked( cboxol_get_user_avatar_visibility() === $level['id'] ); ?> />
									<?php echo esc_html( $level['label'] ); ?>
								</label>

							<?php endforeach; ?>
						</div>
					</fieldset>

					<input type="hidden" id="avatar-privacy-user-id" value="<?php echo esc_attr( bp_displayed_user_id() ); ?>" />

					<?php wp_nonce_field( 'openlab_avatar_privacy', 'openlab-avatar-privacy-nonce' ); ?>
				</div>
			</div>

		<?php endif; ?>

		<?php if ( 'crop-image' === bp_get_avatar_admin_step() ) : ?>
			<div class="panel panel-default">

				<div class="panel-heading"><?php esc_html_e( 'Crop Avatar', 'commons-in-a-box' ); ?></div>
				<div class="panel-body">
					<?php do_action( 'template_notices' ); ?>

					<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_html_e( 'Avatar to crop', 'buddypress' ); ?>" />

					<div id="avatar-crop-pane">
						<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_html_e( 'Avatar preview', 'buddypress' ); ?>" />
					</div>

					<input class="btn btn-primary" type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_html_e( 'Crop Image', 'buddypress' ); ?>" />

					<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
					<input type="hidden" id="x" name="x" />
					<input type="hidden" id="y" name="y" />
					<input type="hidden" id="w" name="w" />
					<input type="hidden" id="h" name="h" />

					<?php wp_nonce_field( 'bp_avatar_cropstore' ); ?>
				</div>
			</div>

		<?php endif; ?>
	</form>

<?php else : ?>

	<?php // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?>
	<p><?php _e( 'Your avatar will be used on your profile and throughout the site. To change your avatar, please create an account with <a href="http://gravatar.com">Gravatar</a> using the same email address as you used to register with this site.', 'buddypress' ); ?></p>

<?php endif; ?>
</div>
<?php do_action( 'bp_after_profile_avatar_upload_content' ); ?>
