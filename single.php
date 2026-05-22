<?php
/**
 * Single post template.
 *
 * @package Awesome
 */

get_header();
?>

<div class="container">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
			<header class="entry-header single-post__title-container">
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php awesome_single_post_meta(); ?>
				<?php awesome_the_post_subtitle(); ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="single-post__featured">
					<?php the_post_thumbnail( 'large' ); ?>
				</figure>
			<?php endif; ?>

			<div class="<?php echo esc_attr( awesome_content_class() ); ?>">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
