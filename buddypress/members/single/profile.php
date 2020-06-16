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
	case 'edit':
		bp_get_template_part( 'members/single/profile/edit' );
		break;

	// Change Avatar
	case 'change-avatar':
		bp_get_template_part( 'members/single/profile/change-avatar' );
		break;

	// Compose
	case 'public':
		if ( bp_is_active( 'xprofile' ) ) {
			// Display XProfile
			bp_get_template_part( 'members/single/profile/profile-loop' );
		} else {
			// Display WordPress profile (fallback)
			bp_get_template_part( 'members/single/profile/profile-wp' );
		}

		break;

	// Any other
	default:
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;
?>
</div><!-- .profile -->

<?php do_action( 'bp_after_profile_content' ); ?>
