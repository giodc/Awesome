<?php
/**
 * Single post template.
 *
 * @package Awesome
 */

get_header();

$has_hero    = awesome_post_has_hero();
$has_sidebar = awesome_show_single_sidebar();
?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>

	<?php if ( $has_hero ) : ?>
		<?php awesome_the_post_hero(); ?>
	<?php endif; ?>

	<div class="container<?php echo $has_sidebar ? ' container--with-sidebar' : ''; ?>">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
			<?php if ( ! $has_hero ) : ?>
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
			<?php endif; ?>

			<div class="single-post__layout<?php echo $has_sidebar ? ' has-sidebar' : ''; ?>">
				<div class="single-post__main">
					<div class="<?php echo esc_attr( awesome_content_class() ); ?>">
						<?php the_content(); ?>
					</div>

					<?php if ( $has_hero ) : ?>
						<footer class="single-post__meta-footer">
							<?php awesome_single_post_meta(); ?>
						</footer>
					<?php endif; ?>

					<?php awesome_the_post_share(); ?>
				</div>

				<?php if ( $has_sidebar ) : ?>
					<aside class="single-post__sidebar" role="complementary">
						<div class="single-post__sidebar-inner">
							<?php dynamic_sidebar( 'sidebar-single' ); ?>
						</div>
					</aside>
				<?php endif; ?>
			</div>
		</article>
	</div>
<?php endwhile; ?>

<?php
get_footer();
