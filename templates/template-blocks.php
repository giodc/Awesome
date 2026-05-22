<?php
/**
 * Template Name: Blocks Content
 * Template Post Type: page, post
 *
 * Full-width block editor content without extra title chrome.
 *
 * @package Awesome
 */

get_header();
?>

<div class="container container--blocks">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'blocks-content' ); ?>>
			<div class="<?php echo esc_attr( awesome_content_class() ); ?> entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
