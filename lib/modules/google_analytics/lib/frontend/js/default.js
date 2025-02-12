window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', js_sv_tracking_manager_google_analytics_scripts_default.tracking_id, { 'anonymize_ip': js_sv_tracking_manager_google_analytics_scripts_default.anonymize_ip });

if(js_sv_tracking_manager_google_analytics_scripts_default.user_id !== '') {
	gtag('set', {'user_id': js_sv_tracking_manager_google_analytics_scripts_default.user_id});
}