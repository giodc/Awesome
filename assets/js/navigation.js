/**
 * Awesome theme navigation — mobile off-canvas, desktop dropdowns, transparent header scroll.
 */
(function () {
	'use strict';

	var burger = document.querySelector('.burger-menu');
	var mobileMenu = document.getElementById('mobile-menu');
	var overlay = document.querySelector('.mobile-menu-overlay');
	var closeBtn = document.querySelector('.close-menu');

	if (burger && mobileMenu) {
		function setMenuOpen(open) {
			burger.classList.toggle('is-active', open);
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			mobileMenu.classList.toggle('is-active', open);
			mobileMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
			document.body.classList.toggle('menu-open', open);

			if (overlay) {
				overlay.classList.toggle('is-active', open);
				overlay.hidden = !open;
			}
		}

		function closeMenu() {
			setMenuOpen(false);
		}

		burger.addEventListener('click', function () {
			var isOpen = mobileMenu.classList.contains('is-active');
			setMenuOpen(!isOpen);
		});

		if (closeBtn) {
			closeBtn.addEventListener('click', closeMenu);
		}

		if (overlay) {
			overlay.addEventListener('click', closeMenu);
		}

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && mobileMenu.classList.contains('is-active')) {
				closeMenu();
			}
		});

		// Mobile submenu toggles.
		mobileMenu.querySelectorAll('.mobile-submenu-toggle').forEach(function (button) {
			button.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();

				var item = button.closest('li');
				var submenu = item ? item.querySelector(':scope > .mobile-sub-menu') : null;
				if (!submenu) {
					return;
				}

				var expanded = button.getAttribute('aria-expanded') === 'true';
				button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
				submenu.hidden = expanded;
			});
		});
	}

	// Desktop dropdown toggles (click + hover).
	var desktopDropdowns = document.querySelectorAll('.desktop-nav-list > .dropdown-menu');

	desktopDropdowns.forEach(function (item) {
		var toggleBtn = item.querySelector('.dropdown-arrow-btn');
		var panel = item.querySelector(':scope > .sub-menu');

		if (!toggleBtn || !panel) {
			return;
		}

		function openPanel() {
			item.classList.add('is-open');
			panel.classList.add('is-open');
			toggleBtn.setAttribute('aria-expanded', 'true');
		}

		function closePanel() {
			item.classList.remove('is-open');
			panel.classList.remove('is-open');
			toggleBtn.setAttribute('aria-expanded', 'false');
		}

		toggleBtn.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			var isOpen = panel.classList.contains('is-open');
			desktopDropdowns.forEach(function (other) {
				if (other !== item) {
					var otherPanel = other.querySelector(':scope > .sub-menu');
					var otherBtn = other.querySelector('.dropdown-arrow-btn');
					other.classList.remove('is-open');
					if (otherPanel) {
						otherPanel.classList.remove('is-open');
					}
					if (otherBtn) {
						otherBtn.setAttribute('aria-expanded', 'false');
					}
				}
			});
			if (isOpen) {
				closePanel();
			} else {
				openPanel();
			}
		});

		item.addEventListener('mouseenter', openPanel);
		item.addEventListener('mouseleave', closePanel);
	});

	document.addEventListener('click', function (event) {
		if (!event.target.closest('.dropdown-menu')) {
			desktopDropdowns.forEach(function (item) {
				item.classList.remove('is-open');
				var panel = item.querySelector(':scope > .sub-menu');
				var toggleBtn = item.querySelector('.dropdown-arrow-btn');
				if (panel) {
					panel.classList.remove('is-open');
				}
				if (toggleBtn) {
					toggleBtn.setAttribute('aria-expanded', 'false');
				}
			});
		}
	});

	// Header search modal.
	var searchToggle = document.querySelector('.header-search-toggle');
	var searchModal = document.getElementById('search-modal');
	var searchInput = document.getElementById('header-search-field');

	if (searchToggle && searchModal) {
		function openSearch() {
			searchModal.hidden = false;
			searchModal.setAttribute('aria-hidden', 'false');
			searchToggle.setAttribute('aria-expanded', 'true');
			document.body.classList.add('search-modal-open');
			window.setTimeout(function () {
				if (searchInput) {
					searchInput.focus();
					searchInput.select();
				}
			}, 10);
		}

		function closeSearch() {
			searchModal.hidden = true;
			searchModal.setAttribute('aria-hidden', 'true');
			searchToggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('search-modal-open');
			searchToggle.focus();
		}

		searchToggle.addEventListener('click', function () {
			if (searchModal.hidden) {
				openSearch();
			} else {
				closeSearch();
			}
		});

		searchModal.querySelectorAll('[data-search-close]').forEach(function (el) {
			el.addEventListener('click', closeSearch);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !searchModal.hidden) {
				closeSearch();
			}
		});
	}

	// Transparent header: restore solid background + dark text after scroll.
	var header = document.querySelector('.site-header--transparent');
	if (!header || !document.body.classList.contains('has-transparent-header')) {
		return;
	}

	var scrollThreshold = 40;
	var ticking = false;

	function updateHeaderScroll() {
		var scrolled = window.scrollY > scrollThreshold;
		header.classList.toggle('is-scrolled', scrolled);
		ticking = false;
	}

	window.addEventListener(
		'scroll',
		function () {
			if (!ticking) {
				window.requestAnimationFrame(updateHeaderScroll);
				ticking = true;
			}
		},
		{ passive: true }
	);

	updateHeaderScroll();
})();
