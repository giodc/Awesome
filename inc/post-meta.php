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

	register_post_meta(
		'post',
		'awesome_post_hero',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'default'           => false,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => static function (): bool {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'awesome_sticky_sidebar',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
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
 * Add post layout options meta box.
 */
function awesome_add_layout_meta_box(): void {
	add_meta_box(
		'awesome-layout',
		__( 'Post Layout', 'awesome' ),
		'awesome_render_layout_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'awesome_add_layout_meta_box' );

/**
 * Render post layout meta box.
 *
 * @param WP_Post $post Current post.
 */
function awesome_render_layout_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'awesome_save_layout', 'awesome_layout_nonce' );

	$hero     = (bool) get_post_meta( $post->ID, 'awesome_post_hero', true );
	$sidebar  = get_post_meta( $post->ID, 'awesome_sticky_sidebar', true );
	$sidebar  = is_string( $sidebar ) ? $sidebar : '';

	echo '<p>';
	echo '<label>';
	echo '<input type="checkbox" name="awesome_post_hero" value="1" ' . checked( $hero, true, false ) . '> ';
	echo esc_html__( 'Hero header (uses featured image)', 'awesome' );
	echo '</label>';
	echo '</p>';
	echo '<p class="description">' . esc_html__( 'Shows category, title, and subtitle over the featured image with an overlay.', 'awesome' ) . '</p>';

	echo '<p>';
	echo '<label for="awesome-sticky-sidebar-field">' . esc_html__( 'Sticky sidebar', 'awesome' ) . '</label>';
	echo '<select id="awesome-sticky-sidebar-field" name="awesome_sticky_sidebar" class="widefat">';
	printf( '<option value="" %s>%s</option>', selected( $sidebar, '', false ), esc_html__( 'Use theme default', 'awesome' ) );
	printf( '<option value="1" %s>%s</option>', selected( $sidebar, '1', false ), esc_html__( 'Enabled', 'awesome' ) );
	printf( '<option value="0" %s>%s</option>', selected( $sidebar, '0', false ), esc_html__( 'Disabled', 'awesome' ) );
	echo '</select>';
	echo '</p>';
}

/**
 * Save post layout meta box values.
 *
 * @param int $post_id Post ID.
 */
function awesome_save_layout_meta_box( int $post_id ): void {
	if ( ! isset( $_POST['awesome_layout_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['awesome_layout_nonce'] ) ), 'awesome_save_layout' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$hero = isset( $_POST['awesome_post_hero'] ) ? '1' : '';
	if ( $hero === '' ) {
		delete_post_meta( $post_id, 'awesome_post_hero' );
	} else {
		update_post_meta( $post_id, 'awesome_post_hero', true );
	}

	$sidebar = isset( $_POST['awesome_sticky_sidebar'] )
		? sanitize_text_field( wp_unslash( $_POST['awesome_sticky_sidebar'] ) )
		: '';

	if ( ! in_array( $sidebar, array( '', '0', '1' ), true ) ) {
		$sidebar = '';
	}

	if ( $sidebar === '' ) {
		delete_post_meta( $post_id, 'awesome_sticky_sidebar' );
	} else {
		update_post_meta( $post_id, 'awesome_sticky_sidebar', $sidebar );
	}
}
add_action( 'save_post', 'awesome_save_layout_meta_box' );

/**
 * Whether the current post should use the hero layout.
 *
 * @param int $post_id Post ID.
 */
function awesome_post_has_hero( int $post_id = 0 ): bool {
	$post_id = $post_id > 0 ? $post_id : (int) get_the_ID();

	if ( $post_id <= 0 || ! has_post_thumbnail( $post_id ) ) {
		return false;
	}

	return (bool) get_post_meta( $post_id, 'awesome_post_hero', true );
}

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
 *
 * @param string $class Extra CSS class.
 */
function awesome_the_post_subtitle( string $class = 'single-post__subtitle entry-subtitle' ): void {
	$subtitle = awesome_get_post_subtitle();

	if ( $subtitle === '' ) {
		return;
	}

	echo '<h2 class="' . esc_attr( $class ) . '">';
	echo esc_html( $subtitle );
	echo '</h2>';
}

/**
 * Whether transparent overlay header is active (homepage setting or post hero).
 */
function awesome_has_transparent_header(): bool {
	if ( is_front_page() && (bool) get_theme_mod( 'awesome_homepage_transparent_header', false ) ) {
		return true;
	}

	if ( is_singular( 'post' ) && awesome_post_has_hero() ) {
		return true;
	}

	return false;
}

/**
 * Primary category for a post (first assigned).
 *
 * @param int $post_id Post ID.
 */
function awesome_get_primary_category( int $post_id = 0 ): ?WP_Term {
	$post_id    = $post_id > 0 ? $post_id : (int) get_the_ID();
	$categories = get_the_category( $post_id );

	if ( ! is_array( $categories ) || $categories === array() ) {
		return null;
	}

	return $categories[0];
}

/**
 * Render single post hero (featured image background).
 */
function awesome_the_post_hero(): void {
	$post_id = (int) get_the_ID();

	if ( ! awesome_post_has_hero( $post_id ) ) {
		return;
	}

	$image_url = get_the_post_thumbnail_url( $post_id, 'full' );
	$category  = awesome_get_primary_category( $post_id );

	if ( ! is_string( $image_url ) || $image_url === '' ) {
		return;
	}

	echo '<header class="single-post__hero" style="background-image:url(' . esc_url( $image_url ) . ')">';
	echo '<div class="single-post__hero-overlay" aria-hidden="true"></div>';
	echo '<div class="single-post__hero-inner container">';

	if ( $category instanceof WP_Term ) {
		echo '<p class="single-post__hero-category">';
		echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">';
		echo esc_html( $category->name );
		echo '</a>';
		echo '</p>';
	}

	echo '<h1 class="single-post__hero-title entry-title">' . esc_html( get_the_title() ) . '</h1>';
	awesome_the_post_subtitle( 'single-post__hero-subtitle entry-subtitle' );
	echo '</div>';
	echo '</header>';
}

/**
 * Body classes for layout modes.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function awesome_body_classes( array $classes ): array {
	if ( awesome_has_transparent_header() ) {
		$classes[] = 'has-transparent-header';
	}

	if ( is_singular( 'post' ) && awesome_post_has_hero() ) {
		$classes[] = 'has-post-hero';
	}

	if ( is_singular( 'post' ) && awesome_show_single_sidebar() ) {
		$classes[] = 'has-post-sidebar';
	}

	if ( awesome_show_category_hero() ) {
		$classes[] = 'has-category-hero';
	}

	return $classes;
}
add_filter( 'body_class', 'awesome_body_classes' );
