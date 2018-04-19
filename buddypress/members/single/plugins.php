<?php
/**
 * Plugins - template: for MOL Invitations
 *
 * */
/* * begin layout* */
	global $bp;

	do_action( 'bp_before_member_plugin_template' );
	?>

	<?php if ( bp_is_current_component( 'invite-anyone' ) ) : ?>
		<?php echo openlab_submenu_markup( 'invitations' ); ?>
	<?php endif; ?>

	<div id="item-body" role="main">
		<?php do_action( 'bp_template_content' );
		do_action( 'bp_after_member_body' );
		?>
	</div><!-- #item-body -->
	<?php
	do_action( 'bp_after_member_plugin_template' );
