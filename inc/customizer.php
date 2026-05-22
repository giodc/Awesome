<?php
/**
 * Theme Customizer.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function awesome_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'awesome_header',
		array(
			'title'    => __( 'Header', 'awesome' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'awesome_header_display',
		array(
			'default'           => 'site_title',
			'sanitize_callback' => 'awesome_sanitize_header_display',
		)
	);

	$wp_customize->add_control(
		'awesome_header_display',
		array(
			'label'   => __( 'Header branding', 'awesome' ),
			'section' => 'awesome_header',
			'type'    => 'radio',
			'choices' => array(
				'site_title' => __( 'Site title (from Settings → General)', 'awesome' ),
				'custom'     => __( 'Custom site name', 'awesome' ),
				'logo_only'  => __( 'Logo only (hide text)', 'awesome' ),
			),
		)
	);

	$wp_customize->add_setting(
		'awesome_custom_site_name',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'awesome_custom_site_name',
		array(
			'label'       => __( 'Custom site name', 'awesome' ),
			'description' => __( 'Used when “Custom site name” is selected above.', 'awesome' ),
			'section'     => 'awesome_header',
			'type'        => 'text',
		)
	);

	$wp_customize->add_section(
		'awesome_colors',
		array(
			'title'       => __( 'Theme Colors', 'awesome' ),
			'description' => __( 'These map to the former Nuxt public color environment variables.', 'awesome' ),
			'priority'    => 40,
		)
	);

	$labels = array(
		'awesome_color_primary'       => __( 'Primary color', 'awesome' ),
		'awesome_color_secondary'     => __( 'Secondary color', 'awesome' ),
		'awesome_color_accents'       => __( 'Accent color', 'awesome' ),
		'awesome_color_background'    => __( 'Background color', 'awesome' ),
		'awesome_color_text'          => __( 'Text color', 'awesome' ),
		'awesome_color_content_link'  => __( 'Content links text color', 'awesome' ),
		'awesome_color_heading'       => __( 'Heading color', 'awesome' ),
		'awesome_color_heading_hover' => __( 'Heading hover color', 'awesome' ),
		'awesome_color_text_hover'    => __( 'Text hover color', 'awesome' ),
		'awesome_color_footer_bg'     => __( 'Footer background', 'awesome' ),
		'awesome_color_footer_text'   => __( 'Footer text color', 'awesome' ),
		'awesome_color_footer_text_hover' => __( 'Footer text hover color', 'awesome' ),
	);

	$defaults = awesome_default_colors();
	$map      = awesome_color_setting_map();

	foreach ( $map as $setting_id => $key ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $labels[ $setting_id ],
					'section' => 'awesome_colors',
				)
			)
		);
	}

	$wp_customize->add_section(
		'awesome_footer',
		array(
			'title'    => __( 'Footer', 'awesome' ),
			'priority' => 50,
		)
	);

	$wp_customize->add_setting(
		'awesome_footer_copyright',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'awesome_footer_copyright',
		array(
			'label'       => __( 'Copyright line', 'awesome' ),
			'description' => __( 'Leave empty to use the default: © year + site name.', 'awesome' ),
			'section'     => 'awesome_footer',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'awesome_customize_register' );

/**
 * Sanitize header display option.
 *
 * @param mixed $value Raw value.
 */
function awesome_sanitize_header_display( $value ): string {
	$allowed = array( 'site_title', 'custom', 'logo_only' );
	return in_array( $value, $allowed, true ) ? $value : 'site_title';
}

/**
 * Live preview script for Customizer colors.
 */
function awesome_customize_preview_js(): void {
	wp_enqueue_script(
		'awesome-customizer-preview',
		AWESOME_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview', 'jquery' ),
		AWESOME_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'awesome_customize_preview_js' );
