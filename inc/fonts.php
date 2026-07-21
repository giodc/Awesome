<?php
/**
 * Performance-minded theme fonts (curated list, load only selections).
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Curated font catalog.
 *
 * System stacks load nothing from the network.
 * Google fonts are loaded only when selected.
 *
 * @return array<string, array{label: string, family: string, stack: string, google: string|null, weights: string}>
 */
function awesome_get_font_catalog(): array {
	return array(
		'system'           => array(
			'label'   => __( 'System UI (fastest)', 'awesome' ),
			'family'  => 'system-ui',
			'stack'   => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
			'google'  => null,
			'weights' => '',
		),
		'georgia'          => array(
			'label'   => __( 'Georgia (system serif)', 'awesome' ),
			'family'  => 'Georgia',
			'stack'   => 'Georgia, "Times New Roman", serif',
			'google'  => null,
			'weights' => '',
		),
		'instrument-sans'  => array(
			'label'   => 'Instrument Sans',
			'family'  => 'Instrument Sans',
			'stack'   => '"Instrument Sans", sans-serif',
			'google'  => 'Instrument+Sans',
			'weights' => '400;500;600;700',
		),
		'inter'            => array(
			'label'   => 'Inter',
			'family'  => 'Inter',
			'stack'   => '"Inter", sans-serif',
			'google'  => 'Inter',
			'weights' => '400;500;600;700',
		),
		'source-sans-3'    => array(
			'label'   => 'Source Sans 3',
			'family'  => 'Source Sans 3',
			'stack'   => '"Source Sans 3", sans-serif',
			'google'  => 'Source+Sans+3',
			'weights' => '400;500;600;700',
		),
		'dm-sans'          => array(
			'label'   => 'DM Sans',
			'family'  => 'DM Sans',
			'stack'   => '"DM Sans", sans-serif',
			'google'  => 'DM+Sans',
			'weights' => '400;500;600;700',
		),
		'nunito-sans'      => array(
			'label'   => 'Nunito Sans',
			'family'  => 'Nunito Sans',
			'stack'   => '"Nunito Sans", sans-serif',
			'google'  => 'Nunito+Sans',
			'weights' => '400;500;600;700',
		),
		'libre-franklin'   => array(
			'label'   => 'Libre Franklin',
			'family'  => 'Libre Franklin',
			'stack'   => '"Libre Franklin", sans-serif',
			'google'  => 'Libre+Franklin',
			'weights' => '400;500;600;700',
		),
		'lora'             => array(
			'label'   => 'Lora',
			'family'  => 'Lora',
			'stack'   => '"Lora", serif',
			'google'  => 'Lora',
			'weights' => '400;500;600;700',
		),
		'merriweather'     => array(
			'label'   => 'Merriweather',
			'family'  => 'Merriweather',
			'stack'   => '"Merriweather", serif',
			'google'  => 'Merriweather',
			'weights' => '400;700',
		),
		'playfair-display' => array(
			'label'   => 'Playfair Display',
			'family'  => 'Playfair Display',
			'stack'   => '"Playfair Display", serif',
			'google'  => 'Playfair+Display',
			'weights' => '400;500;600;700',
		),
		'fraunces'         => array(
			'label'   => 'Fraunces',
			'family'  => 'Fraunces',
			'stack'   => '"Fraunces", serif',
			'google'  => 'Fraunces',
			'weights' => '400;500;600;700',
		),
	);
}

/**
 * Font role keys and defaults.
 *
 * @return array<string, string> Role => default catalog slug.
 */
function awesome_font_role_defaults(): array {
	return array(
		'body'    => 'instrument-sans',
		'heading' => 'instrument-sans',
		'header'  => 'instrument-sans',
	);
}

/**
 * Theme mod key for a font role.
 *
 * @param string $role Role key.
 */
function awesome_font_mod_key( string $role ): string {
	return 'awesome_font_' . $role;
}

/**
 * Sanitize a font catalog slug.
 *
 * @param mixed $value Raw value.
 */
function awesome_sanitize_font_slug( $value ): string {
	$value   = is_string( $value ) ? $value : '';
	$catalog = awesome_get_font_catalog();

	return array_key_exists( $value, $catalog ) ? $value : 'instrument-sans';
}

/**
 * Selected font slug for a role.
 *
 * @param string $role Role key.
 */
function awesome_get_font_slug( string $role ): string {
	$defaults = awesome_font_role_defaults();
	$default  = $defaults[ $role ] ?? 'instrument-sans';

	return awesome_sanitize_font_slug( get_theme_mod( awesome_font_mod_key( $role ), $default ) );
}

/**
 * CSS font stack for a role.
 *
 * @param string $role Role key.
 */
function awesome_get_font_stack( string $role ): string {
	$slug    = awesome_get_font_slug( $role );
	$catalog = awesome_get_font_catalog();

	return $catalog[ $slug ]['stack'] ?? $catalog['instrument-sans']['stack'];
}

/**
 * Unique Google font families required by current selections.
 *
 * @return array<string, array{google: string, weights: string}>
 */
function awesome_get_required_google_fonts(): array {
	$catalog = awesome_get_font_catalog();
	$needed  = array();

	foreach ( array_keys( awesome_font_role_defaults() ) as $role ) {
		$slug = awesome_get_font_slug( $role );
		$font = $catalog[ $slug ] ?? null;

		if ( ! is_array( $font ) || empty( $font['google'] ) ) {
			continue;
		}

		$needed[ $font['google'] ] = array(
			'google'  => $font['google'],
			'weights' => $font['weights'],
		);
	}

	return $needed;
}

/**
 * Build a single Google Fonts CSS2 URL for required families.
 */
function awesome_get_google_fonts_url(): string {
	$families = awesome_get_required_google_fonts();

	if ( $families === array() ) {
		return '';
	}

	$parts = array();

	foreach ( $families as $font ) {
		$parts[] = 'family=' . $font['google'] . ':wght@' . $font['weights'];
	}

	return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
}

/**
 * Enqueue only the Google fonts currently selected.
 */
function awesome_enqueue_fonts(): void {
	$url = awesome_get_google_fonts_url();

	if ( $url === '' ) {
		return;
	}

	wp_enqueue_style( 'awesome-fonts', $url, array(), null );
}
add_action( 'wp_enqueue_scripts', 'awesome_enqueue_fonts', 5 );

/**
 * Preconnect to Google Fonts only when a remote font is used.
 *
 * @param array<int, string|array<string, mixed>> $urls          Current URLs.
 * @param string                                  $relation_type Relation type.
 * @return array<int, string|array<string, mixed>>
 */
function awesome_font_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' !== $relation_type || awesome_get_required_google_fonts() === array() ) {
		return $urls;
	}

	$urls[] = 'https://fonts.googleapis.com';
	$urls[] = array(
		'href'        => 'https://fonts.gstatic.com',
		'crossorigin' => 'anonymous',
	);

	return $urls;
}
add_filter( 'wp_resource_hints', 'awesome_font_resource_hints', 10, 2 );

/**
 * Choices for Customizer font selects.
 *
 * @return array<string, string>
 */
function awesome_get_font_choices(): array {
	$choices = array();

	foreach ( awesome_get_font_catalog() as $slug => $font ) {
		$suffix = null === $font['google']
			? __( 'local', 'awesome' )
			: __( 'Google', 'awesome' );

		$choices[ $slug ] = sprintf( '%1$s (%2$s)', $font['label'], $suffix );
	}

	return $choices;
}
