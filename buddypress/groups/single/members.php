<?php

$members_args = [];

// Get private users of the group
$private_users = openlab_get_private_members_of_group( bp_get_current_group_id() );

// If user is not mod and there are private users, exclude them from the list
if ( ! current_user_can( 'bp_moderate' ) && ! empty( $private_users ) ) {
	$members_args['exclude'] = $private_users;
}

// Don't exclude admins from the list.
$members_args['exclude_admins_mods'] = 0;
?>

<?php if ( bp_group_has_members( $members_args ) ) : ?>

	<?php do_action( 'bp_before_group_members_content' ); ?>
	<div class="row">
		<div class="submenu col-sm-16">
			<ul class="nav nav-inline">
				<?php openlab_group_membership_tabs(); ?>
			</ul>
		</div><!-- .submenu -->

		<div id="member-count" class="pag-count col-sm-8 align-right">
			<?php bp_group_member_pagination_count(); ?>
		</div>

	</div>

	<?php do_action( 'bp_before_group_members_list' ); ?>

	<div id="group-members-list" class="item-list group-members group-list clearfix">
		<?php
		while ( bp_group_members() ) :
			bp_group_the_member();

			$user_avatar = bp_core_fetch_avatar(
				array(
					'item_id' => bp_get_member_user_id(),
					'object'  => 'user',
					'type'    => 'full',
					'html'    => false,
				)
			);
			?>

			<div class="group-item col-md-8 col-xs-12">
				<div class="group-item-wrapper">
					<div class="row">
						<div class="item-avatar col-md-9 col-xs-7">
							<?php /* translators: %s: member name */ ?>
							<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $user_avatar ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Avatar for %s', 'openlab-theme' ), bp_get_group_member_name() ) ); ?>"/></a>
						</div>

						<div class="item col-md-15 col-xs-17">
							<p class="h5">
								<a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_member_permalink(); ?>" data-basevalue="28" data-minvalue="20" data-basewidth="152"><?php bp_member_name(); ?></a><span class="original-copy hidden"><?php bp_member_name(); ?></span>
							</p>

							<span class="activity"><?php openlab_group_member_joined_since(); ?></span>

							<?php // Only logged-in non-moderators see "Hide my membership". ?>
							<?php if ( ( bp_get_member_user_id() === bp_loggedin_user_id() ) && ! current_user_can( 'bp_moderate' ) ) : ?>
								<div class="group-item-membership-privacy">
									<label>
										<input type="checkbox" name="membership_privacy" id="membership_privacy" data-group_id="<?php echo esc_attr( bp_get_current_group_id() ); ?>" value="<?php echo esc_attr( bp_loggedin_user_id() ); ?>" <?php checked( openlab_is_my_membership_private( bp_get_current_group_id() ) ); ?> /> <?php esc_html_e( 'Hide my membership', 'commons-in-a-box' ); ?>
										<?php wp_nonce_field( 'openlab_hide_membership_' . bp_get_current_group_id(), 'openlab_hide_membership_nonce_' . bp_get_current_group_id(), false, true ); ?>
									</label>
								</div>
							<?php endif; ?>

							<?php // Moderators see hidden membership label. ?>
							<?php if ( current_user_can( 'bp_moderate' ) && in_array( bp_get_member_user_id(), $private_users, true ) ) : ?>
								<p class="private-membership-indicator"><span class="fa fa-eye-slash"></span> <?php esc_html_e( 'Membership hidden', 'commons-in-a-box' ); ?></p>
							<?php endif ?>

							<?php do_action( 'bp_group_members_list_item' ); ?>

							<?php if ( function_exists( 'friends_install' ) ) : ?>

								<div class="action">
									<?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ); ?>

									<?php do_action( 'bp_group_members_list_item_action' ); ?>
								</div>

							<?php endif; ?>

						</div><!-- .item -->
					</div><!-- .row -->
				</div><!-- .group-item-wrapper -->
			</div><!-- .group-item -->

		<?php endwhile; ?>

	</div>
		<div id="pag-top" class="pagination clearfix">

			<div class="pagination-links" id="member-dir-pag-top">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo openlab_members_pagination_links( 'mlpage' ); ?>
			</div>

		</div>

	<?php do_action( 'bp_after_group_members_content' ); ?>

<?php else : ?>

	<div id="message" class="info">
		<p class="bold"><?php esc_html_e( 'This group has no members.', 'commons-in-a-box' ); ?></p>
	</div>

<?php endif; ?>
