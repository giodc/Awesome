<?php
/**
 * Archive, category, tag, and search result post card.
 *
 * @package Awesome
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'archive-post' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="archive-post__featured">
			<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<?php the_post_thumbnail( 'medium_large' ); ?>
			</a>
		</figure>
	<?php endif; ?>
	<header class="archive-post__header">
		<h2 class="archive-post__title entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
		<?php awesome_archive_post_meta(); ?>
	</header>
</article>
