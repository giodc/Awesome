/**
 * Live preview for Awesome theme color settings.
 */
(function ($) {
	'use strict';

	var map = {
		awesome_color_primary: '--color-primary',
		awesome_color_secondary: '--color-secondary',
		awesome_color_accents: '--color-accents',
		awesome_color_background: '--color-background',
		awesome_color_text: '--color-text',
		awesome_color_content_link: '--color-content-link',
		awesome_color_heading: '--color-heading',
		awesome_color_heading_hover: '--color-heading-hover',
		awesome_color_text_hover: '--color-text-hover',
		awesome_color_footer_bg: '--footer-background',
		awesome_color_footer_text: '--footer-text',
		awesome_color_footer_text_hover: '--footer-text-hover'
	};

	$.each(map, function (settingId, cssVar) {
		wp.customize(settingId, function (setting) {
			setting.bind(function (value) {
				document.documentElement.style.setProperty(cssVar, value);
			});
		});
	});
})(jQuery);
