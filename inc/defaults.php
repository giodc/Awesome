<?php
/**
 * Theme default values.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default color palette (matches former Nuxt public env vars).
 *
 * @return array<string, string>
 */
function awesome_default_colors(): array {
	return array(
		'primary'        => '#000000',
		'secondary'      => '#6a2929',
		'accents'        => '#8d4b4b',
		'background'     => '#f2f0e5',
		'text'           => '#000000',
		'content_link'   => '#000000',
		'heading'        => '#000000',
		'heading_hover'  => '#6a2929',
		'text_hover'     => '#6a2929',
		'footer_bg'      => '#4a3030',
		'footer_text'    => '#cccccc',
		'footer_text_hover' => '#ffffff',
	);
}

/**
 * Customizer setting keys mapped to color keys.
 *
 * @return array<string, string>
 */
function awesome_color_setting_map(): array {
	return array(
		'awesome_color_primary'        => 'primary',
		'awesome_color_secondary'      => 'secondary',
		'awesome_color_accents'        => 'accents',
		'awesome_color_background'     => 'background',
		'awesome_color_text'           => 'text',
		'awesome_color_content_link'   => 'content_link',
		'awesome_color_heading'        => 'heading',
		'awesome_color_heading_hover'  => 'heading_hover',
		'awesome_color_text_hover'     => 'text_hover',
		'awesome_color_footer_bg'      => 'footer_bg',
		'awesome_color_footer_text'    => 'footer_text',
		'awesome_color_footer_text_hover' => 'footer_text_hover',
	);
}

/**
 * Resolved theme colors from the Customizer.
 *
 * @return array<string, string>
 */
function awesome_get_theme_colors(): array {
	$colors  = awesome_default_colors();
	$setting = awesome_color_setting_map();

	foreach ( $setting as $option => $key ) {
		$value = get_theme_mod( $option, '' );
		if ( is_string( $value ) && $value !== '' ) {
			$colors[ $key ] = sanitize_hex_color( $value ) ?: $colors[ $key ];
		}
	}

	return $colors;
}
