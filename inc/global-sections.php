<?php
/**
 * Global section CPT and injection zones.
 *
 * @package Awesome
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global section post type slug.
 */
function awesome_global_section_post_type(): string {
	return 'awesome_section';
}

/**
 * Register the Global Section CPT.
 */
function awesome_register_global_section_cpt(): void {
	register_post_type(
		awesome_global_section_post_type(),
		array(
			'labels'              => array(
				'name'               => __( 'Global Sections', 'awesome' ),
				'singular_name'      => __( 'Global Section', 'awesome' ),
				'add_new'            => __( 'Add New', 'awesome' ),
				'add_new_item'       => __( 'Add New Global Section', 'awesome' ),
				'edit_item'          => __( 'Edit Global Section', 'awesome' ),
				'new_item'           => __( 'New Global Section', 'awesome' ),
				'view_item'          => __( 'View Global Section', 'awesome' ),
				'search_items'       => __( 'Search Global Sections', 'awesome' ),
				'not_found'          => __( 'No global sections found.', 'awesome' ),
				'not_found_in_trash' => __( 'No global sections found in Trash.', 'awesome' ),
				'menu_name'          => __( 'Global Sections', 'awesome' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_position'       => 58,
			'menu_icon'           => 'dashicons-layout',
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'revisions' ),
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'has_archive'         => false,
		)
	);
}
add_action( 'init', 'awesome_register_global_section_cpt' );

/**
 * Injection zone keys.
 *
 * @return array<string, string> Zone key => label.
 */
function awesome_global_section_zones(): array {
	return array(
		'after_header'  => __( 'After header', 'awesome' ),
		'before_footer' => __( 'Before footer', 'awesome' ),
	);
}

/**
 * Theme mod key for a zone.
 *
 * @param string $zone Zone key.
 */
function awesome_global_section_mod_key( string $zone ): string {
	return 'awesome_section_' . $zone;
}

/**
 * Choices for Customizer select (None + published sections).
 *
 * @return array<string, string>
 */
function awesome_get_global_section_choices(): array {
	$choices = array(
		'0' => __( '— None —', 'awesome' ),
	);

	$posts = get_posts(
		array(
			'post_type'              => awesome_global_section_post_type(),
			'post_status'            => 'publish',
			'posts_per_page'         => 100,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $posts as $post ) {
		$choices[ (string) $post->ID ] = $post->post_title !== ''
			? $post->post_title
			: sprintf(
				/* translators: %d: post ID */
				__( 'Untitled section #%d', 'awesome' ),
				$post->ID
			);
	}

	return $choices;
}

/**
 * Sanitize a global section post ID for Customizer.
 *
 * @param mixed $value Raw value.
 */
function awesome_sanitize_global_section_id( $value ): int {
	$id = absint( $value );

	if ( $id <= 0 ) {
		return 0;
	}

	$post = get_post( $id );

	if ( ! $post instanceof WP_Post || awesome_global_section_post_type() !== $post->post_type || 'publish' !== $post->post_status ) {
		return 0;
	}

	return $id;
}

/**
 * Meta key for disabling a zone on a singular page/post.
 *
 * @param string $zone Zone key.
 */
function awesome_global_section_disable_meta_key( string $zone ): string {
	return 'awesome_disable_section_' . $zone;
}

/**
 * Whether the current singular view disables a zone.
 *
 * @param string $zone Zone key.
 */
function awesome_is_global_section_disabled( string $zone ): bool {
	if ( ! is_singular( array( 'post', 'page' ) ) ) {
		return false;
	}

	$post_id = (int) get_queried_object_id();

	if ( $post_id <= 0 ) {
		return false;
	}

	return (bool) get_post_meta( $post_id, awesome_global_section_disable_meta_key( $zone ), true );
}

/**
 * Assigned section ID for a zone.
 *
 * @param string $zone Zone key.
 */
function awesome_get_global_section_id( string $zone ): int {
	if ( ! array_key_exists( $zone, awesome_global_section_zones() ) ) {
		return 0;
	}

	return awesome_sanitize_global_section_id( get_theme_mod( awesome_global_section_mod_key( $zone ), 0 ) );
}

/**
 * Whether a global section zone has publishable content assigned.
 *
 * @param string $zone Zone key.
 */
function awesome_has_global_section( string $zone ): bool {
	if ( awesome_is_global_section_disabled( $zone ) ) {
		return false;
	}

	$id = awesome_get_global_section_id( $zone );

	if ( $id <= 0 ) {
		return false;
	}

	$post = get_post( $id );

	if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
		return false;
	}

	$content = $post->post_content;

	return is_string( $content ) && trim( $content ) !== '';
}

/**
 * Render a global section for a zone.
 *
 * @param string $zone Zone key.
 */
function awesome_render_global_section( string $zone ): void {
	if ( ! awesome_has_global_section( $zone ) ) {
		return;
	}

	$id   = awesome_get_global_section_id( $zone );
	$post = get_post( $id );

	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$content = $post->post_content;

	$setup_post = $GLOBALS['post'] ?? null;
	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Temporary for block rendering context.
	$GLOBALS['post'] = $post;
	setup_postdata( $post );

	printf(
		'<div class="global-section global-section--%1$s entry-content">',
		esc_attr( str_replace( '_', '-', $zone ) )
	);
	echo apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered block content.
	echo '</div>';

	if ( $setup_post instanceof WP_Post ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['post'] = $setup_post;
		setup_postdata( $setup_post );
	} else {
		wp_reset_postdata();
	}
}

/**
 * Add per-page/post controls to hide global sections.
 */
function awesome_add_global_section_meta_box(): void {
	foreach ( array( 'page', 'post' ) as $post_type ) {
		add_meta_box(
			'awesome-global-sections',
			__( 'Global Sections', 'awesome' ),
			'awesome_render_global_section_meta_box',
			$post_type,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'awesome_add_global_section_meta_box' );

/**
 * Render global section disable checkboxes.
 *
 * @param WP_Post $post Current post.
 */
function awesome_render_global_section_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'awesome_save_global_sections', 'awesome_global_sections_nonce' );

	echo '<p class="description">' . esc_html__( 'Hide theme-wide sections on this post or page.', 'awesome' ) . '</p>';

	foreach ( awesome_global_section_zones() as $zone => $label ) {
		$checked = (bool) get_post_meta( $post->ID, awesome_global_section_disable_meta_key( $zone ), true );

		echo '<p>';
		echo '<label>';
		printf(
			'<input type="checkbox" name="%1$s" value="1" %2$s> %3$s',
			esc_attr( awesome_global_section_disable_meta_key( $zone ) ),
			checked( $checked, true, false ),
			esc_html(
				sprintf(
					/* translators: %s: zone label */
					__( 'Hide “%s”', 'awesome' ),
					$label
				)
			)
		);
		echo '</label>';
		echo '</p>';
	}
}

/**
 * Save per-page/post global section visibility.
 *
 * @param int $post_id Post ID.
 */
function awesome_save_global_section_meta_box( int $post_id ): void {
	if ( ! isset( $_POST['awesome_global_sections_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['awesome_global_sections_nonce'] ) ), 'awesome_save_global_sections' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array_keys( awesome_global_section_zones() ) as $zone ) {
		$key = awesome_global_section_disable_meta_key( $zone );

		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, '1' );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}
add_action( 'save_post', 'awesome_save_global_section_meta_box' );
