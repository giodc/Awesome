<?php
/**
 * 404 template.
 *
 * @package Awesome
 */

get_header();
?>

<div class="container">
	<div class="wp-content">
		<h1><?php esc_html_e( 'Page not found', 'awesome' ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist.', 'awesome' ); ?></p>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'awesome' ); ?></a></p>
	</div>
</div>

<?php
get_footer();
