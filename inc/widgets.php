<?php
/**
 * Widget areas.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer sidebar IDs (up to 5 columns).
 *
 * @return string[]
 */
function awesome_footer_sidebar_ids(): array {
	return array( 'footer-1', 'footer-2', 'footer-3', 'footer-4', 'footer-5' );
}

/**
 * Register widget areas.
 */
function awesome_register_widget_areas(): void {
	foreach ( awesome_footer_sidebar_ids() as $index => $sidebar_id ) {
		$number = $index + 1;

		register_sidebar(
			array(
				/* translators: %d: footer column number */
				'name'          => sprintf( __( 'Footer Column %d', 'awesome' ), $number ),
				'id'            => $sidebar_id,
				'description'   => __( 'Add a Navigation Menu or Custom HTML widget. Use one column for a horizontal footer bar; use multiple columns for a vertical grid.', 'awesome' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="footer-column-title widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

	register_sidebar(
		array(
			'name'          => __( 'Single Post Sidebar', 'awesome' ),
			'id'            => 'sidebar-single',
			'description'   => __( 'Sticky sidebar shown on single posts when enabled.', 'awesome' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'awesome_register_widget_areas' );

/**
 * Whether the single post sidebar should render.
 */
function awesome_show_single_sidebar(): bool {
	if ( ! is_singular( 'post' ) || ! is_active_sidebar( 'sidebar-single' ) ) {
		return false;
	}

	$post_id = (int) get_the_ID();
	if ( $post_id > 0 ) {
		$override = get_post_meta( $post_id, 'awesome_sticky_sidebar', true );
		if ( '0' === $override || '1' === $override ) {
			return '1' === $override;
		}
	}

	return (bool) get_theme_mod( 'awesome_single_sticky_sidebar', false );
}

/**
 * Footer sidebars that currently have widgets.
 *
 * @return string[]
 */
function awesome_get_active_footer_sidebars(): array {
	$active = array();

	foreach ( awesome_footer_sidebar_ids() as $sidebar_id ) {
		if ( is_active_sidebar( $sidebar_id ) ) {
			$active[] = $sidebar_id;
		}
	}

	return $active;
}

/**
 * Whether any footer widget area is in use.
 */
function awesome_has_footer_widgets(): bool {
	return awesome_get_active_footer_sidebars() !== array();
}

/**
 * Render footer widget columns (Nuxt-style layout).
 *
 * One active column  → centered title + horizontal links.
 * Multiple columns → vertical content per column in a grid.
 */
function awesome_render_footer_widgets(): void {
	$active = awesome_get_active_footer_sidebars();

	if ( $active === array() ) {
		return;
	}

	$count       = count( $active );
	$is_single   = 1 === $count;
	$classes     = array( 'footer-columns' );

	if ( $is_single ) {
		$classes[] = 'single-column';
	} else {
		$classes[] = 'is-multi-column';
	}

	printf(
		'<div class="%1$s" data-footer-columns="%2$d">',
		esc_attr( implode( ' ', $classes ) ),
		$count
	);

	foreach ( $active as $sidebar_id ) {
		echo '<div class="footer-column">';
		dynamic_sidebar( $sidebar_id );
		echo '</div>';
	}

	echo '</div>';
}
