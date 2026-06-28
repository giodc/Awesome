<?php
/**
 * Lightweight post sharing.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Share destinations for a post.
 *
 * @param int $post_id Post ID.
 * @return array<string, array{label: string, url: string, external: bool}>
 */
function awesome_get_post_share_links( int $post_id = 0 ): array {
	$post_id = $post_id > 0 ? $post_id : get_the_ID();

	if ( $post_id <= 0 ) {
		return array();
	}

	$url   = get_permalink( $post_id );
	$title = get_the_title( $post_id );

	if ( ! is_string( $url ) || $url === '' ) {
		return array();
	}

	$encoded_url   = rawurlencode( $url );
	$encoded_title = rawurlencode( html_entity_decode( wp_strip_all_tags( $title ), ENT_QUOTES, get_bloginfo( 'charset' ) ) );

	$links = array(
		'facebook' => array(
			'label'    => __( 'Facebook', 'awesome' ),
			'url'      => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
			'external' => true,
		),
		'x'        => array(
			'label'    => _x( 'X', 'Share on X (Twitter)', 'awesome' ),
			'url'      => 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title,
			'external' => true,
		),
		'linkedin' => array(
			'label'    => __( 'LinkedIn', 'awesome' ),
			'url'      => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url,
			'external' => true,
		),
		'whatsapp' => array(
			'label'    => __( 'WhatsApp', 'awesome' ),
			'url'      => 'https://wa.me/?text=' . rawurlencode( html_entity_decode( wp_strip_all_tags( $title ), ENT_QUOTES, get_bloginfo( 'charset' ) ) . ' ' . $url ),
			'external' => true,
		),
		'email'    => array(
			'label'    => __( 'Email', 'awesome' ),
			'url'      => 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_url,
			'external' => false,
		),
	);

	if ( has_post_thumbnail( $post_id ) ) {
		$image_url = wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'large' );

		if ( is_string( $image_url ) && $image_url !== '' ) {
			$links['pinterest'] = array(
				'label'    => __( 'Pinterest', 'awesome' ),
				'url'      => 'https://pinterest.com/pin/create/button/?url=' . $encoded_url . '&media=' . rawurlencode( $image_url ) . '&description=' . $encoded_title,
				'external' => true,
			);
		}
	}

	return $links;
}

/**
 * Output share section for single posts.
 *
 * @param int $post_id Post ID.
 */
function awesome_the_post_share( int $post_id = 0 ): void {
	$post_id = $post_id > 0 ? $post_id : get_the_ID();

	if ( $post_id <= 0 ) {
		return;
	}

	$url   = get_permalink( $post_id );
	$links = awesome_get_post_share_links( $post_id );

	if ( ! is_string( $url ) || $url === '' ) {
		return;
	}

	echo '<aside class="single-post__share" aria-label="' . esc_attr__( 'Share this post', 'awesome' ) . '">';
	echo '<p class="single-post__share-label">' . esc_html__( 'Share', 'awesome' ) . '</p>';
	echo '<ul class="single-post__share-list">';

	echo '<li>';
	echo '<button type="button" class="single-post__share-action" data-share="copy" data-url="' . esc_url( $url ) . '">';
	echo esc_html__( 'Copy link', 'awesome' );
	echo '</button>';
	echo '</li>';

	echo '<li class="single-post__share-native" hidden>';
	echo '<button type="button" class="single-post__share-action" data-share="native" data-url="' . esc_url( $url ) . '" data-title="' . esc_attr( get_the_title( $post_id ) ) . '">';
	echo esc_html__( 'Share…', 'awesome' );
	echo '</button>';
	echo '</li>';

	foreach ( $links as $slug => $link ) {
		echo '<li>';
		printf(
			'<a class="single-post__share-action" href="%1$s"%2$s>%3$s</a>',
			esc_url( $link['url'] ),
			$link['external'] ? ' target="_blank" rel="noopener noreferrer"' : '',
			esc_html( $link['label'] )
		);
		echo '</li>';
	}

	echo '</ul>';
	echo '<p class="single-post__share-feedback" role="status" aria-live="polite" hidden></p>';
	echo '</aside>';
}
