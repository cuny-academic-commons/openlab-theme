<?php //header related functionality

define('BP_DISABLE_ADMIN_BAR', true);

add_action( 'widgets_init', 'cuny_remove_default_widget_areas', 11 );
function cuny_remove_default_widget_areas() {
	unregister_sidebar('sidebar');
	unregister_sidebar('sidebar-alt');
}
/** Add support for custom background **/
add_theme_support( 'custom-background', array() );

add_action( 'wp_print_styles', 'cuny_no_bp_default_styles', 100 );

// Enqueue Styles For Testimonials Page & sub-pages
add_action('wp_print_styles', 'wds_cuny_ie_styles');
function wds_cuny_ie_styles() {
  if ( is_admin() )
    return;
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
	wp_dequeue_script('superfish');
	wp_dequeue_script('superfish-args');

	wp_dequeue_style( 'gconnect-adminbar' );
}

function openlab_google_font() {
	wp_register_style('google-fonts', set_url_scheme( 'http://fonts.googleapis.com/css?family=Arvo' ),get_bloginfo( 'stylesheet_url' ));
	wp_enqueue_style('google-fonts');
}

add_action( 'wp_enqueue_scripts', 'openlab_google_font');

/**
 * Enqueue our front-end scripts
 */
function openlab_enqueue_frontend_scripts() {
	if ( ( bp_is_group_create() && bp_is_action_variable( 'group-details', 1 ) ) ||
	     ( bp_is_group_create() && bp_is_action_variable( 'invite-anyone', 1 ) ) ||
             ( bp_is_group_admin_page() && bp_is_action_variable( 'edit-details', 0 ) ) ) {
		wp_enqueue_script( 'openlab-group-create', get_stylesheet_directory_uri() . '/js/group-create.js', array( 'jquery' ) );
	}

        if ( bp_is_register_page() ) {
                wp_enqueue_script( 'openlab-registration', get_stylesheet_directory_uri() . '/js/register.js', array( 'jquery', 'parsley' ) );

                wp_localize_script('openlab-registration', 'OLReg', array(
                    'post_data' => $_POST,
                    'account_type_field' => xprofile_get_field_id_from_name('Account Type '),
                ));
    }

}
add_action( 'wp_enqueue_scripts', 'openlab_enqueue_frontend_scripts', 20 );
