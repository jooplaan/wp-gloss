(function( $ ) {
	'use strict';
	/**
	 *
	 * DOM ready
	 */
	$(function() {

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
	});

})( jQuery );





