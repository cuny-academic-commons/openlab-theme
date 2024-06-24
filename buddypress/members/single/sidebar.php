<?php
// Get the displayed user's base domain
// This is required because the my-* pages aren't really displayed user pages from BP's
// point of view
$dud = bp_displayed_user_domain();
if ( ! $dud ) {
	$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
}

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
