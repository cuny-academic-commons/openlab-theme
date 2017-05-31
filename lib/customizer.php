<?php

/**
 * Functionality related to the Customizer.
 */

function openlab_color_schemes() {
	return array(
		'red' => __( 'Red', 'openlab-theme' ),
		'blue' => __( 'Blue', 'openlab-theme' ),
		'gold' => __( 'Gold', 'openlab-theme' ),
	);
}

function openlab_customizer_setup( $wp_customize ) {
	require get_template_directory() . '/lib/customize-controls/class-openlab-color-scheme-customize-control.php';

	// Color Scheme
	$wp_customize->remove_section( 'colors' );
	$wp_customize->add_section( 'openlab_section_color_scheme', array(
		'title' => __( 'Color Scheme', 'openlab-theme' ),
	) );

	$wp_customize->add_setting( 'openlab_color_scheme', array(
		'type' => 'theme_mod',
		'default' => 'default',
		'sanitize_callback' => 'openlab_sanitize_customizer_setting_color_scheme',
	) );

	$wp_customize->add_control(
		new OpenLab_Color_Scheme_Customize_Control(
			$wp_customize,
			'openlab_color_scheme',
			array(
				'label' => 'Foo Color Scheme',
				'choices' => openlab_color_schemes(),
				'section' => 'openlab_section_color_scheme',
			)
		)
	);

	// Logo
	$wp_customize->add_section( 'openlab_logo', array(
		'title' => __( 'Logo', 'openlab-theme' ),
	) );

	$wp_customize->add_setting( 'openlab_logo', array(
		'type' => 'theme_mod',
		'sanitize_callback' => 'openlab_sanitize_customizer_setting_intval',
	) );

	$wp_customize->add_control(
		new WP_Customize_Cropped_Image_Control(
			$wp_customize,
			'openlab_logo',
			array(
				'label'         => __( 'Logo', 'openlab-theme' ),
				'section'       => 'openlab_logo',
				'height'        => 63,
				'width'         => 185,
				'flex_height'   => false,
				'flex_width'    => true,
				'button_labels' => array(
					'select'       => __( 'Select logo' ),
					'change'       => __( 'Change logo' ),
					'remove'       => __( 'Remove' ),
					'default'      => __( 'Default' ),
					'placeholder'  => __( 'No logo selected' ),
					'frame_title'  => __( 'Select logo' ),
					'frame_button' => __( 'Choose logo' ),
				),
			)
		)
	);

	$wp_customize->selective_refresh->add_partial( 'openlab_logo', array(
		'settings'            => array( 'openlab_logo' ),
		'selector'            => '.custom-logo-link',
		'render_callback'     => 'openlab_get_logo_html',
		'container_inclusive' => true,
	) );

	// Home Page
	$wp_customize->add_panel( 'openlab_home_page', array(
		'title' => __( 'Home Page', 'openlab-theme' ),
	) );

	global $wp_registered_sidebars;
	$openlab_sidebars = array( 'home-main', 'home-sidebar' );
	foreach ( $openlab_sidebars as $sidebar_id ) {
		$sid = 'sidebar-widgets-' . $sidebar_id;
		$section = $wp_customize->get_section( $sid );

		if ( ! $section ) {
			_b( 'no' );
			continue;
		}

		$c = clone( $section );
		$wp_customize->remove_section( $id );

		$c->panel = 'openlab_home_page';
		$wp_customize->add_section( $c );
	}
}
add_action( 'customize_register', 'openlab_customizer_setup', 200 );

function openlab_sanitize_customizer_setting_color_scheme( $setting ) {
	$settings = array( 'blue', 'gold', 'red', 'default' );
	if ( ! in_array( $setting, $settings, true ) ) {
		$setting = 'default';
	}
	return $setting;
}

/**
 * Can't pass directly to intval() because Customizer passes more than one param.
 */
function openlab_sanitize_customizer_setting_intval( $setting ) {
	return intval( $setting );
}
