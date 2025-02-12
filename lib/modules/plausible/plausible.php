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
	
	class plausible extends modules {
		public function init() {
			// Section Info
			$this->set_section_title( __('Plausible', 'sv_tracking_manager' ) )
				 ->set_section_desc(__( sprintf('%sPlausible Login%s', '<a target="_blank" href="https://plausible.io/login">','</a>'), 'sv_tracking_manager' ))
				 ->set_section_type( 'settings' )
				 ->load_settings()
				 ->register_scripts()
				 ->get_root()->add_section( $this );

			$this->add_service();
			
			add_action('init', array($this, 'load'));
		}
		
		protected function load_settings(): plausible {
			$this->get_setting('activate')
				 ->set_title( __( 'Activate', 'sv_tracking_manager' ) )
				 ->set_description('Enable Tracking')
				 ->load_type( 'checkbox' );

			return $this;
		}
		protected function register_scripts(): plausible {
			if($this->is_active()) {
				$this->get_script('default')
						->set_path('https://plausible.io/js/plausible.js')
						->set_type('js')
						->set_custom_attributes(' defer data-domain="'.parse_url(get_site_url())['host'].'"');
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
			
			return true;
		}
		public function load(): plausible{
			if($this->is_active()){
				$this->get_script('default')->set_is_enqueued();
			}
			
			return $this;
		}
	}