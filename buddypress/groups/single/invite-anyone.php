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

<?php $group_type = cboxol_get_group_group_type( bp_get_current_group_id() ); ?>

<?php if ( ! bp_get_new_group_id() ) : ?>
	<form action="<?php bp_group_permalink( groups_get_current_group() ); ?>/invite-anyone/send/" method="post" class="form-panel" id="send-invite-form">
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

				<p><a class="btn btn-primary no-deco" href="<?php echo esc_attr( bp_loggedin_user_domain() . BP_INVITE_ANYONE_SLUG . '/invite-new-members/group-invites/' . bp_get_group_id() ); ?>"><?php esc_html_e( 'Invite New Members to This Community', 'commons-in-a-box' ); ?></a></p>

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
