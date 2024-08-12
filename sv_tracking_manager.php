<?php
/*
Version: 2.0.03
Plugin Name: SV Tracking Manager
Text Domain: sv_tracking_manager
Description: This lightweight plugin is an advanced tracking manager and allows you to implement various tags from different tracking providers.
Plugin URI: https://straightvisions.com
Author: straightvisions GmbH
Author URI: https://straightvisions.com
Domain Path: /languages
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0-standalone.html
*/

	namespace sv_tracking_manager;

	if(!class_exists('\sv_dependencies\init')){
		require_once( 'lib/core_plugin/dependencies/sv_dependencies.php' );
	}

	if ( $GLOBALS['sv_dependencies']->set_instance_name( 'SV Tracking Manager' )->check_php_version() ) {
		require_once( dirname(__FILE__) . '/init.php' );
	} else {
		$GLOBALS['sv_dependencies']->php_update_notification()->prevent_plugin_activation();
	}