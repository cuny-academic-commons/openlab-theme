<?php

if ( ( bp_is_user_activity() || ! bp_current_component() ) && ! ( strpos( $post->post_name, 'my-' ) > -1 ) ) {
	$mobile_hide = true;
	$el_id       = 'portfolio-sidebar-widget';
} else {
	$mobile_hide = false;
	$el_id       = 'portfolio-sidebar-inline-widget';
}
?>

<div class="sidebar-widget mol-menu" id="<?php echo esc_attr( $el_id ); ?>">

	<?php openlab_members_sidebar_blocks( $mobile_hide ); ?>
	<?php openlab_member_sidebar_menu(); ?>

</div>
