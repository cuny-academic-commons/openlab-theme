<?php
/**
 * Create a group.
 */
?>

<div class="col-sm-18">

	<?php

	$gt = '';
	if ( ! empty( $_GET['group_type'] ) ) {
		$gt = wp_unslash( urldecode( $_GET['group_type'] ) );
	}

	// @todo Redirect away if course and user cannot create courses.
	$group_type = cboxol_get_group_type( $gt );
	if ( is_wp_error( $group_type ) ) {
		$group_types = cboxol_get_group_types( array(
			'exclude_portfolio' => true,
		) );
		$group_type = reset( $group_types );
	}

	$group_id_to_clone = 0;
	if ( $group_type->get_is_course() && ! empty( $_GET['clone'] ) ) {
		$group_id_to_clone = intval( $_GET['clone'] );
	}

	openlab_group_admin_js_data( $group_type ); ?>

	<?php /* @todo this can't translate */ ?>
	<h1 class="entry-title mol-title"><?php echo esc_html( $group_type->get_label( 'create_clone_item' ) ); ?></h1>

	<?php echo openlab_create_group_menu( $group_type ); ?>

	<p class="group-create-help-text"><?php echo esc_html( $group_type->get_label( 'create_item_help_text' ) ); ?></p>

	<div id="single-course-body" class="<?php echo ( $group_type->get_is_course() ? 'course-create' : '' ); ?>">
		<div id="openlab-main-content"></div>

		<form action="<?php bp_group_creation_form_action() ?>" method="post" id="create-group-form" class="standard-form form-panel" enctype="multipart/form-data">

			<?php do_action( 'bp_before_create_group' ) ?>

			<?php do_action( 'template_notices' ) ?>

			<input type="hidden" id="new-group-type" value="<?php echo esc_attr( $group_type->get_slug() ); ?>" />

			<?php /* Group creation step 1: Basic group details */ ?>
			<?php if ( bp_is_group_creation_step( 'group-details' ) ) : ?>

				<?php do_action( 'bp_before_group_details_creation_step' ); ?>

				<?php /* Create vs Clone for Courses */ ?>
				<?php if ( $group_type->get_can_be_cloned() ) : ?>
					<div class="panel panel-default create-or-clone-selector">
						<div class="panel-heading semibold"><?php esc_html_e( 'Create New or Clone Existing?', 'openlab-theme' ); ?></div>
						<div class="panel-body">
						<?php /* @todo Rephrase?
						<p class="ol-tooltip clone-course-tooltip" id="clone-course-tooltip-2">If you taught the same course in a previous semester or year, cloning can save you time.</p>
						*/ ?>

						<ul class="create-or-clone-options">
							<li class="radio">
								<label for="create-or-clone-create"><input type="radio" name="create-or-clone" id="create-or-clone-create" value="create" <?php checked( ! (bool) $group_id_to_clone ) ?> /><?php esc_html_e( 'Create New', 'openlab-theme' ); ?></label>
							</li>

							<?php
							// Only show 'Existing' field if there's something to clone.
							$group_args = array(
								'show_hidden' => true,
								'user_id' => bp_loggedin_user_id(),
								'group_type' => $group_type->get_slug(),
							);

							$groups_of_type = groups_get_groups( $group_args );

							?>

							<li class="disable-if-js form-group radio form-inline">
								<label for="create-or-clone-clone" <?php echo ( $groups_of_type['total'] < 1 ? 'class="disabled-opt"' : '' ); ?>><input type="radio" name="create-or-clone" id="create-or-clone-clone" value="clone" <?php checked( (bool) $group_id_to_clone ) ?> <?php echo ( $groups_of_type['total'] < 1 ? 'disabled' : '' ); ?> /><?php esc_html_e( 'Clone Existing', 'openlab-theme' ) ?></label>

								<label class="sr-only" for="group-to-clone"><?php esc_html_e( 'Choose Clone Source', 'openlab-theme' ); ?></label>
								<select class="form-control" id="group-to-clone" name="group-to-clone">
									<option value="" <?php selected( $group_id_to_clone, 0 ) ?>>-</option>

									<?php foreach ( $groups_of_type['groups'] as $user_group ) : ?>
										<option value="<?php echo esc_attr( $user_group->id ) ?>" <?php selected( $group_id_to_clone, $user_group->id ) ?>><?php echo esc_attr( $user_group->name ) ?></option>
									<?php endforeach ?>
								</select>
							</li>
						</ul>

						<p class="ol-clone-description italics" id="ol-clone-description"><?php echo esc_html( $group_type->get_label( 'clone_help_text' ) ); ?></p>
						</div>
					</div>

				<?php endif; ?>

				<?php /* Name/Description */ ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold">
						<label for="group-name"><?php esc_html_e( 'Name', 'openlab-theme' ); ?> <?php _e( '(required)', 'openlab-theme' ) ?></label>
					</div>

					<div class="panel-body">

						<?php if ( $group_type->get_is_portfolio() ) : ?>
							<p class="ol-tooltip"><?php echo esc_html( $group_type->get_label( 'name_help_text' ) ); ?></p>

							<ul class="ol-tooltip">
								<li><?php esc_html_e( 'FirstName LastName\'s Portfolio', 'openlab-theme' ); ?></li>
								<li><?php esc_html_e( 'Jane Smith\'s Portfolio (Example)', 'openlab-theme' ); ?></li>
							</ul>

							<input class="form-control" size="80" type="text" name="group-name" id="group-name" value="<?php echo esc_attr( bp_get_new_group_name() ) ?>" required />

						<?php else : ?>
							<p class="ol-tooltip"><?php echo esc_html( $group_type->get_label( 'name_help_text' ) ); ?></p>
							<input class="form-control" size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" required />

						<?php endif ?>
					</div><!-- /.panel-body -->
				</div>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><label for="group-desc"><?php esc_html_e( 'Description', 'openlab-theme' ); ?> <?php esc_html_e( '(required)', 'openlab-theme' ) ?></label></div>
					<div class="panel-body">
						<textarea class="form-control" name="group-desc" id="group-desc" required><?php bp_new_group_description() ?></textarea>
					</div>
				</div>

				<?php do_action( 'bp_after_group_details_creation_step' ) ?>

				<?php openlab_group_privacy_settings_markup(); ?>

				<?php wp_nonce_field( 'groups_create_save_group-details' ) ?>

			<?php endif; ?>

			<?php /* Group creation step 2: Group settings */ ?>
			<?php if ( bp_is_group_creation_step( 'group-settings' ) ) : ?>

				<?php do_action( 'bp_before_group_settings_creation_step' ); ?>

				<?php if ( function_exists( 'bbpress' ) && ! cboxol_is_portfolio() ) : ?>
					<input type="hidden" name="group-show-forum" value="1" />
				<?php endif; ?>

				<?php openlab_group_privacy_settings( $group_type ); ?>

			<?php endif; ?>

			<?php /* Group creation step 3: Avatar Uploads */ ?>

			<?php if ( bp_is_group_creation_step( 'group-avatar' ) ) : ?>

				<?php do_action( 'bp_before_group_avatar_creation_step' ); ?>

				<?php if ( ! bp_get_avatar_admin_step() || 'upload-image' == bp_get_avatar_admin_step() ) : ?>

					<div class="panel panel-default">
						<div class="panel-heading"><?php esc_html_e( 'Upload Avatar', 'openlab-theme' ); ?></div>

						<div class="panel-body">
							<div class="row">
								<div class="col-sm-8">
									<div id="avatar-wrapper">
										<?php bp_new_group_avatar() ?>
									</div>
								</div>
								<div class="col-sm-16">

									<p class="italics"><?php esc_html_e( 'Upload an image to use as an avatar for this group. The image will be shown on the group Profile page, and in search results.', 'openlab-theme' ) ?></p>

									<p id="avatar-upload">
										<div class="form-group form-inline">
												<div class="form-control type-file-wrapper">
													<input type="file" name="file" id="file" />
												</div>
												<input class="btn btn-primary top-align" type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
												<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
										</div>
									</p>

									<p class="italics"><?php esc_html_e( 'To skip the avatar upload process, click the "Next Step" button.', 'openlab-theme' ); ?></p>
								</div>
							</div>
						</div>
					</div>

					<?php endif; ?>

					<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

						<div class="panel panel-default">
						<div class="panel-heading">Crop Avatar</div>
						<div class="panel-body">

							<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ) ?>" />

							<div id="avatar-crop-pane">
								<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ) ?>" />
							</div>

							<input class="btn btn-primary" type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ) ?>" />

							<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
							<input type="hidden" name="upload" id="upload" />
							<input type="hidden" id="x" name="x" />
							<input type="hidden" id="y" name="y" />
							<input type="hidden" id="w" name="w" />
							<input type="hidden" id="h" name="h" />

						</div>
						</div>

					<?php endif; ?>

					<?php do_action( 'bp_after_group_avatar_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-avatar' ) ?>

				<?php endif; ?>

				<?php /* Group creation step 4: Invite friends to group */ ?>
				<?php if ( bp_is_group_creation_step( 'group-invites' ) ) : ?>

					<?php do_action( 'bp_before_group_invites_creation_step' ); ?>

				<?php if ( function_exists( 'bp_get_total_friend_count' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
						<div class="left-menu">
							<div id="invite-list">
								<ul>
									<?php bp_new_group_invite_friend_list() ?>
								</ul>

								<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ); ?>
							</div>
						</div><!-- .left-menu -->

						<div class="main-column">

							<div id="message" class="info">
								<p><?php _e( 'Select people to invite from your friends list.', 'openlab-theme' ); ?></p>
							</div>

								<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
							<ul id="friend-list" class="item-list">
								<?php if ( bp_group_has_invites() ) : ?>
									<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

										<li id="<?php bp_group_invite_item_id() ?>">
											<?php bp_group_invite_user_avatar() ?>

											<h4><?php bp_group_invite_user_link() ?></h4>
											<span class="activity"><?php bp_group_invite_user_last_active() ?></span>

											<div class="action">
												<a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e( 'Remove Invite', 'openlab-theme' ) ?></a>
											</div>
										</li>

									<?php endwhile; ?>

									<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ) ?>
								<?php endif; ?>
							</ul>

						</div><!-- .main-column -->

					<?php else : ?>

						<div id="message" class="info">
							<p><?php esc_html_e( 'Once you have built up friend connections you will be able to invite others. You can send invites any time in the future by selecting the "Send Invites" option when viewing the group Profile.', 'openlab-theme' ); ?></p>
						</div>

					<?php endif; ?>

					<?php wp_nonce_field( 'groups_create_save_group-invites' ) ?>
					<?php do_action( 'bp_after_group_invites_creation_step' ); ?>

				<?php endif; ?>

				<?php do_action( 'groups_custom_create_steps' ) // Allow plugins to add custom group creation steps  ?>

				<?php do_action( 'bp_before_group_creation_step_buttons' ); ?>

					<?php if ( 'crop-image' != bp_get_avatar_admin_step() ) : ?>
						<?php /* Previous Button */ ?>
						<?php if ( ! bp_is_first_group_creation_step() && 'group-settings' !== bp_get_groups_current_create_step() ) : ?>
							<input class="btn btn-primary prev-btn btn-margin btn-margin-top" type="button" value="&#xf137; <?php _e( 'Previous Step', 'buddypress' ) ?>" id="group-creation-previous" name="previous" onclick="location.href = '<?php bp_group_creation_previous_link() ?>'" />
						<?php endif; ?>

						<?php /* Next Button */ ?>
						<?php if ( ! bp_is_last_group_creation_step() && ! bp_is_first_group_creation_step() ) : ?>
							<input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php _e( 'Next Step', 'openlab-theme' ) ?> &#xf138;" id="group-creation-next" name="save" />
						<?php endif; ?>

						<?php /* Create Button */ ?>
						<?php if ( bp_is_first_group_creation_step() ) : ?>
							<input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php _e( 'Create and Continue', 'openlab-theme' ); ?> &#xf138;" id="group-creation-create" name="save" />
						<?php endif; ?>

						<?php /* Finish Button */ ?>
						<?php if ( bp_is_last_group_creation_step() ) : ?>
							<input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php _e( 'Finish', 'openlab-theme' ) ?> &#xf138;" id="group-creation-finish" name="save" />
					<?php endif; ?>
				<?php endif; ?>

				<?php do_action( 'bp_after_group_creation_step_buttons' ); ?>

<?php /* Don't leave out this hidden field */ ?>
				<input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" />

<?php do_action( 'bp_directory_groups_content' ) ?>

<?php do_action( 'bp_after_create_group' ) ?>

		</form>
	</div>
</div>
<?php openlab_bp_sidebar( 'members' ); ?>
