<?php
/**
 * Search results template.
 *
 * @package Awesome
 */

get_header();
?>

<div class="container">
	<header class="archive-header">
		<h1 class="archive-title">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search results for: %s', 'awesome' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<ul class="post-list wp-block-list">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<li>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="wp-content">
			<p><?php esc_html_e( 'No results found.', 'awesome' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
