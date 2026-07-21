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

<header class="site-header site-header--width-<?php echo esc_attr( awesome_get_header_width() ); ?><?php echo awesome_has_transparent_header() ? ' site-header--transparent' : ''; ?>" role="banner">
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
		<?php endif; ?>

		<?php if ( has_nav_menu( 'header' ) || awesome_header_search_enabled() ) : ?>
			<div class="header-actions">
				<?php if ( awesome_header_search_enabled() ) : ?>
					<button
						type="button"
						class="header-search-toggle"
						aria-expanded="false"
						aria-controls="search-modal"
						aria-label="<?php esc_attr_e( 'Open search', 'awesome' ); ?>"
					>
						<svg class="header-search-toggle__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
							<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"></circle>
							<path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
						</svg>
					</button>
				<?php endif; ?>

				<?php if ( has_nav_menu( 'header' ) ) : ?>
					<button type="button" class="burger-menu" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Toggle menu', 'awesome' ); ?>">
						<span></span>
						<span></span>
						<span></span>
					</button>
				<?php endif; ?>
			</div>
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

<?php if ( awesome_header_search_enabled() ) : ?>
	<div id="search-modal" class="search-modal" aria-hidden="true" hidden>
		<div class="search-modal__backdrop" data-search-close></div>
		<div
			class="search-modal__dialog"
			role="dialog"
			aria-modal="true"
			aria-labelledby="search-modal-title"
		>
			<button type="button" class="search-modal__close" data-search-close aria-label="<?php esc_attr_e( 'Close search', 'awesome' ); ?>">
				<span aria-hidden="true">✕</span>
			</button>
			<h2 id="search-modal-title" class="search-modal__title"><?php esc_html_e( 'Search', 'awesome' ); ?></h2>
			<form class="search-modal__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="header-search-field"><?php esc_html_e( 'Search for:', 'awesome' ); ?></label>
				<input
					type="search"
					id="header-search-field"
					class="search-modal__input"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( 'Type to search…', 'awesome' ); ?>"
					autocomplete="off"
				>
				<button type="submit" class="search-modal__submit"><?php esc_html_e( 'Search', 'awesome' ); ?></button>
			</form>
		</div>
	</div>
<?php endif; ?>

<?php awesome_render_global_section( 'after_header' ); ?>

<main id="main" class="site-main">
