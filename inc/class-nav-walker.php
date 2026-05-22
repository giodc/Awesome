<?php
/**
 * Navigation menu walkers.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Desktop header menu with dropdown submenus.
 */
class Awesome_Desktop_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Start submenu level.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ): void {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"sub-menu dropdown-content\" role=\"menu\">\n";
	}

	/**
	 * Start menu item.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ): void {
		$indent      = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$has_children = in_array( 'menu-item-has-children', $item->classes, true );
		$classes     = empty( $item->classes ) ? array() : (array) $item->classes;

		if ( 0 === $depth && $has_children ) {
			$classes[] = 'menu-item-has-children';
			$classes[] = 'dropdown-menu';
		}

		$class_names = $classes ? ' class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '"' : '';
		$output     .= $indent . '<li' . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		if ( 0 === $depth && $has_children ) {
			$output .= '<div class="dropdown-toggle">';
			$output .= '<a class="dropdown-label menu-item" href="' . esc_url( $atts['href'] ) . '">';
			$output .= esc_html( $item->title );
			$output .= '</a>';
			$output .= '<button type="button" class="dropdown-arrow-btn" aria-expanded="false" aria-label="' . esc_attr__( 'Toggle submenu', 'awesome' ) . '">';
			$output .= '<span class="dropdown-arrow" aria-hidden="true">›</span>';
			$output .= '</button>';
			$output .= '</div>';
			return;
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$link_class = ( $depth > 0 ) ? 'dropdown-item menu-item' : 'menu-item';
		$output    .= '<a class="' . esc_attr( $link_class ) . '"' . $attributes . '>';
		$output    .= esc_html( $item->title );
		$output    .= '</a>';
	}

	/**
	 * End menu item.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ): void {
		$output .= "</li>\n";
	}
}

/**
 * Mobile off-canvas menu with expandable submenus.
 */
class Awesome_Mobile_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Start submenu.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ): void {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"sub-menu mobile-sub-menu\" hidden>\n";
	}

	/**
	 * Start menu item.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ): void {
		$indent       = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$has_children = in_array( 'menu-item-has-children', $item->classes, true );
		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;

		if ( $has_children ) {
			$classes[] = 'menu-item-has-children';
		}

		$class_names = $classes ? ' class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '"' : '';
		$output     .= $indent . '<li' . $class_names . '>';

		$atts         = array();
		$atts['href'] = ! empty( $item->url ) ? $item->url : '';

		if ( $has_children ) {
			$output .= '<div class="mobile-menu-item-row">';
			$output .= '<a class="menu-item" href="' . esc_url( $atts['href'] ) . '">' . esc_html( $item->title ) . '</a>';
			$output .= '<button type="button" class="mobile-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Expand submenu', 'awesome' ) . '">';
			$output .= '<span class="mobile-submenu-icon" aria-hidden="true">›</span>';
			$output .= '</button>';
			$output .= '</div>';
			return;
		}

		$output .= '<a class="menu-item" href="' . esc_url( $atts['href'] ) . '">' . esc_html( $item->title ) . '</a>';
	}

	/**
	 * End menu item.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ): void {
		$output .= "</li>\n";
	}
}
