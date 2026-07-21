<?php
/**
 * Front page template.
 *
 * @package Awesome
 */

get_header();

$transparent = awesome_has_transparent_header();
?>

<div class="<?php echo $transparent ? 'homepage-fullbleed' : 'container'; ?>">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'homepage-content' ); ?>>
			<div class="<?php echo esc_attr( awesome_content_class() ); ?> entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
