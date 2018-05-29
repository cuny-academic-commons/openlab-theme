<?php // header related functionality

define( 'BP_DISABLE_ADMIN_BAR', true );

add_action( 'widgets_init', 'cuny_remove_default_widget_areas', 11 );
function cuny_remove_default_widget_areas() {
	unregister_sidebar( 'sidebar' );
	unregister_sidebar( 'sidebar-alt' );
}
/** Add support for custom background */
add_theme_support( 'custom-background', array() );

add_action( 'wp_print_styles', 'cuny_no_bp_default_styles', 100 );

// Enqueue Styles For Testimonials Page & sub-pages
add_action( 'wp_print_styles', 'wds_cuny_ie_styles' );
function wds_cuny_ie_styles() {
	if ( is_admin() ) {
		return;
	}
	?>

	<!--[if lte IE 7]>
	  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie7.css" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE 8]>
	  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie8.css" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE 9]>
	  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie9.css" type="text/css" media="screen" />
	<![endif]-->


	<?php }

function cuny_no_bp_default_styles() {
	wp_dequeue_style( 'gconnect-bp' );
	wp_dequeue_script( 'superfish' );
	wp_dequeue_script( 'superfish-args' );

	wp_dequeue_style( 'gconnect-adminbar' );
}

function openlab_google_font() {
	wp_register_style( 'google-fonts', set_url_scheme( 'http://fonts.googleapis.com/css?family=Arvo' ), get_bloginfo( 'stylesheet_url' ), array(), openlab_get_asset_version() );
	wp_enqueue_style( 'google-fonts' );
}
add_action( 'wp_enqueue_scripts', 'openlab_google_font' );
