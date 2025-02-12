<?php
	namespace sv_tracking_manager;
	
	class ec_woocommerce extends ec{
		private static $product								= false;
		private static $order_id							= false;
		
		public function __construct(){
			$this->set_section_title('WooCommerce');
			$this->set_section_desc('');
			$this->set_section_type('settings');
		}
		public function wp_head(){
		
		}
		public function init(){
			$this->load_settings();
			add_action('init', array($this, 'wp_init'));
		}
		private function load_settings(){
			$this->get_root()->add_section($this);
			
			$this->s['checkout_label_cart']					= static::$settings->create($this)
																				  ->set_ID('checkout_label_cart')
																				  ->set_title(__('Checkout Label: Cart Page', $this->get_module_name()))
																				  ->set_description(__('Must be the same name as in Analytics -> Settings -> E-Commerce-Settings -> Checkout Labeling.', $this->get_module_name()))
																				  ->load_type('text')
																				  ->set_placeholder('e.g. View Cart');
			$this->s['checkout_label_review']				= static::$settings->create($this)
																				  ->set_ID('checkout_label_review')
																				  ->set_title(__('Checkout Label: Review Page', $this->get_module_name()))
																				  ->set_description(__('Must be the same name as in Analytics -> Settings -> E-Commerce-Settings -> Checkout Labeling.', $this->get_module_name()))
																				  ->load_type('text')
																				  ->set_placeholder('e.g. View Review');
			$this->s['checkout_label_thankyou']				= static::$settings->create($this)
																				  ->set_ID('checkout_label_thankyou')
																				  ->set_title(__('Checkout Label: Thankyou Page', $this->get_module_name()))
																				  ->set_description(__('Must be the same name as in Analytics -> Settings -> E-Commerce-Settings -> Checkout Labeling.', $this->get_module_name()))
																				  ->load_type('text')
																				  ->set_placeholder('e.g. View Thankyou');
			
			$this->s['checkout_step_cart']					= static::$settings->create($this)
																				  ->set_ID('checkout_step_cart')
																				  ->set_title(__('Checkout Step: Cart Page', $this->get_module_name()))
																				  ->set_description(__('Must be the same order as in Analytics -> Settings -> E-Commerce-Settings -> E-Commerce-Setup -> Funnel Steps', $this->get_module_name()))
																				  ->load_type('number')
																				  ->set_placeholder('e.g. 2');
			$this->s['checkout_step_review']					= static::$settings->create($this)
																				 ->set_ID('checkout_step_review')
																				 ->set_title(__('Checkout Step: Review Page', $this->get_module_name()))
																				 ->set_description(__('Must be the same order as in Analytics -> Settings -> E-Commerce-Settings -> E-Commerce-Setup -> Funnel Steps', $this->get_module_name()))
																				 ->load_type('number')
																				 ->set_placeholder('e.g. 3');
			$this->s['checkout_step_thankyou']					= static::$settings->create($this)
																				 ->set_ID('checkout_step_thankyou')
																				 ->set_title(__('Checkout Step: Thankyou Page', $this->get_module_name()))
																				 ->set_description(__('Should not be added to E-Commerce-Funnel in Analytics, as it it available by default. Should be one number higher than review-step.', $this->get_module_name()))
																				 ->load_type('number')
																				 ->set_placeholder('e.g. 4');
		}
		public function wp_init(){
			if(function_exists('WC')) {
				add_action('wp_head', array($this, 'wp_footer'), 991);
				add_action('wp_head', array($this, 'checkout_cart'), 999);
				add_action('wp_head', array($this, 'checkout_review'), 999);
				add_action('wp_head', array($this, 'checkout_thankyou'), 999);
				add_action('wp_head', array($this, 'wp_head'), 1000);
			}
		}
		public function wp_footer(){
			echo '
			<script data-id="'.$this->get_name().'">
			if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
				ga("set", "currencyCode", "' . get_woocommerce_currency() . '");
			}
			</script>
		';
		}
		public function set_product($product){
			if(!static::$product){
				if($product instanceof \WC_Product){
					static::$product							= $product;
				}else{
					$notice = static::$notices->create();
					$notice->set_title(__('Invalid Produkt for GA Manager', $this->get_name()));
					$notice->set_desc_admin(__('Product should be instance of WC_Product - ', $this->get_name()) . var_export($product,true));
					$notice->set_state(3);
				}
			}else{
				$notice = static::$notices->create();
				$notice->set_title(__('Product for GA Manager already set', $this->get_name()));
				$notice->set_desc_admin(__FUNCTION__.__(' in GA Manager was called but product was already set', $this->get_name()));
				$notice->set_state(3);
			}
		}
		public function get_product_cat($product_id){
			$terms = get_the_terms( $product_id, 'product_cat' );
			foreach ($terms as $term) {
				$product_cat = $term->name;
			}
			return $product_cat;
		}
		public function add_to_cart(){
			// @todo: Support for WooCommerce standard add to cart action
		}
		public function map_wc_item_to_ec_product($item){
			return apply_filters('sv_tracking_manager_ec_woocommerce_map_wc_item_to_ec_product', array(
				'id'								=> $item['product_id'],
				'name'								=> get_the_title($item['product_id']),
				'category'							=> $this->get_product_cat($item['product_id']),
				'brand'								=> '', // no default support for brands in WooCommerce
				'variant'							=> isset($item['data']) ? strip_tags($item['data']->get_formatted_name()) : strip_tags($item->get_name()),
				'price'								=> isset($item['data']) ? $item['data']->get_data()['price'] : ($item->get_data()['subtotal'] / $item['quantity']),
				'quantity'							=> $item['quantity']
			), $item);
		}
		public function checkout_cart(){
			if(is_cart() && WC()->cart->get_cart_contents_count() > 0) {
				foreach(WC()->cart->cart_contents as $item){
					$this->add_product($this->map_wc_item_to_ec_product($item));
				}
				echo '
			<script data-id="'.$this->get_name().'">
			if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
				ga("ec:setAction","checkout", {
					"step": '.intval($this->s['checkout_step_cart']->get_data()).',
					"option": "'.((strlen($this->s['checkout_label_cart']->get_data()) > 0) ? $this->s['checkout_label_cart']->get_data() : 'View Cart').'"
				});
				ga("send", "event", "Checkout", "View Cart");
			}
			</script>
			';
			}
		}
		public function checkout_review(){
			if(is_checkout() && WC()->cart->get_cart_contents_count() > 0) {
				foreach(WC()->cart->cart_contents as $item){
					$this->add_product($this->map_wc_item_to_ec_product($item));
				}
				
				echo '
			<script data-id="'.$this->get_name().'">
			if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
				ga("ec:setAction","checkout", {
					"step": '.intval($this->s['checkout_step_review']->get_data()).',
					"option": "'.((strlen($this->s['checkout_label_review']->get_data()) > 0) ? $this->s['checkout_label_review']->get_data() : 'View Review').'"
				});
				ga("send", "event", "Checkout", "View Review");
			}
			</script>
			';
			}
		}
		public function checkout_thankyou(){
			// @note: ec actions should never run after an event has been sent. Make sure to send all ec:actions before first ga(send, event, ...) command.
			
			if(is_wc_endpoint_url('order-received')) {
				global $wp;
				$order									= new \WC_Order($wp->query_vars['order-received']);
				if(!$order->meta_exists( $this->get_name().'_checkout_tracked') && $order->get_items()) {
					$order->update_meta_data($this->get_name() . '_checkout_tracked', '1');
					$order->save();
					
					/*
					 * EC ACTIONS
					 */
					
					foreach ($order->get_items() as $item) {
						$this->add_product($this->map_wc_item_to_ec_product($item));
					}
					
					echo '
					<script data-id="' . $this->get_name() . '">
					if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
						ga("ec:setAction","checkout", {
							"step": '.intval($this->s['checkout_step_thankyou']->get_data()).',
							"option": "'.((strlen($this->s['checkout_label_thankyou']->get_data()) > 0) ? $this->s['checkout_label_thankyou']->get_data() : '"View Thankyou').'"
						});
						ga("ec:setAction", "purchase", {
							"id": "' . $order->get_id() . '",
							"affiliation": "' . get_bloginfo('name') . '",
							"revenue": ' . $order->get_total() . ',
							"tax": ' . $order->get_total_tax() . ',
							"shipping": ' . $order->get_shipping_total() . ',
							"coupon": "' . implode(',', $order->get_used_coupons()) . '"
						});
					}
					</script>
					';
					
					/*
					 * EVENTS
					 */
					
					echo '
					<script data-id="' . $this->get_name() . '">
					if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
						ga("send", "event", "Checkout", "View Thankyou", "", ' . intval($order->get_total()) . ');
					}
					</script>
					';
					
					foreach ($order->get_items() as $item) {
						echo '
						<script data-id="' . $this->get_name() . '">
						if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
							ga("send", "event", "Checkout", "Purchased Product", "'.$item['product_id'].'", ' . intval( $item['line_total'] ) . ');
						}
						</script>
						';
					}
				}
			}
		}
	}
	?>