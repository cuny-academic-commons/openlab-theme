<?php

/**
 * Functionality related to the Customizer.
 */

function openlab_customizer_setup( $wp_customize ) {
	require get_template_directory() . '/lib/customize-controls/class-openlab-color-scheme-customize-control.php';

	$wp_customize->remove_section( 'colors' );

	$wp_customize->add_section( 'openlab_section_color_scheme', array(
		'title' => __( 'Color Scheme', 'openlab-theme' ),
	) );

	$wp_customize->add_setting( 'openlab_setting_color_scheme', array(
		'type' => 'theme_mod',
		'default' => 'default',
		'sanitize_callback' => 'openlab_sanitize_customizer_setting_color_scheme',
	) );

	$wp_customize->add_control(
		new OpenLab_Color_Scheme_Customize_Control(
			$wp_customize,
			'openlab_setting_color_scheme',
			array(
				'label' => 'Foo Color Scheme',
				'choices' => array(
					'red' => 'Red',
					'blue' => 'Blue',
					'gold' => 'Gold',
				),
				'section' => 'openlab_section_color_scheme',
			)
		)
	);
}
add_action( 'customize_register', 'openlab_customizer_setup', 100 );

function openlab_sanitize_customizer_setting_color_scheme( $setting ) {
	$settings = array( 'blue', 'gold', 'red', 'default' );
	if ( ! in_array( $setting, $settings, true ) ) {
		$setting = 'default';
	}
	return $setting;
}
