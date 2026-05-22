<?php
/**
 * Post meta fields.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta key for the post subtitle.
 */
function awesome_subtitle_meta_key(): string {
	return 'subtitle';
}

/**
 * Register post meta.
 */
function awesome_register_post_meta(): void {
	register_post_meta(
		'post',
		awesome_subtitle_meta_key(),
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => static function (): bool {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'awesome_register_post_meta' );

/**
 * Add subtitle field to the post editor.
 */
function awesome_add_subtitle_meta_box(): void {
	add_meta_box(
		'awesome-subtitle',
		__( 'Subtitle', 'awesome' ),
		'awesome_render_subtitle_meta_box',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'awesome_add_subtitle_meta_box' );

/**
 * Render subtitle meta box.
 *
 * @param WP_Post $post Current post.
 */
function awesome_render_subtitle_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'awesome_save_subtitle', 'awesome_subtitle_nonce' );

	$subtitle = get_post_meta( $post->ID, awesome_subtitle_meta_key(), true );
	$subtitle = is_string( $subtitle ) ? $subtitle : '';

	echo '<p>';
	echo '<label class="screen-reader-text" for="awesome-subtitle-field">';
	echo esc_html__( 'Subtitle', 'awesome' );
	echo '</label>';
	echo '<input type="text" id="awesome-subtitle-field" name="awesome_subtitle" value="' . esc_attr( $subtitle ) . '" class="widefat" placeholder="' . esc_attr__( 'Optional subtitle shown below the title', 'awesome' ) . '">';
	echo '</p>';
}

/**
 * Save subtitle meta box value.
 *
 * @param int $post_id Post ID.
 */
function awesome_save_subtitle_meta_box( int $post_id ): void {
	if ( ! isset( $_POST['awesome_subtitle_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['awesome_subtitle_nonce'] ) ), 'awesome_save_subtitle' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$subtitle = isset( $_POST['awesome_subtitle'] )
		? sanitize_text_field( wp_unslash( $_POST['awesome_subtitle'] ) )
		: '';

	if ( $subtitle === '' ) {
		delete_post_meta( $post_id, awesome_subtitle_meta_key() );
		return;
	}

	update_post_meta( $post_id, awesome_subtitle_meta_key(), $subtitle );
}
add_action( 'save_post', 'awesome_save_subtitle_meta_box' );

/**
 * Get the current post subtitle.
 */
function awesome_get_post_subtitle(): string {
	$subtitle = get_post_meta( get_the_ID(), awesome_subtitle_meta_key(), true );

	return is_string( $subtitle ) ? $subtitle : '';
}

/**
 * Output post subtitle as h2 when set.
 */
function awesome_the_post_subtitle(): void {
	$subtitle = awesome_get_post_subtitle();

	if ( $subtitle === '' ) {
		return;
	}

	echo '<h2 class="single-post__subtitle entry-subtitle">';
	echo esc_html( $subtitle );
	echo '</h2>';
}
