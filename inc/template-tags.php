<?php
/**
 * Template tags and helpers.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site name for header branding.
 */
function awesome_get_site_name(): string {
	$display = get_theme_mod( 'awesome_header_display', 'site_title' );

	if ( 'custom' === $display ) {
		$custom = get_theme_mod( 'awesome_custom_site_name', '' );
		if ( is_string( $custom ) && $custom !== '' ) {
			return $custom;
		}
	}

	return get_bloginfo( 'name', 'display' );
}

/**
 * Whether header text should be shown.
 */
function awesome_show_header_text(): bool {
	return 'logo_only' !== get_theme_mod( 'awesome_header_display', 'site_title' );
}

/**
 * Render site logo / title link.
 */
function awesome_site_branding(): void {
	$home_url = esc_url( home_url( '/' ) );
	$name     = awesome_get_site_name();
	$show_text = awesome_show_header_text();

	echo '<a href="' . $home_url . '" class="site-logo-wrapper" rel="home">';

	if ( has_custom_logo() ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		echo wp_get_attachment_image( $logo_id, 'full', false, array(
			'class' => 'site-logo',
			'alt'   => $show_text ? '' : esc_attr( $name ),
		) );
	}

	if ( $show_text && $name !== '' ) {
		echo '<span class="site-title">' . esc_html( $name ) . '</span>';
	}

	echo '</a>';
}

/**
 * Footer copyright line.
 */
function awesome_footer_copyright(): string {
	$custom = get_theme_mod( 'awesome_footer_copyright', '' );
	if ( is_string( $custom ) && $custom !== '' ) {
		return $custom;
	}

	return sprintf(
		/* translators: 1: year, 2: site name */
		__( '© %1$s %2$s. All rights reserved.', 'awesome' ),
		gmdate( 'Y' ),
		awesome_get_site_name()
	);
}

/**
 * Render single post meta: date, author, categories, and tags.
 */
function awesome_single_post_meta(): void {
	$author_id = (int) get_the_author_meta( 'ID' );
	$categories = get_the_category();
	$tags       = get_the_tags();

	echo '<div class="single-post__meta">';

	echo '<time class="single-post__meta-date" datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">';
	echo esc_html( get_the_date() );
	echo '</time>';

	echo '<div class="single-post__meta-details">';

	if ( $author_id > 0 ) {
		echo '<span class="single-post__meta-item single-post__meta-author">';
		echo '<span class="single-post__meta-label">' . esc_html__( 'By', 'awesome' ) . '</span> ';
		echo '<a href="' . esc_url( get_author_posts_url( $author_id ) ) . '">';
		echo esc_html( get_the_author() );
		echo '</a>';
		echo '</span>';
	}

	if ( is_array( $categories ) && $categories !== array() ) {
		$category_links = array();

		foreach ( $categories as $category ) {
			$category_links[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( get_category_link( $category->term_id ) ),
				esc_html( $category->name )
			);
		}

		echo '<span class="single-post__meta-item single-post__meta-categories">';
		echo '<span class="single-post__meta-label">' . esc_html__( 'In', 'awesome' ) . '</span> ';
		echo wp_kses_post( implode( ', ', $category_links ) );
		echo '</span>';
	}

	if ( is_array( $tags ) && $tags !== array() ) {
		$tag_links = array();

		foreach ( $tags as $tag ) {
			$tag_links[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( get_tag_link( $tag->term_id ) ),
				esc_html( $tag->name )
			);
		}

		echo '<span class="single-post__meta-item single-post__meta-tags">';
		echo '<span class="single-post__meta-label">' . esc_html__( 'Tags:', 'awesome' ) . '</span> ';
		echo wp_kses_post( implode( ', ', $tag_links ) );
		echo '</span>';
	}

	echo '</div>';
	echo '</div>';
}

/**
 * Render archive listing meta: date and categories.
 */
function awesome_archive_post_meta(): void {
	$categories = get_the_category();

	echo '<div class="archive-post__meta">';

	echo '<time class="archive-post__meta-date" datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">';
	echo esc_html( get_the_date() );
	echo '</time>';

	if ( is_array( $categories ) && $categories !== array() ) {
		$category_links = array();

		foreach ( $categories as $category ) {
			$category_links[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( get_category_link( $category->term_id ) ),
				esc_html( $category->name )
			);
		}

		echo '<span class="archive-post__meta-item archive-post__meta-categories">';
		echo '<span class="archive-post__meta-label">' . esc_html__( 'In', 'awesome' ) . '</span> ';
		echo wp_kses_post( implode( ', ', $category_links ) );
		echo '</span>';
	}

	echo '</div>';
}

/**
 * Main content wrapper class.
 */
function awesome_content_class(): string {
	$classes = array( 'wp-content' );

	if ( is_singular( 'post' ) ) {
		$classes[] = 'single-post__content';
	}

	return implode( ' ', $classes );
}
