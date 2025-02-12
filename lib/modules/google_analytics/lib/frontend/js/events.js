window.addEventListener('load', function() {
	js_sv_tracking_manager_google_analytics_scripts_events.forEach(function(event, index){
		jQuery(document).on(event.event, event.element, function () {
			if (window.ga) {
				gtag('event', event.action, {
					'event_category': event.category,
					'event_label': event.label,
					'value': event.value,
					'non_interaction': event.non_interaction
				});
			}
		});
	});
});