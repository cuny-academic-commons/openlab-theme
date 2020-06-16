<?php do_action( 'bp_before_member_home_content' ); ?>

	<?php openlab_bp_mobile_sidebar( 'members' ); ?>
	<div class="col-sm-18 col-xs-24 members-single-home">
		<div id="openlab-main-content" class="content-wrapper">

			<?php

			do_action( 'bp_before_member_body' );

			if ( bp_is_user_activity() || ! bp_current_component() ) :
				bp_get_template_part( 'members/single/member-home' );

			elseif ( bp_is_user_blogs() ) :
				bp_get_template_part( 'members/single/blogs' );

			elseif ( bp_is_user_friends() ) :
				bp_get_template_part( 'members/single/friends' );

			elseif ( bp_is_user_groups() ) :
				bp_get_template_part( 'members/single/groups' );

			elseif ( bp_is_user_messages() ) :
				bp_get_template_part( 'members/single/messages' );

			elseif ( bp_is_user_profile() ) :
				bp_get_template_part( 'members/single/profile' );

			elseif ( bp_is_user_notifications() ) :
				bp_get_template_part( 'members/single/notifications' );

			elseif ( bp_is_user_settings() ) :
				bp_get_template_part( 'members/single/settings' );

			elseif ( bp_current_action() === 'invite-new-members' || bp_current_action() === 'sent-invites' ) :
				bp_get_template_part( 'members/single/invite-anyone' );

			else :
				// If nothing sticks, load a generic template.
				bp_get_template_part( 'members/single/plugins' );

			endif;

			do_action( 'bp_after_member_body' );

			?>
		</div>
	</div>

<?php do_action( 'bp_after_member_home_content' ); ?>

<?php get_template_part( 'parts/sidebar/members' ); ?>
