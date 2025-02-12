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
	
	class yahoo extends modules {
		public function init() {
			// Section Info
			$this->set_section_title( __('Yahoo', 'sv_tracking_manager' ) )
				 ->set_section_desc(__( sprintf('%sYahoo Login%s', '<a target="_blank" href="https://gemini.yahoo.com/">','</a>'), 'sv_tracking_manager' ))
				 ->set_section_type( 'settings' )
				 ->load_settings()
				 ->register_scripts()
				 ->get_root()->add_section( $this );

			$this->add_service();
			
			add_action('init', array($this, 'load'));
		}
		
		protected function load_settings(): yahoo {
			$this->get_setting('activate')
				 ->set_title( __( 'Activate', 'sv_tracking_manager' ) )
				 ->set_description('Enable Tracking')
				 ->load_type( 'checkbox' );
			
			$this->get_setting('tracking_id')
				 ->set_title( __( 'Tracking ID', 'sv_tracking_manager' ) )
				 ->set_description( __( sprintf('%sHow to retrieve Tracking ID%s', '<a target="_blank" href="https://developer.yahoo.com/nativeandsearch/guide/audience-management/dottags/">','</a>'), 'sv_tracking_manager' ) )
				 ->load_type( 'text' );
			
			$this->get_setting('project_id')
				 ->set_title( __( 'Project ID', 'sv_tracking_manager' ) )
				 ->set_description( __( sprintf('%sHow to retrieve Tracking ID%s', '<a target="_blank" href="https://developer.yahoo.com/nativeandsearch/guide/audience-management/dottags/">','</a>'), 'sv_tracking_manager' ) )
				 ->load_type( 'text' );
			
			return $this;
		}
		protected function register_scripts(): yahoo {
			if($this->is_active()) {
				$this->get_script('default')
					 ->set_path('lib/frontend/js/default.js')
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
			// Project ID not set
			if(!$this->get_setting('project_id')->get_data()){
				return false;
			}
			// Project ID empty
			if(strlen(trim($this->get_setting('project_id')->get_data())) === 0){
				return false;
			}
			
			return true;
		}
		public function load(): yahoo{
			if($this->is_active()){
				$this->get_script('default')
					 ->set_is_enqueued()
					 ->set_localized(array(
						 'tracking_id'	=> $this->get_setting('tracking_id')->get_data(),
						 'project_id'	=> $this->get_setting('project_id')->get_data()
					 ));
			}
			
			return $this;
		}
	}