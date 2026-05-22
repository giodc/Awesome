<?php
/**
 * Archive template.
 *
 * @package Awesome
 */

get_header();
?>

<div class="container">
	<header class="archive-header">
		<h1 class="archive-title"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="archive-description wp-content">', '</div>' ); ?>
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
			<p><?php esc_html_e( 'Nothing found.', 'awesome' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
