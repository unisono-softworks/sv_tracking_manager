<div class="sv_setting_subpage">
	<h2><?php _e('Events', 'sv_tracking_manager'); ?></h2>
	<p><a target="_blank" href="https://developers.google.com/analytics/devguides/collection/analyticsjs/events">Documentation</a></p>
	<div class="sv_setting_flex">
		<?php
			echo $module->get_setting('custom_events')->form();
		?>
	</div>
</div>