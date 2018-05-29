<?php

/**
 * Functionality related to the Customizer.
 */

/**
 * Set up Customizer panels.
 */
function openlab_customizer_setup( $wp_customize ) {
	require __DIR__ . '/class-customize-footer-section.php';

	// Color Scheme
	$wp_customize->remove_section( 'colors' );
	$wp_customize->add_section( 'openlab_section_color_scheme', array(
		'title' => __( 'Color Scheme', 'openlab-theme' ),
	) );

	$wp_customize->add_setting( 'openlab_color_scheme', array(
		'type' => 'theme_mod',
		'default' => 'blue',
		'sanitize_callback' => 'openlab_sanitize_customizer_setting_color_scheme',
	) );

	$color_schemes = openlab_color_schemes();
	$color_scheme_choices = array();
	foreach ( $color_schemes as $color_scheme => $color_scheme_data ) {
		$color_scheme_choices[ $color_scheme ] = $color_scheme_data['label'];
	}

	$wp_customize->add_control(
		'openlab_color_scheme',
		array(
			'label' => __( 'Color Scheme', 'openlab-theme' ),
			'section' => 'openlab_section_color_scheme',
			'type' => 'radio',
			'choices' => $color_scheme_choices,
			'default' => 'blue',
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
			continue;
		}

		$c = clone( $section );
		$wp_customize->remove_section( $sid );

		$c->panel = 'openlab_home_page';
		$wp_customize->add_section( $c );
	}

	$wp_customize->add_panel( 'openlab_panel_footer', array(
		'title' => __( 'Community-wide Footer', 'openlab-theme' ),
		'description' => __( 'This works', 'openlab-theme' ),
	) );

	// Footer Left
	$wp_customize->add_section( 'openlab_section_footer_left', array(
		'title' => __( 'Footer - Left', 'openlab-theme' ),
		'panel' => 'openlab_panel_footer',
		'description' => __( 'Controls the text on the left-hand side of the community-wide footer.', 'openlab-theme' ),
	) );

	$wp_customize->add_setting( 'openlab_footer_left_heading', array(
		'type' => 'theme_mod',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_setting( 'openlab_footer_left_content', array(
		'type' => 'theme_mod',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( 'openlab_footer_left_heading', array(
		'type' => 'text',
		'label' => __( 'Heading', 'openlab-theme' ),
		'section' => 'openlab_section_footer_left',
	) );

	$wp_customize->add_control( new OpenLab_Footer_Section_Control(
		$wp_customize,
		'openlab_footer_left_content',
		array(
			'label' => __( 'Content', 'openlab-theme' ),
			'section' => 'openlab_section_footer_left',
		)
	) );

	// Footer Middle
	$wp_customize->add_section( 'openlab_section_footer_middle', array(
		'title' => __( 'Footer - Middle', 'openlab-theme' ),
		'panel' => 'openlab_panel_footer',
		'description' => __( 'Controls the text on the middle of the community-wide footer.', 'openlab-theme' ),
	) );

	$wp_customize->add_setting( 'openlab_footer_middle_heading', array(
		'type' => 'theme_mod',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_setting( 'openlab_footer_middle_content', array(
		'type' => 'theme_mod',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( 'openlab_footer_middle_heading', array(
		'type' => 'text',
		'label' => __( 'Heading', 'openlab-theme' ),
		'section' => 'openlab_section_footer_middle',
	) );

	$wp_customize->add_control( new OpenLab_Footer_Section_Control(
		$wp_customize,
		'openlab_footer_middle_content',
		array(
			'label' => __( 'Content', 'openlab-theme' ),
			'section' => 'openlab_section_footer_middle',
		)
	) );

	wp_enqueue_editor();
	wp_enqueue_media();

	wp_enqueue_style( 'openlab-customizer', get_template_directory_uri() . '/css/customizer.css', array(), openlab_get_asset_version() );

	// If there are no more sidebars, remove the top-level Widgets area altogether.
	// @todo this causes PHP notices due to hardcoded stuff in WP. Consider hiding with CSS.
	$widget_panel = $wp_customize->get_panel( 'widgets' );
	if ( $widget_panel && empty( $widget_panel->sections ) ) {
	//	$wp_customize->remove_panel( 'widgets' );
	}

	if ( $wp_customize->is_preview() && ! is_admin() ) {
//		add_action( 'wp_footer', 'openlab_customize_preview' );
	}
}
add_action( 'customize_register', 'openlab_customizer_setup', 200 );

function openlab_enqueue_customizer_preview_script() {
	global $wp_customize;

	if ( empty( $wp_customize ) || ! $wp_customize->is_preview() ) {
		return;
	}

	wp_enqueue_script( 'openlab-customizer-preview', get_template_directory_uri() . '/js/customizer-preview.js', array( 'jquery' ), openlab_get_asset_version(), true );
}
add_action( 'wp_enqueue_scripts', 'openlab_enqueue_customizer_preview_script' );


function openlab_customizer_styles() {
	$color_schemes = openlab_color_schemes();
	?>
	<style type="text/css">
		#customize-control-openlab_color_scheme label::before {
			border: 1px solid #666;
			border-radius: 50%;
			content: '';
			display: block;
			float: right;
			height: 25px;
			margin-right: 20px;
			margin-top: -4px;
			width: 25px;
		}

		<?php foreach ( $color_schemes as $color_scheme => $color_scheme_data ) : ?>
			<?php printf(
				"\n" . '#customize-control-openlab_color_scheme label[for="_customize-input-openlab_color_scheme-radio-%s"]::before {
					background-color: %s;
				}',
				esc_attr( $color_scheme ),
				esc_attr( $color_scheme_data['icon_color'] )
			); ?>
		<?php endforeach; ?>
	</style>
	<?php
}
add_action( 'customize_controls_print_styles', 'openlab_customizer_styles' );

function openlab_customizer_scripts() {
	wp_enqueue_script( 'openlab-theme-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-controls' ), openlab_get_asset_version() );
}
add_action( 'customize_controls_enqueue_scripts', 'openlab_customizer_scripts' );

function openlab_sanitize_customizer_setting_color_scheme( $setting ) {
	$color_schemes = openlab_color_schemes();

	if ( ! isset( $color_schemes[ $setting ] ) ) {
		$setting = 'blue';
	}

	return $setting;
}

/**
 * Can't pass directly to intval() because Customizer passes more than one param.
 */
function openlab_sanitize_customizer_setting_intval( $setting ) {
	return intval( $setting );
}
