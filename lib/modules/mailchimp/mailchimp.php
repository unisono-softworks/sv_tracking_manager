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
	
	class mailchimp extends modules {
		public function init() {
			// Section Info
			$this->set_section_title( __('Mailchimp', 'sv_tracking_manager' ) )
				 ->set_section_desc(__( sprintf('%sMailchimp Login%s', '<a target="_blank" href="https://login.mailchimp.com/">','</a>'), 'sv_tracking_manager' ))
				 ->set_section_type( 'settings' )
				 ->load_settings()
				 ->register_scripts()
				 ->get_root()->add_section( $this );

			$this->add_service();
			
			add_action('init', array($this, 'load'));
		}
		
		protected function load_settings(): mailchimp {
			$this->get_setting('activate')
				 ->set_title( __( 'Activate', 'sv_tracking_manager' ) )
				 ->set_description('Enable Mailchimp Script')
				 ->load_type( 'checkbox' );
			
			$this->get_setting('script_url')
				 ->set_title( __( 'Mailchimp Script URL', 'sv_tracking_manager' ) )
				 ->set_description( __( sprintf('%sGet Script URL from Form Script%s', '<a target="_blank" href="https://mailchimp.com/de/help/add-a-pop-up-signup-form-to-your-website/">','</a>'), 'sv_tracking_manager' ) )
				->set_placeholder('https://chimpstatic.com/mcjs-connected/js/users/d41d8cd98f00b204e9800998ecf8427e/acd1bc21d20c359f8fd6a65107399106.js')
				->load_type( 'text' );

			return $this;
		}
		protected function register_scripts(): mailchimp {
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
			if(!$this->get_setting('script_url')->get_data()){
				return false;
			}
			// Tracking ID empty
			if(strlen(trim($this->get_setting('script_url')->get_data())) === 0){
				return false;
			}
			
			return true;
		}
		public function load(): mailchimp{
			if($this->is_active()){
				$this->get_script('default')
					 ->set_is_enqueued()
					 ->set_localized(array(
						 'script_url'	=> $this->get_setting('script_url')->get_data()
					 ));
			}
			
			return $this;
		}
	}