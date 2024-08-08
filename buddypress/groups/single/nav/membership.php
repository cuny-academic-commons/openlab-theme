<?php

/**
 * 'Membership' tabs.
 *
 * @since 1.6.0 Moved from openlab_group_membership_tabs() into this file.
 */

$group = groups_get_current_group();

$current_tab = bp_action_variable( 0 );

?>

<?php if ( bp_is_item_admin() ) : ?>
	<li class="<?php echo 'manage-members' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'manage-members' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Membership', 'commons-in-a-box' ); ?></a></li>

	<?php if ( 'private' === $group->status ) : ?>
		<li class="<?php echo 'membership-requests' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'membership-requests' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Member Requests', 'commons-in-a-box' ); ?></a></li>
	<?php endif; ?>
<?php else : ?>
	<li class="<?php echo bp_is_current_action( 'members' ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_url( $group, bp_groups_get_path_chunks( [ 'members' ] ) ) ); ?>"><?php esc_html_e( 'Membership', 'commons-in-a-box' ); ?></a></li>
<?php endif; ?>

<?php if ( bp_group_is_member() && invite_anyone_access_test() && openlab_is_admin_truly_member() ) : ?>
	<li class="<?php echo bp_is_current_action( 'invite-anyone' ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_url( $group, bp_groups_get_path_chunks( [ 'invite-anyone' ] ) ) ); ?>"><?php esc_html_e( 'Invite New Members', 'commons-in-a-box' ); ?></a></li>
<?php endif; ?>

<?php if ( bp_is_item_admin() ) : ?>
	<li class="<?php echo 'notifications' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'notifications' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Email Members', 'commons-in-a-box' ); ?></a></li>
<?php endif; ?>

<?php if ( bp_group_is_member() && openlab_is_admin_truly_member() ) : ?>
	<li class="<?php echo bp_is_current_action( 'notifications' ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_url( $group, bp_groups_get_path_chunks( [ 'notifications' ] ) ) ); ?>"><?php esc_html_e( 'Your Email Options', 'commons-in-a-box' ); ?></a></li>
<?php endif; ?>

<?php
