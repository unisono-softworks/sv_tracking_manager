//window.addEventListener('load', function() {
	jQuery.fn.sv_is_in_view = function () {
		var win = jQuery(window);
		var viewport = {
			top: win.scrollTop(),
			left: win.scrollLeft()
		};
		viewport.right = viewport.left + win.width();
		viewport.bottom = viewport.top + win.height();

		var bounds = this.offset();
		bounds.right = bounds.left + this.outerWidth();
		bounds.bottom = bounds.top + this.outerHeight();

		return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));

	};

	jQuery.fn.sv_scroll_percentage = function (name) {
		var elementTop = jQuery(this).offset().top;
		var elementHeight = jQuery(this).height();
		var elementBottom = elementHeight;

		var windowHeight = jQuery(window).height();
		var scrollTop = jQuery(window).scrollTop() - elementTop;
		var elementScroll = scrollTop + windowHeight;

		var percentage = elementScroll / elementBottom * 100;

		return Math.round(percentage);
	};
	jQuery( document ).on( "scroll", function() {
		if ( window.ga ) {
			js_sv_tracking_manager_google_analytics_scripts_events_scroll.forEach(function(event, index){
				if (
					event.scroll_percentage === 0
					&& !event.triggered
					&& jQuery(event.element).get(0)
					&& jQuery(event.element).sv_is_in_view()
				) {
					event.triggered = true;

					gtag('event', event.action, {
						'event_category': event.category,
						'event_label': event.label,
						'value': event.value,
						'non_interaction': event.non_interaction
					});
				}else if(
					!event.triggered
					&& jQuery(event.element).get(0)
					&& jQuery(event.element).sv_scroll_percentage(event.element) >=  event.scroll_percentage // check for scroll percentage
				) {
					event.triggered = true;

					gtag('event', event.action, {
						'event_category': event.category,
						'event_label': event.label,
						'value': event.value,
						'non_interaction': event.non_interaction
					});
				}
			});
		}
	});
//});