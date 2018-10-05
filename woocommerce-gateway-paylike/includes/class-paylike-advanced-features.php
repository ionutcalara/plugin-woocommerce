<?php

/**
 * Optional features, not related to the core of the payment process
 */
class Paylike_Advanced_Features {


	public $settings;

	public function __construct() {
		$this->settings = get_option( 'woocommerce_paylike_settings' );
		$this->actions();

	}

	/**
	 *
	 */
	public function actions() {
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_surcharge' ) );
	}

	public function add_surcharge() {
		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
		if ( $chosen_payment_method === 'paylike' ) {
			$woocommerce->cart->add_fee( 'Surcharge', 1.00, true, 'standard' );
		}
	}
}

$paylike_advanced = new Paylike_Advanced_Features();
