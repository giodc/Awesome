<?php
/**
 * Expose Yoast SEO post meta via the REST API.
 *
 * Based on SEO Fields API Support by Dmytro Verzhykovskyi.
 *
 * @see https://github.com/verzhykovskyi/seo-fields-api-support
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Yoast SEO meta keys exposed to the REST API.
 *
 * @return string[]
 */
function awesome_yoast_rest_meta_keys(): array {
	return array(
		'_yoast_wpseo_title',
		'_yoast_wpseo_metadesc',
		'_yoast_wpseo_focuskw',
	);
}

/**
 * Register Yoast SEO meta for a post type.
 *
 * @param string $post_type Post type name.
 */
function awesome_register_yoast_rest_meta( string $post_type ): void {
	$args = array(
		'show_in_rest'      => true,
		'single'            => true,
		'type'              => 'string',
		'auth_callback'     => static function (): bool {
			return current_user_can( 'edit_posts' );
		},
		'sanitize_callback' => 'sanitize_text_field',
	);

	foreach ( awesome_yoast_rest_meta_keys() as $meta_key ) {
		register_post_meta( $post_type, $meta_key, $args );
	}
}

/**
 * Register Yoast fields for all public post types when Yoast SEO is active.
 */
function awesome_yoast_rest_api_init(): void {
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return;
	}

	foreach ( get_post_types( array( 'public' => true ), 'names' ) as $post_type ) {
		awesome_register_yoast_rest_meta( $post_type );
	}
}
add_action( 'rest_api_init', 'awesome_yoast_rest_api_init' );
