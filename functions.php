<?php
/**
 * Awesome theme functions.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AWESOME_VERSION', '1.0.0' );
define( 'AWESOME_DIR', get_template_directory() );
define( 'AWESOME_URI', get_template_directory_uri() );

require_once AWESOME_DIR . '/inc/defaults.php';
require_once AWESOME_DIR . '/inc/customizer.php';
require_once AWESOME_DIR . '/inc/class-nav-walker.php';
require_once AWESOME_DIR . '/inc/template-tags.php';
require_once AWESOME_DIR . '/inc/widgets.php';

/**
 * Theme setup.
 */
function awesome_setup(): void {
	load_theme_textdomain( 'awesome', AWESOME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	register_nav_menus( array(
		'header' => __( 'Header Menu', 'awesome' ),
	) );
}
add_action( 'after_setup_theme', 'awesome_setup' );

/**
 * Enqueue front-end assets.
 */
function awesome_enqueue_assets(): void {
	wp_enqueue_style(
		'awesome-fonts',
		'https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'awesome-main',
		AWESOME_URI . '/assets/css/main.css',
		array( 'awesome-fonts' ),
		AWESOME_VERSION
	);

	wp_enqueue_script(
		'awesome-navigation',
		AWESOME_URI . '/assets/js/navigation.js',
		array(),
		AWESOME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'awesome_enqueue_assets' );

/**
 * Editor styles.
 */
function awesome_editor_styles(): void {
	add_editor_style( 'assets/css/main.css' );
}
add_action( 'after_setup_theme', 'awesome_editor_styles' );

/**
 * Output theme CSS variables from the Customizer.
 */
function awesome_inline_theme_vars(): void {
	$colors = awesome_get_theme_colors();
	$css    = sprintf(
		':root{--color-primary:%1$s;--color-secondary:%2$s;--color-accents:%3$s;--color-background:%4$s;--color-text:%5$s;--color-content-link:%6$s;--color-heading:%7$s;--color-heading-hover:%8$s;--color-text-hover:%9$s;--footer-background:%10$s;--footer-text:%11$s;--footer-text-hover:%12$s;}',
		esc_attr( $colors['primary'] ),
		esc_attr( $colors['secondary'] ),
		esc_attr( $colors['accents'] ),
		esc_attr( $colors['background'] ),
		esc_attr( $colors['text'] ),
		esc_attr( $colors['content_link'] ),
		esc_attr( $colors['heading'] ),
		esc_attr( $colors['heading_hover'] ),
		esc_attr( $colors['text_hover'] ),
		esc_attr( $colors['footer_bg'] ),
		esc_attr( $colors['footer_text'] ),
		esc_attr( $colors['footer_text_hover'] )
	);

	wp_add_inline_style( 'awesome-main', $css );
}
add_action( 'wp_enqueue_scripts', 'awesome_inline_theme_vars', 20 );

/**
 * Content width for embeds and media.
 */
function awesome_content_width(): void {
	$GLOBALS['content_width'] = 800;
}
add_action( 'after_setup_theme', 'awesome_content_width', 0 );

/**
 * Sync block editor color palette with Customizer values.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data.
 */
function awesome_filter_theme_json( WP_Theme_JSON_Data $theme_json ): WP_Theme_JSON_Data {
	$colors = awesome_get_theme_colors();
	$data   = $theme_json->get_data();

	$data['settings']['color']['palette'] = array(
		array( 'slug' => 'primary', 'color' => $colors['primary'], 'name' => 'Primary' ),
		array( 'slug' => 'secondary', 'color' => $colors['secondary'], 'name' => 'Secondary' ),
		array( 'slug' => 'accents', 'color' => $colors['accents'], 'name' => 'Accents' ),
		array( 'slug' => 'background', 'color' => $colors['background'], 'name' => 'Background' ),
		array( 'slug' => 'text', 'color' => $colors['text'], 'name' => 'Text' ),
		array( 'slug' => 'heading', 'color' => $colors['heading'], 'name' => 'Heading' ),
		array( 'slug' => 'footer', 'color' => $colors['footer_bg'], 'name' => 'Footer' ),
	);

	return $theme_json->update_with( $data );
}
add_filter( 'wp_theme_json_data_theme', 'awesome_filter_theme_json' );

/**
 * Remove taxonomy prefixes from category and tag archive titles.
 *
 * @param string $title Archive title.
 */
function awesome_category_archive_title( string $title ): string {
	if ( is_category() ) {
		return single_cat_title( '', false );
	}

	if ( is_tag() ) {
		return single_tag_title( '', false );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'awesome_category_archive_title' );
