<div class="sv_setting_subpage">
	<h2><?php _e('General', 'sv_tracking_manager'); ?></h2>
	<h3 class="divider"><?php _e( 'Display styles', 'sv_tracking_manager' ); ?></h3>
	<div class="sv_setting_flex">
	<?php
		echo $module->get_setting('activate')->form();
		echo $module->get_setting('ga4')->form();
		echo $module->get_setting('user_identification')->form();
		echo $module->get_setting('anonymize_ip')->form();
	?>
	</div>
	<div class="sv_setting_flex">
		<?php
			echo $module->get_setting('tracking_id')->form();
		?>
	</div>
</div>