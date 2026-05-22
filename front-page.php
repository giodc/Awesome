<?php
/**
 * Front page template.
 *
 * @package Awesome
 */

get_header();
?>

<div class="container">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'homepage-content' ); ?>>
			<div class="<?php echo esc_attr( awesome_content_class() ); ?>">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
