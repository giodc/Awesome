<?php
/**
 * Main template fallback.
 *
 * @package Awesome
 */

get_header();
?>

<div class="container">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h1>
				</header>
				<div class="<?php echo esc_attr( awesome_content_class() ); ?>">
					<?php the_excerpt(); ?>
				</div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="<?php echo esc_attr( awesome_content_class() ); ?>">
			<p><?php esc_html_e( 'No posts found.', 'awesome' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
