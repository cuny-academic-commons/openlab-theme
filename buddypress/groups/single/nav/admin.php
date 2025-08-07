<?php

/**
 * 'Admin' nav markup.
 *
 * @since 1.6.0 Moved to this file from openlab_group_admin_tabs().
 */

$group = groups_get_current_group();

$current_tab = bp_action_variable( 0 );

$group_type = cboxol_get_group_group_type( $group->id );

// Portfolio tabs look different from other groups
?>
<?php if ( cboxol_is_portfolio() ) : ?>
	<?php if ( bp_is_item_admin() || bp_is_item_mod() ) { ?>
		<li class="<?php echo ( 'edit-details' === $current_tab || empty( $current_tab ) ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'edit-details' ], 'manage' ) ) ); ?>"><?php echo esc_html( $group_type->get_label( 'group_details' ) ); ?></a></li>
	<?php } ?>

	<li class="<?php echo 'site-details' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'site-details' ], 'manage' ) ) ); ?>"><?php echo esc_html_x( 'Site', 'Group admin nav item', 'commons-in-a-box' ); ?></a></li>

	<li class="<?php echo 'group-settings' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'group-settings' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Settings', 'commons-in-a-box' ); ?></a></li>

	<li class="delete-button <?php echo 'delete-group' === $current_tab ? 'current-menu-item' : ''; ?>"><span class="fa fa-minus-circle"></span><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'delete-group' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Delete Portfolio', 'commons-in-a-box' ); ?></a></li>

<?php else : ?>

	<?php if ( bp_is_item_admin() || bp_is_item_mod() ) { ?>
		<li class="<?php echo ( 'edit-details' === $current_tab || empty( $current_tab ) ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'edit-details' ], 'manage' ) ) ); ?>"><?php echo esc_html( $group_type->get_label( 'group_details' ) ); ?></a></li>
	<?php } ?>

	<?php
	if ( ! bp_is_item_admin() ) {
		return false;
	}
	?>

	<li class="<?php echo 'site-details' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'site-details' ], 'manage' ) ) ); ?>"><?php echo esc_html_x( 'Site', 'Group admin nav item', 'commons-in-a-box' ); ?></a></li>

	<li class="<?php echo 'group-settings' === $current_tab ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'group-settings' ], 'manage' ) ) ); ?>"><?php esc_attr_e( 'Settings', 'commons-in-a-box' ); ?></a></li>

	<?php if ( $group_type->get_can_be_cloned() ) : ?>
		<?php
		$clone_link = add_query_arg(
			array(
				'group_type' => $group_type->get_slug(),
				'clone'      => bp_get_current_group_id(),
			),
			bp_get_groups_directory_url( bp_groups_get_path_chunks( [ 'group-details' ], 'create' ) )
		);
		?>

		<li class="clone-button <?php 'clone-group' === $current_tab ? 'current-menu-item' : ''; ?>"><span class="fa fa-plus-circle"></span><a href="<?php echo esc_url( $clone_link ); ?>"><?php esc_html_e( 'Clone', 'commons-in-a-box' ); ?></a></li>
	<?php endif ?>

	<li class="delete-button last-item <?php echo 'delete-group' === $current_tab ? 'current-menu-item' : ''; ?>"><span class="fa fa-minus-circle"></span><a href="<?php echo esc_attr( bp_get_group_manage_url( $group, bp_groups_get_path_chunks( [ 'delete-group' ], 'manage' ) ) ); ?>"><?php esc_html_e( 'Delete', 'commons-in-a-box' ); ?></a></li>

	<?php if ( $group_type->get_is_portfolio() ) : ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<li class="portfolio-displayname pull-right"><span class="highlight"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?></span></li>
	<?php else : ?>
		<?php // translators: last active timestamp ?>
		<li class="info-line pull-right"><span class="timestamp info-line-timestamp visible-lg"><span class="fa fa-undo"></span> <?php echo esc_html( sprintf( __( 'active %s', 'commons-in-a-box' ), bp_get_group_last_active() ) ); ?></span></li>
	<?php endif; ?>

<?php endif; ?>
