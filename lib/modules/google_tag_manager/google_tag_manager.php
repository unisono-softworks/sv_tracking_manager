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

	class google_tag_manager extends modules {
		public function init() {
			// Section Info
			$this->set_section_title( __('Google Tag Manager', 'sv_tracking_manager' ) )
				->set_section_desc(__( sprintf('%sTag Manager Login%s', '<a target="_blank" href="https://tagmanager.google.com/">','</a>'), 'sv_tracking_manager' ))
				->set_section_type( 'settings' )
				->load_settings()
				->register_scripts()
				->get_root()->add_section( $this );

			$this->add_service();

			add_action('init', array($this, 'load'));
		}

		protected function load_settings(): google_tag_manager {
			$this->get_setting('activate')
				->set_title( __( 'Activate', 'sv_tracking_manager' ) )
				->set_description('Enable Tracking')
				->load_type( 'checkbox' );

			$this->get_setting('activate_consent_mode')
				->set_title( __( 'Activate Consent Mode', 'sv_tracking_manager' ) )
				->set_description( __( sprintf('%sConsent Mode compatible with Usercentrics%s', '<a target="_blank" href="https://docs.usercentrics.com/#/consent-mode">','</a>'), 'sv_tracking_manager' ) )
				->load_type( 'checkbox' );

			$this->get_setting('tracking_id')
				->set_title( __( 'Tracking ID', 'sv_tracking_manager' ) )
				->set_description( __( sprintf('%sHow to retrieve Tracking ID%s', '<a target="_blank" href="https://www.analyticsmania.com/post/google-tag-manager-id/">','</a>'), 'sv_tracking_manager' ) )
				->load_type( 'text' );

			$this->get_setting('url')
				->set_title( __( 'Server URL', 'sv_tracking_manager' ) )
				->set_description('Set a custom URL for loading the tracking script')
				->set_default_value('https://www.googletagmanager.com/gtm.js')
				->load_type( 'text' );

			return $this;
		}
		protected function register_scripts(): google_tag_manager {
			$this->get_script('default')
				->set_path('lib/frontend/js/default.js')
				->set_type('js');

			$this->get_script('consent_mode')
				->set_path('lib/frontend/js/consent_mode.js')
				->set_type('js');

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
		public function is_consent_mode_active(): bool{
			// activate not set
			if(!$this->get_setting('activate_consent_mode')->get_data()){
				return false;
			}
			// activate not true
			if($this->get_setting('activate_consent_mode')->get_data() !== '1'){
				return false;
			}

			return true;
		}
		public function load(): google_tag_manager{
			if($this->is_active()){
				$this->get_script('default')
					->set_is_enqueued()
					->set_localized(array(
						'tracking_id'	=> $this->get_setting('tracking_id')->get_data(),
						'url'			=> $this->get_setting('url')->get_data()
					));

				if($this->is_consent_mode_active()){
					$this->get_script('consent_mode')->set_is_enqueued();

					$this->get_script('default')->set_deps(array($this->get_script('consent_mode')->get_handle()));
				}
			}

			return $this;
		}
	}