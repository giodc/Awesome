<?php
/**
 * Footer template.
 *
 * @package Awesome
 */

?>
</main>

<?php awesome_render_global_section( 'before_footer' ); ?>

<footer class="site-footer<?php echo awesome_has_global_section( 'before_footer' ) ? ' site-footer--after-global-section' : ''; ?>" role="contentinfo">
	<?php if ( awesome_has_footer_widgets() ) : ?>
		<div class="footer-container">
			<?php awesome_render_footer_widgets(); ?>
		</div>
	<?php endif; ?>

	<div class="footer-bottom">
		<p><?php echo esc_html( awesome_footer_copyright() ); ?></p>

		<?php if ( has_nav_menu( 'copyrights' ) ) : ?>
			<nav class="copyrights-nav" aria-label="<?php esc_attr_e( 'Copyright links', 'awesome' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'copyrights',
						'container'      => false,
						'menu_class'     => 'copyrights-menu',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
