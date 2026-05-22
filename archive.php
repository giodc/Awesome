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
		<div class="archive-post-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'archive' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="wp-content">
			<p><?php esc_html_e( 'Nothing found.', 'awesome' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
