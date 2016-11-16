<?php

if (!defined('CSS_DEBUG')) {
    define('CSS_DEBUG', false);
}

function openlab_core_setup() {
    add_theme_support('post-thumbnails');
    global $content_width;
    register_nav_menus(array(
        'main' => __('Main Menu', 'openlab'),
        'aboutmenu' => __('About Menu', 'openlab'),
        'helpmenu' => __('Help Menu', 'openlab'),
        'helpmenusec' => __('Help Menu Secondary', 'openlab')
    ));
}

// test

add_action('after_setup_theme', 'openlab_core_setup');

/* * creating a library to organize functions* */
/* * core* */
require_once( STYLESHEETPATH . '/lib/core/page-control.php' );
require_once( STYLESHEETPATH . '/lib/core/frontend-admin.php' );
require_once( STYLESHEETPATH . '/lib/core/backend-admin.php' );

require_once( STYLESHEETPATH . '/lib/course-clone.php' );
require_once( STYLESHEETPATH . '/lib/header-funcs.php' );
require_once( STYLESHEETPATH . '/lib/post-types.php' );
require_once( STYLESHEETPATH . '/lib/menus.php' );
require_once( STYLESHEETPATH . '/lib/content-processing.php' );
require_once( STYLESHEETPATH . '/lib/nav.php' );
require_once( STYLESHEETPATH . '/lib/breadcrumbs.php' );
require_once( STYLESHEETPATH . '/lib/shortcodes.php' );
require_once( STYLESHEETPATH . '/lib/media-funcs.php' );
require_once( STYLESHEETPATH . '/lib/group-funcs.php' );
require_once( STYLESHEETPATH . '/lib/ajax-funcs.php' );
require_once( STYLESHEETPATH . '/lib/help-funcs.php' );
require_once( STYLESHEETPATH . '/lib/member-funcs.php' );
require_once( STYLESHEETPATH . '/lib/page-funcs.php' );
require_once( STYLESHEETPATH . '/lib/sidebar-funcs.php' );
require_once( STYLESHEETPATH . '/lib/plugin-hooks.php' );
require_once( STYLESHEETPATH . '/lib/theme-hooks.php' );

function openlab_load_scripts() {
    $stylesheet_dir_uri = get_stylesheet_directory_uri();

    /**
     * scripts, additional functionality
     */
    if (!is_admin()) {

        //google fonts
        wp_register_style('google-open-sans', set_url_scheme('http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic'), array(), '2014', 'all');
        wp_enqueue_style('google-open-sans');

        wp_register_style('camera-js-styles', $stylesheet_dir_uri . '/css/camera.css', array(), '20130604', 'all');
        wp_enqueue_style('camera-js-styles');

        //less compliation via js so we can check styles in firebug via fireless - local dev only
        if (CSS_DEBUG) {
            wp_register_script('less-config-js', $stylesheet_dir_uri . '/js/less.config.js', array('jquery'));
            wp_enqueue_script('less-config-js');
            wp_register_script('less-js', $stylesheet_dir_uri . '/js/less-1.7.4.js', array('jquery'));
            wp_enqueue_script('less-js');
        }

        wp_register_script('vendor-js', $stylesheet_dir_uri . '/js/dist/vendor.js', array('jquery'), '1.6.8', true);
        wp_enqueue_script('vendor-js');

        $utility_deps = array('jquery');
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            $utility_deps[] = 'hyphenator-js';
        } else {
            $utility_deps[] = 'openlab-smoothscroll';
        }
        wp_register_script('utility', $stylesheet_dir_uri . '/js/utility.js', $utility_deps, '1.6.9.8', true);
        wp_enqueue_script('utility');
        wp_localize_script('utility', 'localVars', array(
            'nonce' => wp_create_nonce('request-nonce'),
        ));

        wp_register_script('parsley', $stylesheet_dir_uri . '/js/parsley.min.js', array('jquery'));
    }

    if ( bp_is_register_page() ) {
        wp_enqueue_script( 'password-strength-meter' );
    }
}

add_action('wp_enqueue_scripts', 'openlab_load_scripts');

function openlab_admin_scripts() {
    wp_register_script('utility-admin', get_stylesheet_directory_uri() . '/js/utility.admin.js', array('jquery', 'jquery-ui-autocomplete'), '1.6.9.7', true);
    wp_enqueue_script('utility-admin');
    wp_localize_script('utility-admin', 'localVars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('request-nonce'),
    ));
}

add_action('admin_enqueue_scripts', 'openlab_admin_scripts');

/**
 * Giving the main stylesheet the highest priority among stylesheets to make sure it loads last
 */
function openlab_load_scripts_high_priority() {
    $stylesheet_dir_uri = get_stylesheet_directory_uri();

    global $post;
    //less compliation via js so we can check styles in firebug via fireless - local dev only
    //@to-do: way to enqueue as last item?
    if (CSS_DEBUG) {
        wp_register_style('main-styles', $stylesheet_dir_uri . '/style.less', array(), '1.6.9.5', 'all');
        wp_enqueue_style('main-styles');
    } else {

        wp_register_style('main-styles', $stylesheet_dir_uri . '/style.css', array(), '1.6.9.5', 'all');
        wp_enqueue_style('main-styles');
    }

    if (isset($post->post_type) && $post->post_type == 'help') {
        wp_register_style('print-styles', $stylesheet_dir_uri . '/css/print.css', array(), '2015', 'print');
        wp_enqueue_style('print-styles');
    }
}

add_action('wp_enqueue_scripts', 'openlab_load_scripts_high_priority', 999);

/**
 * Custom image sizes
 */
//front page slider
add_image_size('front-page-slider', 735, 295, true);

//custom widgets for OpenLab
function cuny_widgets_init() {
    //add widget for Rotating Post Gallery Widget - will be placed on the homepage
    register_sidebar(array(
        'name' => __('Rotating Post Gallery Widdget', 'cuny'),
        'description' => __('This is the widget for holding the Rotating Post Gallery Widget', 'cuny'),
        'id' => 'pgw-gallery',
        'before_widget' => '<div id="pgw-gallery">',
        'after_widget' => '</div>',
    ));
    //add widget for the Featured Widget - will be placed on the homepage under "In the Spotlight"
    register_sidebar(array(
        'name' => __('Featured Widget', 'cuny'),
        'description' => __('This is the widget for holding the Featured Widget', 'cuny'),
        'id' => 'cac-featured',
        'before_widget' => '<div id="cac-featured">',
        'after_widget' => '</div>',
    ));
}

add_action('widgets_init', 'cuny_widgets_init');

function cuny_o_e_class($num) {
    return $num % 2 == 0 ? " even" : " odd";
}

function cuny_third_end_class($num) {
    return $num % 3 == 0 ? " last" : "";
}

/**
 * Modify the body class
 *
 * Invite New Members and Your Email Options fall under "Settings", so need
 * an appropriate body class.
 */
function openlab_group_admin_body_classes($classes) {
    if (!bp_is_group()) {
        return $classes;
    }

    if (in_array(bp_current_action(), array('invite-anyone', 'notifications'))) {
        $classes[] = 'group-admin';
    }

    return $classes;
}

add_filter('bp_get_the_body_class', 'openlab_group_admin_body_classes');

//for less js - local dev only
function enqueue_less_styles($tag, $handle) {
    global $wp_styles;
    $match_pattern = '/\.less$/U';
    if (preg_match($match_pattern, $wp_styles->registered[$handle]->src)) {
        $handle = $wp_styles->registered[$handle]->handle;
        $media = $wp_styles->registered[$handle]->args;
        $href = $wp_styles->registered[$handle]->src;
        $rel = isset($wp_styles->registered[$handle]->extra['alt']) && $wp_styles->registered[$handle]->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
        $title = isset($wp_styles->registered[$handle]->extra['title']) ? "title='" . esc_attr($wp_styles->registered[$handle]->extra['title']) . "'" : '';

        $tag = "<link rel='stylesheet/less' $title href='$href' type='text/css' media='$media' />";
    }
    return $tag;
}

add_filter('style_loader_tag', 'enqueue_less_styles', 5, 2);

/**
 * Get content with formatting in place
 * @param type $more_link_text
 * @param type $stripteaser
 * @param type $more_file
 * @return type
 */
function get_the_content_with_formatting($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
}

/**
 * Get a value from a failed POST request, especially during registration.
 */
function openlab_post_value($key) {
    $value = '';
    if (!empty($_POST[$key])) {
        $value = wp_unslash($_POST[$key]);
    }
    return $value;
}

/**
 * Disable the new avatar upload interface introduced in BP 2.3.
 */
add_filter('bp_avatar_is_front_edit', '__return_false');

/**
 * Generate data attributes for xprofile 'input' fields.
 *
 * Used for Parsely validation.
 */
function openlab_profile_field_input_attributes() {
    $attributes = array();

    switch (bp_get_the_profile_field_name()) {
        case 'Name' :
            $attributes[] = 'data-parsley-required';
            $attributes[] = 'data-parsley-required';
            break;

        case 'First Name' :
            $attributes[] = 'data-parsley-required';
            break;

        case 'Last Name' :
            $attributes[] = 'data-parsley-required';
            break;

        case 'Account Type' :
            $attributes[] = 'data-parsley-required';
            break;
    }

    if ($attributes) {
        return ' ' . implode(' ', $attributes) . ' ';
    }
}
