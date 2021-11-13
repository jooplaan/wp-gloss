(function( $ ) {
	'use strict';
	/**
	 *
	 * DOM ready
	 */
	$(function() {
		var mobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
		// Show tooltip only if device is not mobile.
		if( mobile === false) {
			// Show tooltip on hover.
			$('.wp-gloss-tooltip-wrapper').hover(
				function () {
					$(this).find($('.wp-gloss-tooltip')).show();
					$(this).find($('.wp-gloss-tooltip')).attr("aria-hidden", "false");
				},
				function () {
					$(this).find($('.wp-gloss-tooltip')).hide();
					$(this).find($('.wp-gloss-tooltip')).attr("aria-hidden", "true");
				}
			);

			// Show tooltip on focus.
			$('.wp-gloss-tooltip-wrapper').bind('focus', function() {
				$(this).find($('.wp-gloss-tooltip')).show();
				$(this).find($('.wp-gloss-tooltip')).attr("aria-hidden", "false");
			});
			// Hide tooltip on blur/no focus.
			$('.wp-gloss-tooltip-wrapper').bind('blur', function() {
				$(this).find($('.wp-gloss-tooltip')).hide();
				$(this).find($('.wp-gloss-tooltip')).attr("aria-hidden", "true");
			});

			// Hide the tooltip on Escape key press.
			$(document).on(
				'keydown',
				function(event) {
					if(event.key == "Escape") {
						$('.wp-gloss-tooltip').hide();
						$('.wp-gloss-tooltip').attr("aria-hidden", "true");
					}
				}
			);

		};


	});

})( jQuery );





