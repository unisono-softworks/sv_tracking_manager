<?php
namespace sv_tracking_manager;

/**
 * @version         1.000
 * @author			straightvisions GmbH
 * @package			sv_tracking_manager
 * @copyright		2019 straightvisions GmbH
 * @link			https://straightvisions.com
 * @since			1.000
 * @license			See license.txt or https://straightvisions.com
 */

class google_analytics extends modules {
	public function init() {
		// Section Info
		$this->set_section_title( __('Google Analytics', 'sv_tracking_manager' ) )
			->set_section_desc(__( sprintf('%sGoogle Analytics Login%s', '<a target="_blank" href="https://analytics.google.com/">','</a>'), 'sv_tracking_manager' ))
			->set_section_type( 'settings' )
			->set_section_template_path( $this->get_path( 'lib/backend/tpl/settings.php' ) )
			->load_settings()
			->register_scripts()
			->get_root()->add_section( $this );

		$this->add_service();

		add_action('init', array($this, 'load'));
	}

	protected function load_settings(): google_analytics {
		$this->get_setting('activate')
			->set_title( __( 'Activate', 'sv_tracking_manager' ) )
			->set_description('Enable Tracking')
			->load_type( 'checkbox' );

		$this->get_setting('ga4')
			->set_title( __( 'GA4 Compatibility Mode', 'sv_tracking_manager' ) )
			->set_description('This option will become default once Universal-Analytics is shut down. Enable this if you use GA4.')
			->load_type( 'checkbox' );

		$this->get_setting('anonymize_ip')
			->set_title(__('Anonymize IP', 'sv_tracking_manager'))
			->set_description(__(sprintf('%sEnable IP Address anonymization%s', '<a target="_blank" href="https://support.google.com/analytics/answer/2763052?hl=en">','</a>'), 'sv_tracking_manager'))
			->load_type('checkbox');

		$this->get_setting('user_identification')
			->set_title(__('User Identification', 'sv_tracking_manager'))
			->set_description(__(sprintf('%sEnable User Identification%s', '<a target="_blank" href="https://developers.google.com/analytics/devguides/collection/analyticsjs/cookies-user-id">','</a>'), 'sv_tracking_manager'))
			->load_type('checkbox');

		$this->get_setting('tracking_id')
			->set_title( __( 'Tracking ID', 'sv_tracking_manager' ) )
			->set_description( __( sprintf('%sHow to retrieve Tracking ID%s', '<a target="_blank" href="https://support.google.com/analytics/answer/7372977">','</a>'), 'sv_tracking_manager' ) )
			->load_type( 'text' );
		
		
		// Events Groups
		$this->get_setting('custom_events')
			->set_title(__('Custom Events', 'sv_tracking_manager'))
			->load_type('group');
		
		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('entry_label')
			->set_title(__('Entry Label', 'sv_tracking_manager'))
			->set_description(__('This Label will be used as Entry Title for this Settings Group.', 'sv_tracking_manager'))
			->load_type('text')
			->set_placeholder('Entry #...');

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('event')
			->set_title(__('Event Trigger', 'sv_tracking_manager'))
			->set_description(__('Selected trigger will be monitored for event action, see https://www.w3schools.com/jquery/jquery_events.asp', 'sv_tracking_manager'))
			->load_type('text')
			->set_placeholder('click');

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('element')
			->set_title(__('DOM Element', 'sv_tracking_manager'))
			->set_description(__('DOM Selector (e.g. .contact_form, #submit)', 'sv_tracking_manager'))
			->load_type('text')
			->set_placeholder('html')
			->set_default_value('html');

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('scroll_percentage')
			->set_title(__('Scroll Percentage', 'sv_tracking_manager'))
			->set_description(__('Requires Event Trigger set to "scroll". This Event will be triggered once scrolling has reached percentage of the DOM element set above. Use "html" as element if you want to track scroll-status auf the whole page. When no percentage is set, event triggers when element is in view.', 'sv_tracking_manager'))
			->load_type('number')
			->set_min(0)
			->set_max(100);

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('eventCategory')
			->set_title(__('eventCategory', 'sv_tracking_manager'))
			->set_description(__('Typically the object that was interacted with (e.g. "Video")', 'sv_tracking_manager'))
			->load_type('text');

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('eventAction')
			->set_title(__('eventAction', 'sv_tracking_manager'))
			->set_description(__('The type of interaction (e.g. "play")', 'sv_tracking_manager'))
			->load_type('text');

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('eventLabel')
			->set_title(__('eventLabel', 'sv_tracking_manager'))
			->set_description(__('Useful for categorizing events (e.g. "Fall Campaign")', 'sv_tracking_manager'))
			->load_type('text');

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('eventValue')
			->set_title(__('eventValue', 'sv_tracking_manager'))
			->set_description(__('A numeric value associated with the event (e.g. 42)', 'sv_tracking_manager'))
			->load_type('number');

		$this->get_setting('custom_events')->run_type()->add_child()
			->set_ID('active_page')
			->set_title(__('Active Page', 'sv_tracking_manager'))
			->set_description(__('Optional, if you do not want to apply this event globally on site, but on a specific page.', 'sv_tracking_manager'))
			->load_type('select_page');

		$this->get_setting('custom_events')->run_type()->add_child()
			 ->set_ID('non_interaction')
			 ->set_title(__('Non Interaction', 'sv_tracking_manager'))
			 ->set_description(__('Custom Events will reduce bounce rate in Analytics. Activate this to avoid reducing bounce rate by this event.', 'sv_tracking_manager'))
			 ->load_type('checkbox');

		return $this;
	}
	protected function register_scripts(): google_analytics {
		if($this->is_active()) {
			$this->get_script('ga')
				->set_path('https://www.googletagmanager.com/gtag/js', true)
				->set_type('js');

			$this->get_script('default')
				->set_deps(array('jquery', $this->get_script('ga')->get_handle()))
				->set_path('lib/frontend/js/default.js')
				->set_type('js');
			
			$this->get_script('events')
				 ->set_deps(array($this->get_script('default')->get_handle()))
				 ->set_path('lib/frontend/js/events.js')
				 ->set_type('js');
			
			$this->get_script('events_scroll')
				 ->set_deps(array($this->get_script('default')->get_handle()))
				 ->set_path('lib/frontend/js/events_scroll.js')
				 ->set_type('js');
		}

		return $this;
	}
	public function is_active(): bool{
		// activate not set
		if(!$this->get_setting('activate')->get_data()){
			return false;
		}
		// activate not true
		if($this->get_setting('activate')->get_data() !== '1'){
			return false;
		}
		// Tracking ID not set
		if(!$this->get_setting('tracking_id')->get_data()){
			return false;
		}
		// Tracking ID empty
		if(strlen(trim($this->get_setting('tracking_id')->get_data())) === 0){
			return false;
		}

		return true;
	}
	public function load(): google_analytics{
		if($this->is_active()){
			$this->get_script('ga')->set_is_enqueued();

			$this->get_script('default')
				->set_is_enqueued()
				->set_localized(array(
				'tracking_id'	=> $this->get_setting('tracking_id')->get_data(),
				'user_id'		=> $this->get_user_id_hash(),
				'anonymize_ip'	=> $this->get_setting('anonymize_ip')->get_data() ? 'true' : 'false'
			));
			
			$this->events();
		}

		return $this;
	}
	public function is_active_user_identification(): bool{
		// activate not set
		if(!$this->get_setting('user_identification')->get_data()){
			return false;
		}
		// activate not true
		if($this->get_setting('user_identification')->get_data() !== '1'){
			return false;
		}

		return true;
	}
	public function get_user_id_hash(): string{
		if($this->is_active_user_identification()) {
			return md5(get_current_user_id().wp_get_current_user()->user_login);
		}else{
			return '';
		}
	}
	public function cleanup(&$value, $key){
		$value = str_replace('"', "'", $value);
	}
	public function events(): google_analytics{
		$events				= $this->get_setting('custom_events')->get_data();
		$events_js			= array();
		$events_scroll_js	= array();
		
		if ($events && is_array($events) && count($events) > 0) {
			foreach ( $events as $event_id => $event ) {
				array_walk( $event, array( $this, 'cleanup' ) );
				
				// event empty
				if(strlen($event['event']) === 0){
					continue;
				}
				
				// event not for current page
				if (isset($event['active_page']) && intval($event['active_page']) > 0 && intval($event['active_page']) != get_queried_object_id()) {
					continue;
				}
				
				if ( $event['event'] == 'scroll' ) {
					$events_scroll_js[]		= array(
						'scroll_percentage'		=> intval($event['scroll_percentage']),
						'event'           		=> $event['event'],
						'element'				=> $event['element'],
						'category'				=> $event['eventcategory'],
						'action'				=> $event['eventaction'],
						'label'					=> $event['eventlabel'],
						'value'					=> intval( $event['eventvalue'] ),
						'non_interaction'		=> ( isset( $event['non_interaction'] ) && intval( $event['non_interaction'] ) > 0 ) ? true : false,
						'triggered'				=> false
					);
				}else {
					$events_js[] = array(
						'event'           => $event['event'],
						'element'         => $event['element'],
						'category'        => $event['eventcategory'],
						'action'          => $event['eventaction'],
						'label'           => $event['eventlabel'],
						'value'           => intval( $event['eventvalue'] ),
						'non_interaction' => ( isset( $event['non_interaction'] ) && intval( $event['non_interaction'] ) > 0 ) ? true : false,
					);
				}
			}
			
			if(count($events_js) > 0) {
				$this->get_script( 'events' )
					 ->set_is_enqueued()
					 ->set_localized( $events_js );
			}
			
			if(count($events_scroll_js) > 0) {
				$this->get_script( 'events_scroll' )
					 ->set_is_enqueued()
					 ->set_localized( $events_scroll_js );
			}
		}
		
		return $this;
	}
}