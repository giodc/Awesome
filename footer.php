<?php
/**
 * Footer template.
 *
 * @package Awesome
 */

?>
</main>

<footer class="site-footer" role="contentinfo">
	<?php if ( awesome_has_footer_widgets() ) : ?>
		<div class="footer-container">
			<?php awesome_render_footer_widgets(); ?>
		</div>
	<?php endif; ?>

	<div class="footer-bottom">
		<p><?php echo esc_html( awesome_footer_copyright() ); ?></p>
		<?php echo do_shortcode( '[cc_show_cookie_banner_nsc_bar]' ); ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
