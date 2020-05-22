<?php
$nav_menu_args = array(
	'theme_location' => 'aboutmenu',
	'container'      => 'div',
	'container_id'   => 'about-menu',
	'menu_class'     => 'sidebar-nav clearfix',
);
?>

<div id="sidebar" class="sidebar col-sm-6 col-xs-24 pull-right type-about">
	<div class="sidebar-wrapper">
		<h2 class="sidebar-title hidden-xs"><?php esc_html_e( 'About', 'openlab-theme' ); ?></h2>
		<div class="sidebar-block hidden-xs">
			<?php wp_nav_menu( $nav_menu_args ); ?>
		</div>
	</div>
</div>
