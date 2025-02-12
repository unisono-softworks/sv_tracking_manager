<?php
	namespace sv_tracking_manager;

	class modules extends init{
		public function init(){
			$this->load_module('usercentrics');
			$this->load_module('google_analytics');
			$this->load_module('google_tag_manager');
			$this->load_module('custom');
			$this->load_module('facebook');
			$this->load_module('hotjar');
			$this->load_module('hubspot');
			$this->load_module('linkedin');
			$this->load_module('mailchimp');
			$this->load_module('microsoft_advertising');
			$this->load_module('mouseflow');
			$this->load_module('yahoo');
			$this->load_module('plausible');

			add_filter( 'rocket_excluded_inline_js_content', array($this,'rocket_excluded_inline_js_content') );
			add_filter( 'rocket_exclude_js',array($this,'rocket_exclude_js') );
		}
		// never combine external JS
		public function rocket_excluded_inline_js_content(array $pattern): array{
			$pattern[] = 'sv_tracking_manager';

			return $pattern;
		}
		public function rocket_exclude_js($pattern){
			$pattern[] = '(.*)sv-tracking-manager/(.*)';

			return $pattern;
		}
		public function add_service(): modules{
			if($this->is_active()){
				// filter name: sv_tracking_manager_active_services
				add_filter($this->get_root()->get_prefix('active_services'), function(array $services){
					return array_merge($services,array($this->get_module_name() => $this->get_section_title()));
				});

				add_action('wp_head', array($this, 'consent_management'), 1);
				add_action('wp_footer', array($this, 'consent_management'), 1);
			}

			return $this;
		}
		public function consent_management(): modules{
			// filter name: sv_tracking_manager_consent_management
			$activated = apply_filters($this->get_root()->get_prefix('consent_management'), false);

			// @todo: currently no effect, since uc scripts are directly loaded
			// filter name: sv_tracking_manager_no_consent_required
			$no_consent_required	= apply_filters($this->get_root()->get_prefix('no_consent_required'), array(
				'usercentrics',
				'usercentrics_block',
				'usercentrics_block_ui'
			));

			if($activated){
				foreach($this->get_scripts() as $script){
					if(!in_array($script->get_handle(),$no_consent_required)) {
						$script
							->set_consent_required()
							// filter name: sv_tracking_manager_data_attributes
							->set_custom_attributes(apply_filters($this->get_root()->get_prefix('data_attributes'), $script->get_custom_attributes(), $script));
					}
				}
			}

			return $this;
		}
	}