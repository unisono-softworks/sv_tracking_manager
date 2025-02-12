<?php
namespace sv_tracking_manager;

class ec extends modules{
	public function __construct(){

	}
	public function init(){
		$this->ec_woocommerce->init();

		add_action('wp_head', array($this, 'wp_head'), 990);
		add_action('wp_head', array($this, 'product_view'), 991);
		add_action('wp_footer', array($this, 'product_impression'), 1000);
	}
	public function wp_head(){
		echo '
			<script data-id="'.$this->get_name().'">
			/* '.$this->get_name().' */
			if (window.ga) {
				ga("require", "ec");
			}
			</script>
		';
	}
	public function product_view(){
		$product						= false;
		$product						= apply_filters('sv_tracking_manager_ec_set_product_view', $product);

		if($product) {
			echo '
			<script data-id="' . $this->get_name() . '">
			/* '.$this->get_name().' */
			if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
				ga("ec:addProduct", {                 // Provide product details in an productFieldObject.
				  "id": "' . $product['id'] . '",                     // Product ID (string).
				  "name": "' . $product['name'] . '",  // Product name (string).
				  "category": "' . $product['category'] . '",     // Product category (string).
				  "brand": "' . $product['brand'] . '",                 // Product brand (string).
				  //"variant": "gray",                  // Product variant (string).
				  //"position": 2                       // Product position (number).
				});
				
				ga("ec:setAction", "detail");       // Detail action.
			}
			</script>
		';
		}
	}
	public function product_impression(){
		// we cannot send whole product data for all products in list due to payload size error
		// see: https://stackoverflow.com/questions/38671176/google-analytics-how-to-overcome-payload-size-restriction
		// todo: check and implement product data import feature: https://support.google.com/analytics/answer/6014867?hl=en
		// todo: check if there is an automatic way, as there will be a gap when new products are created in WooCommerce and new product data is not imported to analytics instantly.
		return;

		$product_lists						= apply_filters('sv_tracking_manager_ec_add_product_impression', $product_lists);

		if($product_lists && count($product_lists) > 0) {
			foreach($product_lists as $products) {
				ksort($products);
				foreach($products as $product) {
					echo '
						<script data-id="' . $this->get_name() . '">
						/* '.$this->get_name().' */
						if (window.ga) {
							ga("ec:addImpression", {            // Provide product details in an impressionFieldObject.
								"id": "' . $product['id'] . '",                   // Product ID (string).
								"name": "' . $product['name'] . '", // Product name (string).
								"category": "' . $product['category'] . '",   // Product category (string).
								"brand": "' . $product['brand'] . '",                // Product brand (string).
								"variant": "' . $product['variant'] . '",               // Product variant (string).
								"list": "' . $product['list'] . '",       // Product list (string).
								"position": ' . $product['position'] . ',                     // Product position (number).
								"price": ' . $product['price'] . '
							});
						}
						</script>
					';
				}
			}
		}
	}
	public function add_product(array $param){
		echo '
			<script data-id="'.$this->get_name().'">
			/* '.$this->get_name().' */
			if (sv_tracking_manager_shapepress_dsgvo_userPermissions("google-analytics") && window.ga) {
				ga("ec:addProduct", {
					"id": "' . $param['id'] . '",
					"name": "' . $param['name'] . '",
					"category": "' . $param['category'] . '",
					"brand": "' . $param['brand'] . '",
					"variant": "' . $param['variant'] . '",
					"price": ' . $param['price'] . ',
					"quantity": ' . $param['quantity'] . '
				});
			}
			</script>
			';
	}
}