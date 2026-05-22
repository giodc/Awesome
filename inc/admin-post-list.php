<?php
/**
 * Admin post list enhancements.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add featured image and status columns to the posts list table.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function awesome_posts_list_columns( array $columns ): array {
	$new_columns = array();

	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new_columns['awesome_thumbnail'] = __( 'Image', 'awesome' );
		}

		$new_columns[ $key ] = $label;

		if ( 'title' === $key ) {
			$new_columns['awesome_status'] = __( 'Status', 'awesome' );
		}
	}

	return $new_columns;
}
add_filter( 'manage_posts_columns', 'awesome_posts_list_columns' );

/**
 * Render custom post list columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function awesome_posts_list_column_content( string $column, int $post_id ): void {
	if ( 'awesome_thumbnail' === $column ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo '<a href="' . esc_url( get_edit_post_link( $post_id ) ) . '" class="awesome-post-list-thumb">';
			echo get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'width:60px;height:60px;object-fit:cover;border-radius:4px;' ) );
			echo '</a>';
		} else {
			echo '<span aria-hidden="true">—</span>';
			echo '<span class="screen-reader-text">' . esc_html__( 'No featured image', 'awesome' ) . '</span>';
		}
		return;
	}

	if ( 'awesome_status' === $column ) {
		awesome_render_editorial_status_badge( $post_id );
	}
}
add_action( 'manage_posts_custom_column', 'awesome_posts_list_column_content', 10, 2 );

/**
 * Output editorial status filter dropdown on the posts list screen.
 *
 * @param string $post_type Current post type.
 */
function awesome_posts_list_status_filter( string $post_type ): void {
	if ( 'post' !== $post_type ) {
		return;
	}

	$selected = isset( $_GET['awesome_editorial_status'] )
		? awesome_sanitize_editorial_status( wp_unslash( $_GET['awesome_editorial_status'] ) )
		: '';

	echo '<select name="awesome_editorial_status" id="awesome-editorial-status-filter">';
	echo '<option value="">' . esc_html__( 'All statuses', 'awesome' ) . '</option>';

	foreach ( awesome_get_editorial_statuses() as $slug => $label ) {
		printf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $slug ),
			selected( $selected, $slug, false ),
			esc_html( $label )
		);
	}

	echo '</select>';
}
add_action( 'restrict_manage_posts', 'awesome_posts_list_status_filter' );

/**
 * Filter posts list by editorial status.
 *
 * @param WP_Query $query Main query.
 */
function awesome_posts_list_status_filter_query( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'edit' !== $screen->base || 'post' !== $screen->post_type ) {
		return;
	}

	$status = isset( $_GET['awesome_editorial_status'] )
		? awesome_sanitize_editorial_status( wp_unslash( $_GET['awesome_editorial_status'] ) )
		: '';

	if ( $status === '' ) {
		return;
	}

	$query->set(
		'meta_query',
		array(
			array(
				'key'   => awesome_status_meta_key(),
				'value' => $status,
			),
		)
	);
}
add_action( 'pre_get_posts', 'awesome_posts_list_status_filter_query' );

/**
 * Set column widths for custom columns.
 */
function awesome_posts_list_admin_styles(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'edit' !== $screen->base || 'post' !== $screen->post_type ) {
		return;
	}

	$css = '
		.column-awesome_thumbnail { width: 72px; text-align: center; }
		.column-awesome_status { width: 140px; }
		.awesome-post-list-thumb { display: inline-block; line-height: 0; }
		.awesome-status {
			border-radius: 4px;
			display: inline-block;
			font-size: 12px;
			font-weight: 600;
			line-height: 1.3;
			padding: 4px 8px;
			white-space: nowrap;
		}
		.awesome-status--none { color: #646970; font-weight: 400; }
		.awesome-status--completed { background: #d1e7dd; color: #0a3622; }
		.awesome-status--working_on { background: #cfe2ff; color: #052c65; }
		.awesome-status--awaiting_review { background: #fff3cd; color: #664d03; }
	';

	wp_add_inline_style( 'wp-admin', $css );
}
add_action( 'admin_head', 'awesome_posts_list_admin_styles' );
