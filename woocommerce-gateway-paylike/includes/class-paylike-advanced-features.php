<?php

/**
 * Optional features, not related to the core of the payment process
 */
class Paylike_Advanced_Features {


	public $settings;

	public function __construct() {
		$this->settings = get_option( 'woocommerce_paylike_settings' );
		if ( ! $this->needed() ) {
			return;
		}
		$this->actions();
	}

	/**
	 *
	 */
	public function actions() {
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_surcharge' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 *
	 */
	public function scripts() {
		global $wp_version;
		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) && ! is_add_payment_method_page() ) {
			return;
		}
		wp_enqueue_script( 'woocommerce_paylike
	_advanced', plugins_url( 'assets/js/paylike_advanced.js', WC_PAYLIKE_MAIN_FILE ), array(), WC_PAYLIKE_VERSION, true );
	}

	/**
	 *
	 */
	public function add_surcharge() {
		global $woocommerce;
		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
		if ( $chosen_payment_method !== 'paylike' ) {
			return;
		}
		$flat_fee = $this->settings['surcharge_fee'];
		if ( $flat_fee ) {
			$woocommerce->cart->add_fee( $this->get_fee_name(), wc_format_decimal( $flat_fee ), true, 'standard' );
		}
		$percentage = $this->settings['surcharge_percentage'];
		if ( $percentage ) {
			$amount = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * ( $percentage / 100 );
			$woocommerce->cart->add_fee( $this->get_fee_name( true ), wc_format_decimal( $amount ), true, 'standard' );
		}

	}

	/**
	 * @return bool
	 */
	public function needed() {
		return ( $this->settings['surcharge_percentage'] > 0 || $this->settings['surcharge_fee'] > 0 );
	}

	/**
	 * @param bool $percentage
	 *
	 * @return mixed
	 */
	public function get_fee_name( $percentage = false ) {
		$fee_name = $this->settings['surcharge_fee_name'];
		if ( $percentage && $this->settings['surcharge_percentage_fee_name'] ) {
			$fee_name = $this->settings['surcharge_percentage_fee_name'];
		}
		if ( ! $fee_name ) {
			$fee_name = $this->settings['surcharge_percentage_fee_name'];
		}

		return $fee_name;
	}
}

$paylike_advanced = new Paylike_Advanced_Features();
