(function( $ ) {
	'use strict';

	// Settings:
	// Timeout to hide tooltip
	var TIMEOUT_LENGTH = 200;
	// global timeout map; quick n dirty
	var timeouts = new WeakMap();

	// here we attach all event listeners to control the tooltip
	function initTooltip(tooltipContainer) {
		var trigger = $('.wp-gloss-tooltip-trigger');
		var tooltip = $('.wp-gloss-tooltip');

		// Show tooltip on hover and focus.
		tooltipContainer.bind('mouseenter', function () {
			showTooltip(tooltip);
		});
		trigger.bind('focus', function () {
			showTooltip(tooltip);
		});

		// Hide tooltip on mouse out and blur
		// use timeout on mouse leave.
		tooltipContainer.bind('mouseleave', function () {
			timeoutTooltip(tooltip);
		});
		trigger.bind('blur', function () {
			hideTooltip(tooltip);
		});

		// Hide the tooltip on escape key press.
		$(document).on(
			'keydown',
			function(event) {
				if(event.key == "Escape") {
					hideTooltip(tooltip);
				}
			}
		);
	}

	function showTooltip(tooltip) {
		$(tooltip).css('display','block');
		$(tooltip).attr("aria-hidden", "false");
		// If a hide timeout exists for this tooltip, clear it.
		if (timeouts.has(tooltip)) {
			window.clearTimeout(timeouts.get(tooltip));
		}
	}

	function hideTooltip(tooltip) {
		$(tooltip).css('display','none');
		$(tooltip).attr("aria-hidden", "true");
	}

	function timeoutTooltip(tooltip) {
		// Hide the tooltip after a set amount of time.
		var timeoutId = window.setTimeout(function () {
			hideTooltip(tooltip);
		}, TIMEOUT_LENGTH);
		// Store the timeout so it can be cleared.
		timeouts.set(tooltip, timeoutId);
	}

	/**
	 *
	 * DOM ready
	 */
	$(function() {
		// initiate tooltips
		$('.wp-gloss-tooltip-wrapper').each (function (tooltip) {
			initTooltip($(this));
		});

	});

})( jQuery );





