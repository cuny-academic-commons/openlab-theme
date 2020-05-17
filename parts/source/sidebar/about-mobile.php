<?php
$nav_menu_args = array(
	'theme_location' => 'aboutmenu',
	'container'      => 'div',
	'container_id'   => 'about-mobile-menu',
	'menu_class'     => 'sidebar-nav clearfix',
);
?>

<div id="sidebar-mobile" class="sidebar clearfix mobile-dropdown">
	<div class="sidebar-block">
		<?php wp_nav_menu( $nav_menu_args ); ?>
	</div>
</div>
