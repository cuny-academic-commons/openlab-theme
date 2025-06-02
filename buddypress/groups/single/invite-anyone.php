<?php
/**
 * This template, which powers the group Send Invites tab when IA is enabled, can be overridden
 * with a template file at groups/single/invite-anyone.php
 *
 * @package Invite Anyone
 * @since 0.8.5
 */
?>

<?php do_action( 'bp_before_group_send_invites_content' ); ?>

<?php
$group_type = cboxol_get_group_group_type( bp_get_current_group_id() );

$form_action = bp_get_group_url(
	groups_get_current_group(),
	bp_groups_get_path_chunks( [ 'invite-anyone', 'send' ] )
);
?>

<?php if ( ! bp_get_new_group_id() ) : ?>
	<form action="<?php echo esc_url( $form_action ); ?>" method="post" class="form-panel" id="send-invite-form">
	<?php endif; ?>

	<div id="topgroupinvite" class="panel panel-default">
		<div class="panel-heading semibold"><?php echo esc_html( $group_type->get_label( 'invite_community_members_to_group' ) ); ?></div>
		<div class="panel-body">

			<?php do_action( 'template_notices' ); ?>

			<label><?php echo esc_html( $group_type->get_label( 'search_for_members_to_invite_to_group' ) ); ?>:</label>

			<ul class="first acfb-holder invite-search inline-element-list">
				<li>
					<input type="text" name="send-to-input" class="send-to-input form-control" id="send-to-input" />
				</li>
			</ul>

			<div id="searchinvitemembersdescription">
				<p class="italics"><?php esc_html_e( 'Start typing a few letters of member\'s display name. When a dropdown list appears, select from the list.', 'commons-in-a-box' ); ?></p>

				<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
				<ul id="invite-anyone-invite-list" class="item-list inline-element-list row">
					<?php if ( bp_group_has_invites() ) : ?>

						<?php
						while ( bp_group_invites() ) :
							bp_group_the_invite();
							?>

							<li id="<?php bp_group_invite_item_id(); ?>">
								<?php bp_group_invite_user_avatar(); ?>

								<h4><?php bp_group_invite_user_link(); ?></h4>
								<span class="activity"><?php bp_group_invite_user_last_active(); ?></span>

								<?php do_action( 'bp_group_send_invites_item' ); ?>

								<div class="action">
									<a class="remove" href="<?php bp_group_invite_user_remove_invite_url(); ?>" id="<?php bp_group_invite_item_id(); ?>"><?php esc_html_e( 'Remove Invite', 'commons-in-a-box' ); ?></a>

									<?php do_action( 'bp_group_send_invites_item_action' ); ?>
								</div>
							</li>

						<?php endwhile; ?>

					<?php endif; ?>
				</ul>

			</div>

			<p class="invite-copy italics">
				<?php esc_html_e( 'These members will be sent an invitation.', 'commons-in-a-box' ); ?>

				<?php if ( bp_is_group_create() ) : ?>
					<?php esc_html_e( 'Click \'Finish\' to continue.', 'commons-in-a-box' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'Click the "Send Invites" button to continue.', 'commons-in-a-box' ); ?>
				<?php endif ?>
			</p>

			<?php do_action( 'bp_before_group_send_invites_list' ); ?>

			<?php if ( ! bp_get_new_group_id() ) : ?>
				<div class="submit">
					<input class="btn btn-primary" type="submit" name="submit" id="submit" value="<?php esc_attr_e( 'Send Invites', 'commons-in-a-box' ); ?>" />
				</div>
			<?php endif; ?>

			<?php do_action( 'bp_after_group_send_invites_list' ); ?>
		</div>
	</div>

	<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ); ?>

	<?php if ( invite_anyone_access_test() && ! bp_is_group_create() ) : ?>
		<div class="panel panel-default">
			<div class="panel-heading semibold"><?php esc_html_e( 'Invite new members by email:', 'commons-in-a-box' ); ?></div>
			<div class="panel-body">

				<p class="invite-copy"><?php esc_html_e( 'This link will take you to My Invitations, where you may invite people to join the community and this group.', 'commons-in-a-box' ); ?></p>

				<p><a class="btn btn-primary no-deco" href="<?php echo esc_attr( bp_loggedin_user_url( bp_members_get_path_chunks( [ BP_INVITE_ANYONE_SLUG, 'invite-new-members', 'group-invites', bp_get_group_id() ] ) ) ); ?>"><?php esc_html_e( 'Invite New Members to This Community', 'commons-in-a-box' ); ?></a></p>

			</div>
		</div>
	<?php endif; ?>

	<!-- <div class="clear"></div> -->

	<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ); ?>

	<!-- Don't leave out this sweet field -->
	<?php if ( ! bp_get_new_group_id() ) : ?>
		<input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id(); ?>" />
	<?php else : ?>
		<input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id(); ?>" />
	<?php endif; ?>

	<?php if ( ! bp_get_new_group_id() ) : ?>
	</form>
<?php endif; ?>

<?php do_action( 'bp_after_group_send_invites_content' ); ?>

<?php if ( openlab_user_can_bulk_import_group_members( bp_get_current_group_id(), bp_loggedin_user_id() ) ) : ?>

	<?php
	$import_results = null;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! empty( $_GET['import_id'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$import_id      = intval( wp_unslash( $_GET['import_id'] ) );
		$import_results = groups_get_groupmeta( bp_get_current_group_id(), 'import_' . $import_id );
	}

	$form_action = bp_get_group_url(
		groups_get_current_group(),
		bp_groups_get_path_chunks( [ 'invite-anyone' ] )
	);

	?>

	<?php if ( ! bp_is_group_create() ) : ?>
		<form method="post" id="import-members-form" class="form-panel" action="<?php echo esc_url( $form_action ); ?>">

		<div class="panel panel-default">
			<div class="panel-heading semibold"><?php esc_html_e( 'Import Members', 'commons-in-a-box' ); ?></div>
			<div class="panel-body">

				<?php $show_submit_border = false; ?>

				<?php if ( $import_results ) : ?>
					<?php if ( ! empty( $import_results['success'] ) ) : ?>
						<?php
						$user_links = [];
						foreach ( $import_results['success'] as $success_email ) {
							$success_user = get_user_by( 'email', $success_email );
							if ( ! $success_user ) {
								continue;
							}

							$user_links[] = sprintf(
								'<li><a href="%s">%s</a> (%s)</li>',
								esc_attr( bp_core_get_user_domain( $success_user->ID ) ),
								esc_html( bp_core_get_user_displayname( $success_user->ID ) ),
								esc_html( $success_email )
							);
						}
						?>

						<?php if ( $user_links ) : ?>
							<div class="import-results-section import-results-section-success">
								<p class="invite-copy">
									<?php esc_html_e( 'The following OpenLab members were successfully added.', 'commons-in-a-box' ); ?>
									<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
									<ul><?php echo implode( '', $user_links ); ?></ul>
								</p>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $import_results['illegal_address'] ) ) : ?>
						<?php $show_submit_border = true; ?>
						<div class="import-results-section import-results-section-illegal">
							<p class="invite-copy"><?php esc_html_e( 'The following email addresses are not valid for this community.', 'commons-in-a-box' ); ?></p>

							<label for="illegal-addresses" class="sr-only"><?php esc_html_e( 'Illegal addresses', 'commons-in-a-box' ); ?></label>
							<textarea name="illegal-addresses" class="form-control" id="illegal-addresses"><?php echo esc_textarea( implode( ', ', $import_results['illegal_address'] ) ); ?></textarea>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $import_results['invalid_address'] ) ) : ?>
						<?php
						$invalid = [];
						foreach ( $import_results['invalid_address'] as $invalid_address ) {
							$invalid[] = sprintf(
								'<strong>%s</strong>',
								esc_html( $invalid_address )
							);
						}
						?>

						<?php if ( $invalid ) : ?>
							<?php $show_submit_border = true; ?>
							<div class="import-results-section import-results-section-invalid">
								<p class="invite-copy"><?php esc_html_e( 'The following don\'t appear to be valid email addresses. Please verify and resubmit.', 'commons-in-a-box' ); ?></p>
								<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
								<p class="invite-copy"><?php echo implode( ', ', $invalid ); ?></p>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $import_results['not_found'] ) ) : ?>
						<?php $show_submit_border = true; ?>
						<div class="import-results-section import-results-section-not-found">
							<p class="invite-copy"><?php esc_html_e( 'The following email addresses are valid, but no corresponding community members were found. The link below wil take you to My Invitations > Invite New Members, where you may invite the following to join the community and this group.', 'commons-in-a-box' ); ?></p>

							<?php
							$invite_link = bp_members_get_user_url(
								bp_loggedin_user_id(),
								bp_members_get_path_chunks( [ BP_INVITE_ANYONE_SLUG ] )
							);
							$invite_link = add_query_arg(
								[
									'emails'   => $import_results['not_found'],
									'group_id' => bp_get_current_group_id(),
								],
								$invite_link
							);
							?>

							<p class="invite-new-members-link"><span class="fa fa-chevron-circle-right" aria-hidden="true"></span> <a href="<?php echo esc_attr( $invite_link ); ?>"><?php esc_html_e( 'Invite the following to join the community', 'commons-in-a-box' ); ?></a></p>

							<label for="not-found-addresses" class="sr-only"><?php esc_html_e( 'Addresses not found in the system', 'commons-in-a-box' ); ?></label>
							<textarea name="not-found-addresses" class="form-control" id="not-found-addresses"><?php echo esc_textarea( implode( ', ', $import_results['not_found'] ) ); ?></textarea>
						</div>
					<?php endif; ?>

					<?php
					$submit_border_class    = $show_submit_border ? ' import-results-section-submit-show-border' : '';
					$group_invite_permalink = bp_get_group_url(
						groups_get_current_group(),
						bp_groups_get_path_chunks( [ BP_INVITE_ANYONE_SLUG ] )
					);
					?>

					<div class="import-results-section import-results-section-submit <?php echo esc_attr( $submit_border_class ); ?>">
						<p><a class="btn btn-primary no-deco" href="<?php echo esc_attr( $group_invite_permalink ); ?>"><?php esc_html_e( 'Perform a new import', 'commons-in-a-box' ); ?></a></p>
					</div>

				<?php else : ?>

					<p class="invite-copy"><?php esc_html_e( 'Add community members to this group in bulk by entering a list of email addresses below. Existing community members corresponding to this list will be added automatically to the group and will receive notification via email.', 'commons-in-a-box' ); ?></p>

					<p class="invite-copy import-acknowledge"><label><input type="checkbox" name="import-acknowledge-checkbox" id="import-acknowledge-checkbox" value="1" /> <?php esc_html_e( 'I acknowledge that the following individuals are officially associated with this group or have approved this action.', 'commons-in-a-box' ); ?></label></p>

					<label class="sr-only" for="email-addresses-to-import"><?php esc_html_e( 'Enter email addresses', 'commons-in-a-box' ); ?></label>
					<textarea name="email-addresses-to-import" id="email-addresses-to-import" class="form-control" placeholder="<?php esc_html_e( 'Separate email addresses with commas, or enter one per line.', 'commons-in-a-box' ); ?>"></textarea>

					<p><input type="submit" class="btn btn-primary no-deco" value="<?php esc_attr_e( 'Import', 'commons-in-a-box' ); ?>" /></p>
				<?php endif; ?>
			</div>
		</div>

		<?php wp_nonce_field( 'group_import_members', 'group-import-members-nonce' ); ?>

		</form>
	<?php endif; ?>
<?php endif; ?>
