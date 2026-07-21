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

	$wp_customize->add_setting(
		'awesome_header_width',
		array(
			'default'           => 'full',
			'sanitize_callback' => 'awesome_sanitize_header_width',
		)
	);

	$wp_customize->add_control(
		'awesome_header_width',
		array(
			'label'       => __( 'Header width', 'awesome' ),
			'description' => __( 'Full width spans the viewport. Content width matches the main content container.', 'awesome' ),
			'section'     => 'awesome_header',
			'type'        => 'radio',
			'choices'     => array(
				'full'    => __( 'Full width', 'awesome' ),
				'content' => __( 'Content width', 'awesome' ),
			),
		)
	);

	$wp_customize->add_setting(
		'awesome_header_search',
		array(
			'default'           => true,
			'sanitize_callback' => 'awesome_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'awesome_header_search',
		array(
			'label'       => __( 'Show search icon', 'awesome' ),
			'description' => __( 'Displays a search button in the header that opens a search modal.', 'awesome' ),
			'section'     => 'awesome_header',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'awesome_header_menu_font_size',
		array(
			'default'           => '1.125',
			'sanitize_callback' => 'awesome_sanitize_header_menu_font_size',
		)
	);

	$wp_customize->add_control(
		'awesome_header_menu_font_size',
		array(
			'label'   => __( 'Menu font size', 'awesome' ),
			'section' => 'awesome_header',
			'type'    => 'select',
			'choices' => array(
				'0.875' => '0.875rem',
				'1'     => '1rem',
				'1.125' => '1.125rem',
				'1.25'  => '1.25rem',
				'1.375' => '1.375rem',
				'1.5'   => '1.5rem',
			),
		)
	);

	$wp_customize->add_section(
		'awesome_layout',
		array(
			'title'    => __( 'Layout', 'awesome' ),
			'priority' => 32,
		)
	);

	$wp_customize->add_setting(
		'awesome_content_max_width',
		array(
			'default'           => 1000,
			'sanitize_callback' => 'awesome_sanitize_content_max_width',
		)
	);

	$wp_customize->add_control(
		'awesome_content_max_width',
		array(
			'label'       => __( 'Content max width', 'awesome' ),
			'description' => __( 'Applies to post/page content and to the header when Header width is set to Content width.', 'awesome' ),
			'section'     => 'awesome_layout',
			'type'        => 'select',
			'choices'     => array(
				'800'  => '800px',
				'900'  => '900px',
				'1000' => '1000px',
				'1100' => '1100px',
				'1200' => '1200px',
				'1400' => '1400px',
			),
		)
	);

	$wp_customize->add_section(
		'awesome_fonts',
		array(
			'title'       => __( 'Fonts', 'awesome' ),
			'description' => __( 'Only selected Google fonts are loaded. System fonts add no network request.', 'awesome' ),
			'priority'    => 33,
		)
	);

	$font_controls = array(
		'body'    => __( 'Body / text font', 'awesome' ),
		'heading' => __( 'Heading font', 'awesome' ),
		'header'  => __( 'Header / menu font', 'awesome' ),
	);

	foreach ( $font_controls as $role => $label ) {
		$setting_id = awesome_font_mod_key( $role );
		$defaults   = awesome_font_role_defaults();

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $role ],
				'sanitize_callback' => 'awesome_sanitize_font_slug',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'awesome_fonts',
				'type'    => 'select',
				'choices' => awesome_get_font_choices(),
			)
		);
	}

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

	$wp_customize->add_section(
		'awesome_homepage',
		array(
			'title'       => __( 'Homepage', 'awesome' ),
			'description' => __( 'Options for the page set as Settings → Reading → Homepage.', 'awesome' ),
			'priority'    => 35,
		)
	);

	$wp_customize->add_setting(
		'awesome_homepage_transparent_header',
		array(
			'default'           => false,
			'sanitize_callback' => 'awesome_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'awesome_homepage_transparent_header',
		array(
			'label'       => __( 'Transparent header', 'awesome' ),
			'description' => __( 'Overlays the homepage so a full-width Cover/Hero can sit behind it. Content starts at the top of the viewport.', 'awesome' ),
			'section'     => 'awesome_homepage',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_section(
		'awesome_single_post',
		array(
			'title'    => __( 'Single Post', 'awesome' ),
			'priority' => 45,
		)
	);

	$wp_customize->add_setting(
		'awesome_single_sticky_sidebar',
		array(
			'default'           => false,
			'sanitize_callback' => 'awesome_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'awesome_single_sticky_sidebar',
		array(
			'label'       => __( 'Sticky sidebar by default', 'awesome' ),
			'description' => __( 'Shows the “Single Post Sidebar” widget area. Can be overridden per post.', 'awesome' ),
			'section'     => 'awesome_single_post',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_section(
		'awesome_categories',
		array(
			'title'    => __( 'Categories', 'awesome' ),
			'priority' => 46,
		)
	);

	$wp_customize->add_setting(
		'awesome_category_hero_enabled',
		array(
			'default'           => false,
			'sanitize_callback' => 'awesome_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'awesome_category_hero_enabled',
		array(
			'label'       => __( 'Enable category hero', 'awesome' ),
			'description' => __( 'Shows a full-width hero with the category name and description on category archives.', 'awesome' ),
			'section'     => 'awesome_categories',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'awesome_category_hero_bg',
		array(
			'default'           => '#1a1f26',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'awesome_category_hero_bg',
			array(
				'label'   => __( 'Category hero background', 'awesome' ),
				'section' => 'awesome_categories',
			)
		)
	);

	$wp_customize->add_setting(
		'awesome_category_hero_text',
		array(
			'default'           => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'awesome_category_hero_text',
			array(
				'label'   => __( 'Category hero text', 'awesome' ),
				'section' => 'awesome_categories',
			)
		)
	);

	$wp_customize->add_section(
		'awesome_global_sections',
		array(
			'title'       => __( 'Global Sections', 'awesome' ),
			'description' => __( 'Inject block content created under Global Sections into fixed theme zones. Choose “None” to disable a zone.', 'awesome' ),
			'priority'    => 48,
		)
	);

	foreach ( awesome_global_section_zones() as $zone => $label ) {
		$setting_id = awesome_global_section_mod_key( $zone );

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => 0,
				'sanitize_callback' => 'awesome_sanitize_global_section_id',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'awesome_global_sections',
				'type'    => 'select',
				'choices' => awesome_get_global_section_choices(),
			)
		);
	}
}
add_action( 'customize_register', 'awesome_customize_register' );

/**
 * Sanitize checkbox values.
 *
 * @param mixed $value Raw value.
 */
function awesome_sanitize_checkbox( $value ): bool {
	return (bool) $value;
}

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
 * Sanitize header width option.
 *
 * @param mixed $value Raw value.
 */
function awesome_sanitize_header_width( $value ): string {
	$allowed = array( 'full', 'content' );
	return in_array( $value, $allowed, true ) ? $value : 'full';
}

/**
 * Sanitize content max width (pixels).
 *
 * @param mixed $value Raw value.
 */
function awesome_sanitize_content_max_width( $value ): int {
	$allowed = array( 800, 900, 1000, 1100, 1200, 1400 );
	$width   = (int) $value;

	return in_array( $width, $allowed, true ) ? $width : 1000;
}

/**
 * Resolved content max width in pixels.
 */
function awesome_get_content_max_width(): int {
	return awesome_sanitize_content_max_width( get_theme_mod( 'awesome_content_max_width', 1000 ) );
}

/**
 * Resolved header width setting.
 */
function awesome_get_header_width(): string {
	return awesome_sanitize_header_width( get_theme_mod( 'awesome_header_width', 'full' ) );
}

/**
 * Whether the header search icon/modal is enabled.
 */
function awesome_header_search_enabled(): bool {
	return (bool) get_theme_mod( 'awesome_header_search', true );
}

/**
 * Sanitize header menu font size (rem value without unit).
 *
 * @param mixed $value Raw value.
 */
function awesome_sanitize_header_menu_font_size( $value ): string {
	$allowed = array( '0.875', '1', '1.125', '1.25', '1.375', '1.5' );
	$value   = is_string( $value ) || is_numeric( $value ) ? (string) $value : '1.125';

	return in_array( $value, $allowed, true ) ? $value : '1.125';
}

/**
 * Resolved header menu font size with rem unit.
 */
function awesome_get_header_menu_font_size(): string {
	return awesome_sanitize_header_menu_font_size( get_theme_mod( 'awesome_header_menu_font_size', '1.125' ) ) . 'rem';
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
