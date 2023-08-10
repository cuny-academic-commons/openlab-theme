<?php
/**
 * Create a group.
 */
?>

<div class="col-sm-18">

	<?php

	$gt = '';
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( ! empty( $_GET['group_type'] ) ) {
		$gt = wp_unslash( urldecode( $_GET['group_type'] ) );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	// @todo Redirect away if course and user cannot create courses.
	if ( ! bp_get_current_group_id() ) {
		$group_type = cboxol_get_group_type( $gt );
	} else {
		$group_type = cboxol_get_group_group_type( bp_get_current_group_id() );
	}

	if ( is_wp_error( $group_type ) ) {
		$group_types = cboxol_get_group_types(
			array(
				'exclude_portfolio' => true,
			)
		);
		$group_type  = reset( $group_types );
	}

	$the_group              = null;
	$the_description        = '';
	$the_group_clone_source = null;
	if ( groups_get_current_group() ) {
		$the_group              = groups_get_current_group();
		$the_description        = $the_group->description;
		$the_group_clone_source = groups_get_groupmeta( $the_group->id, 'clone_source_group_id', true );
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$group_id_to_clone = 0;
	if ( $group_type->get_can_be_cloned() && ! empty( $_GET['clone'] ) ) {
		$group_id_to_clone = intval( $_GET['clone'] );
	} elseif ( $the_group_clone_source ) {
		$group_id_to_clone = intval( $the_group_clone_source );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$is_shared_clone = false;
	if ( $group_id_to_clone ) {
		$source_group_admin_ids = cboxol_get_all_group_contact_ids( $group_id_to_clone );
		if ( ! in_array( bp_loggedin_user_id(), $source_group_admin_ids, true ) ) {
			$is_shared_clone = true;
		}
	}

	openlab_group_admin_js_data( $group_type );
	?>

	<div class="entry-title">
		<h1 class="mol-title"><?php echo esc_html( $group_type->get_label( 'create_clone_item' ) ); ?></h1>
	</div>

	<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php echo openlab_create_group_menu( $group_type ); ?>

	<div id="single-course-body" class="<?php echo ( $group_type->get_is_course() ? 'course-create' : '' ); ?>">
		<div id="openlab-main-content"></div>

		<form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form form-panel" enctype="multipart/form-data">

			<?php do_action( 'bp_before_create_group' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<input type="hidden" id="new-group-type" value="<?php echo esc_attr( $group_type->get_slug() ); ?>" />

			<?php if ( $the_group ) : ?>
				<input type="hidden" name="existing-group-id" value="<?php echo esc_attr( $the_group->id ); ?>" />
			<?php endif; ?>

			<?php /* Group creation step 1: Basic group details */ ?>
			<?php if ( bp_is_group_creation_step( 'group-details' ) ) : ?>

				<p class="group-create-help-text"><?php echo esc_html( $group_type->get_label( 'create_item_help_text' ) ); ?></p>

				<?php do_action( 'bp_before_group_details_creation_step' ); ?>

				<?php /* Create vs Clone for Courses */ ?>
				<?php if ( $group_type->get_can_be_cloned() ) : ?>
					<div class="panel panel-default create-or-clone-selector">
						<div class="panel-heading semibold"><?php esc_html_e( 'Create New or Clone Existing?', 'commons-in-a-box' ); ?></div>
						<div class="panel-body">
							<?php
							/* @todo Rephrase?
							<p class="ol-tooltip clone-course-tooltip" id="clone-course-tooltip-2">If you taught the same course in a previous semester or year, cloning can save you time.</p>
							*/
							?>

							<ul class="create-or-clone-options">
								<li class="radio">
									<label for="create-or-clone-create"><input type="radio" name="create-or-clone" id="create-or-clone-create" value="create" <?php checked( ! (bool) $group_id_to_clone ); ?> /><?php esc_html_e( 'Create New', 'commons-in-a-box' ); ?></label>
								</li>

								<?php
								// Only show 'Existing' field if there's something to clone.
								$group_args = array(
									'show_hidden' => true,
									'user_id'     => bp_loggedin_user_id(),
									'group_type'  => $group_type->get_slug(),
									'clone_id'    => $group_id_to_clone,
								);

								$groups_of_type = openlab_get_groups_owned_by_user( $group_args );
								?>

								<li class="disable-if-js form-group radio form-inline">
									<label for="create-or-clone-clone" <?php echo ( $groups_of_type['total'] < 1 ? 'class="disabled-opt"' : '' ); ?>><input type="radio" name="create-or-clone" id="create-or-clone-clone" value="clone" <?php checked( (bool) $group_id_to_clone ); ?> <?php echo ( $groups_of_type['total'] < 1 ? 'disabled' : '' ); ?> /><?php esc_html_e( 'Clone Existing', 'commons-in-a-box' ); ?></label>

									<label class="sr-only" for="group-to-clone"><?php esc_html_e( 'Choose Clone Source', 'commons-in-a-box' ); ?></label>
									<select class="form-control" id="group-to-clone" name="group-to-clone">
										<option value="" <?php selected( $group_id_to_clone, 0 ); ?>>-</option>

										<?php foreach ( $groups_of_type['groups'] as $user_group ) : ?>
											<option value="<?php echo esc_attr( $user_group->id ); ?>" <?php selected( $group_id_to_clone, $user_group->id ); ?>><?php echo esc_attr( $user_group->name ); ?></option>
										<?php endforeach ?>
									</select>
								</li>
							</ul>

							<p class="ol-clone-description italics" id="ol-clone-description"><?php esc_html_e( 'Note: The profile, site set-up, and all docs, files, discussions, posts, and pages created by admins will be copied to the clone. Membership or member-created documents, files, discussions, comments or posts will not be copied.', 'commons-in-a-box' ); ?></p>

							<?php $authorship_settings_clone_class = $is_shared_clone ? '' : 'hidden'; ?>
							<div id="shared-cloning-authorship-settings" class="shared-cloning-authorship-settings <?php echo esc_attr( $authorship_settings_clone_class ); ?>">
								<p><?php esc_html_e( 'Shared cloning is enabled for the group you are cloning. The author for all materials copied will be switched to you, unless you uncheck the box below:', 'commons-in-a-box' ); ?></p>
								<input type="checkbox" <?php checked( $is_shared_clone ); ?> name="change-cloned-content-attribution" id="change-cloned-content-attribution" value="1" /> <label for="change-cloned-content-attribution"><?php esc_html_e( 'Switch author to cloner (recommended)', 'commons-in-a-box' ); ?></label>
							</div>

						</div>
					</div>

				<?php endif; ?>

				<?php /* Name/Description */ ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold">
						<label for="group-name"><?php esc_html_e( 'Name', 'commons-in-a-box' ); ?> <?php esc_html_e( '(required)', 'commons-in-a-box' ); ?></label>
					</div>

					<div class="panel-body">

						<?php if ( $group_type->get_is_portfolio() ) : ?>
							<p class="ol-tooltip"><?php echo esc_html( $group_type->get_label( 'name_help_text' ) ); ?></p>

							<ul class="ol-tooltip">
								<li><?php esc_html_e( 'FirstName LastName\'s Portfolio', 'commons-in-a-box' ); ?></li>
								<li><?php esc_html_e( 'Jane Smith\'s Portfolio (Example)', 'commons-in-a-box' ); ?></li>
							</ul>

							<input class="form-control" size="80" type="text" name="group-name" id="group-name" value="<?php echo esc_attr( bp_get_new_group_name() ); ?>" required />

						<?php else : ?>
							<p class="ol-tooltip"><?php echo esc_html( $group_type->get_label( 'name_help_text' ) ); ?></p>
							<input class="form-control" size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name(); ?>" required />

						<?php endif ?>
					</div><!-- /.panel-body -->
				</div>

				<?php openlab_group_url_markup(); ?>

				<?php openlab_group_avatar_markup(); ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><label for="group-desc"><?php esc_html_e( 'Description', 'commons-in-a-box' ); ?> <?php esc_html_e( '(required)', 'commons-in-a-box' ); ?></label></div>
					<div class="panel-body">
						<textarea class="form-control" name="group-desc" id="group-desc" required><?php echo esc_textarea( $the_description ); ?></textarea>
					</div>
				</div>

				<?php if ( ! $group_type->get_is_portfolio() ) : ?>
					<?php openlab_group_sharing_settings_markup( $group_type ); ?>
				<?php endif; ?>

				<?php do_action( 'bp_after_group_details_creation_step' ); ?>

				<?php wp_nonce_field( 'groups_create_save_group-details' ); ?>

			<?php endif; ?>

			<?php /* Group creation step 2: Associated site */ ?>
			<?php if ( bp_is_group_creation_step( 'site-details' ) ) : ?>

				<p class="group-create-help-text"><?php echo esc_html( $group_type->get_label( 'site_help_text' ) ); ?></p>

				<?php openlab_group_site_markup(); ?>

				<?php do_action( 'openlab_after_group_site_markup' ); ?>

				<?php openlab_group_site_privacy_settings_markup(); ?>
				<?php openlab_group_site_member_role_settings_markup(); ?>

				<?php
				/**
				 * Fires after the 'Member Role Settings' on the Site step of group creation.
				 *
				 * @since 1.5.0
				 */
				do_action( 'openlab_group_creation_after_group_site_settings' );
				?>

				<?php wp_nonce_field( 'groups_create_save_site-details' ); ?>

			<?php endif; ?>

			<?php do_action( 'groups_custom_create_steps' ); // Allow plugins to add custom group creation steps ?>

			<?php do_action( 'bp_before_group_creation_step_buttons' ); ?>

			<?php /* Previous Button */ ?>
			<?php if ( ! bp_is_first_group_creation_step() && 'group-settings' !== bp_get_groups_current_create_step() ) : ?>
				<input class="btn btn-primary prev-btn btn-margin btn-margin-top" type="button" value="&#xf137; <?php esc_html_e( 'Previous Step', 'commons-in-a-box' ); ?>" id="group-creation-previous" name="previous" onclick="location.href = '<?php bp_group_creation_previous_link(); ?>'" />
			<?php endif; ?>

			<?php /* Next Button */ ?>
			<?php if ( ! bp_is_last_group_creation_step() && ! bp_is_first_group_creation_step() ) : ?>
				<input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php esc_html_e( 'Next Step', 'commons-in-a-box' ); ?> &#xf138;" id="group-creation-next" name="save" />
			<?php endif; ?>

			<?php /* Create Button */ ?>
			<?php if ( bp_is_first_group_creation_step() ) : ?>
				<input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php esc_html_e( 'Create and Continue', 'commons-in-a-box' ); ?> &#xf138;" id="group-creation-create" name="save" />
			<?php endif; ?>

			<?php /* Finish Button */ ?>
			<?php if ( bp_is_last_group_creation_step() ) : ?>
				<input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php esc_attr_e( 'Finish', 'commons-in-a-box' ); ?> &#xf138;" id="group-creation-finish" name="save" />
			<?php endif; ?>

			<?php do_action( 'bp_after_group_creation_step_buttons' ); ?>

			<?php /* Don't leave out this hidden field */ ?>
			<input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id(); ?>" />

<?php do_action( 'bp_directory_groups_content' ); ?>

<?php do_action( 'bp_after_create_group' ); ?>

		</form>
	</div>
</div>
<?php get_template_part( 'parts/sidebar/members' ); ?>
