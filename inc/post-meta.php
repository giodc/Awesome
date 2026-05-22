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
 * Meta key for editorial status (admin only).
 */
function awesome_status_meta_key(): string {
	return 'awesome_editorial_status';
}

/**
 * Allowed editorial status options.
 *
 * @return array<string, string> Status slug => label.
 */
function awesome_get_editorial_statuses(): array {
	return array(
		'completed'       => __( 'Completed', 'awesome' ),
		'working_on'      => __( 'Working On', 'awesome' ),
		'awaiting_review' => __( 'Awaiting Review', 'awesome' ),
	);
}

/**
 * Sanitize editorial status value.
 *
 * @param mixed $value Raw status value.
 */
function awesome_sanitize_editorial_status( $value ): string {
	$value = is_string( $value ) ? $value : '';

	return array_key_exists( $value, awesome_get_editorial_statuses() ) ? $value : '';
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

	register_post_meta(
		'post',
		awesome_status_meta_key(),
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'awesome_sanitize_editorial_status',
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
 * Add status field to the post editor.
 */
function awesome_add_status_meta_box(): void {
	add_meta_box(
		'awesome-status',
		__( 'Status', 'awesome' ),
		'awesome_render_status_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'awesome_add_status_meta_box' );

/**
 * Render status meta box.
 *
 * @param WP_Post $post Current post.
 */
function awesome_render_status_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'awesome_save_status', 'awesome_status_nonce' );

	$current = get_post_meta( $post->ID, awesome_status_meta_key(), true );
	$current = awesome_sanitize_editorial_status( $current );

	echo '<p>';
	echo '<label for="awesome-status-field" class="screen-reader-text">';
	echo esc_html__( 'Status', 'awesome' );
	echo '</label>';
	echo '<select id="awesome-status-field" name="awesome_editorial_status" class="widefat">';
	echo '<option value="">' . esc_html__( '— No status —', 'awesome' ) . '</option>';

	foreach ( awesome_get_editorial_statuses() as $slug => $label ) {
		printf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $slug ),
			selected( $current, $slug, false ),
			esc_html( $label )
		);
	}

	echo '</select>';
	echo '</p>';
}

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
 * Save status meta box value.
 *
 * @param int $post_id Post ID.
 */
function awesome_save_status_meta_box( int $post_id ): void {
	if ( ! isset( $_POST['awesome_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['awesome_status_nonce'] ) ), 'awesome_save_status' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$status = isset( $_POST['awesome_editorial_status'] )
		? awesome_sanitize_editorial_status( wp_unslash( $_POST['awesome_editorial_status'] ) )
		: '';

	if ( $status === '' ) {
		delete_post_meta( $post_id, awesome_status_meta_key() );
		return;
	}

	update_post_meta( $post_id, awesome_status_meta_key(), $status );
}
add_action( 'save_post', 'awesome_save_status_meta_box' );

/**
 * Get a post's editorial status slug.
 *
 * @param int $post_id Post ID.
 */
function awesome_get_post_editorial_status( int $post_id = 0 ): string {
	$post_id = $post_id > 0 ? $post_id : (int) get_the_ID();

	if ( $post_id <= 0 ) {
		return '';
	}

	$status = get_post_meta( $post_id, awesome_status_meta_key(), true );

	return awesome_sanitize_editorial_status( $status );
}

/**
 * Output a color-coded editorial status badge (admin use).
 *
 * @param int $post_id Post ID.
 */
function awesome_render_editorial_status_badge( int $post_id ): void {
	$status = awesome_get_post_editorial_status( $post_id );

	if ( $status === '' ) {
		echo '<span class="awesome-status awesome-status--none">—</span>';
		return;
	}

	$labels = awesome_get_editorial_statuses();
	$label  = $labels[ $status ] ?? $status;

	printf(
		'<span class="awesome-status awesome-status--%1$s">%2$s</span>',
		esc_attr( $status ),
		esc_html( $label )
	);
}

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
