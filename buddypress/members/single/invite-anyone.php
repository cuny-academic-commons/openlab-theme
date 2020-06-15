<?php
/**
 * BuddyPress - Users Profile
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
?>

<?php do_action( 'bp_before_profile_content' ); ?>

<div class="profile" role="main">

	<?php
	switch ( bp_current_action() ) :

		// Edit
		case 'invite-new-members':
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo openlab_submenu_markup( 'invitations' );
			openlab_invite_anyone_screen_one_content();
			break;

		case 'sent-invites':
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo openlab_submenu_markup( 'invitations' );
			openlab_invite_anyone_screen_two_content();
			break;

		// Any other
		default:
			bp_get_template_part( 'members/single/plugins' );
			break;
	endswitch;
	?>
</div><!-- .profile -->

<?php do_action( 'bp_after_profile_content' ); ?>
