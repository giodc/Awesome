<?php
/**
 * Header template.
 *
 * @package Awesome
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
	<div class="navigation">
		<?php awesome_site_branding(); ?>

		<?php if ( has_nav_menu( 'header' ) ) : ?>
			<nav class="desktop-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'awesome' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'header',
					'container'      => false,
					'menu_class'     => 'desktop-nav-list',
					'fallback_cb'    => false,
					'depth'          => 3,
					'walker'         => new Awesome_Desktop_Nav_Walker(),
				) );
				?>
			</nav>

			<button type="button" class="burger-menu" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Toggle menu', 'awesome' ); ?>">
				<span></span>
				<span></span>
				<span></span>
			</button>
		<?php endif; ?>
	</div>
</header>

<?php if ( has_nav_menu( 'header' ) ) : ?>
	<div id="mobile-menu" class="mobile-menu" aria-hidden="true">
		<button type="button" class="close-menu" aria-label="<?php esc_attr_e( 'Close menu', 'awesome' ); ?>">
			<span class="close-icon" aria-hidden="true">✕</span>
		</button>
		<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Mobile menu', 'awesome' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'header',
				'container'      => false,
				'menu_class'     => 'mobile-nav-list',
				'fallback_cb'    => false,
				'depth'          => 3,
				'walker'         => new Awesome_Mobile_Nav_Walker(),
			) );
			?>
		</nav>
	</div>
	<div class="mobile-menu-overlay" hidden></div>
<?php endif; ?>

<main id="main" class="site-main">
