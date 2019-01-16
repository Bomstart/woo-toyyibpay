<?php
/**
 * Plugin Name: ToyyibPay for Woocommerce
 * Plugin URI: https://toyyibpay.com
 * Description: Enable online payments using online banking. Currently ToyyibPay service is only available to businesses that reside in Malaysia.
 * Version: 1.0.0
 * Author: Bomstart Media Sdn Bhd/Ansi Systems Sdn Bhd
 * Author URI: https://bomstart.my
 * WC requires at least: 2.6.0
 * WC tested up to: 3.3.0
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# Include ToyyibPay Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'toyyibpay_init', 0 );

function toyyibpay_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/toyyibpay.php' );

	add_filter( 'woocommerce_payment_gateways', 'add_toyyibpay_to_woocommerce' );
	function add_toyyibpay_to_woocommerce( $methods ) {
		$methods[] = 'toyyibpay';

		return $methods;
	}
}

# Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'toyyibpay_links' );

function toyyibpay_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=toyyibpay' ) . '">' . __( 'Settings', 'toyyibpay' ) . '</a>',
	);

	# Merge our new link with the default ones
	return array_merge( $plugin_links, $links );
}

add_action( 'init', 'toyyibpay_check_response', 15 );

function toyyibpay_check_response() {
	# If the parent WC_Payment_Gateway class doesn't exist it means WooCommerce is not installed on the site, so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/toyyibpay.php' );

	$toyyibpay = new toyyibpay();
	$toyyibpay->check_toyyibpay_response();
}

function toyyibpay_hash_error_msg( $content ) {
	return '<div class="woocommerce-error">The data that we received is invalid. Thank you.</div>' . $content;
}

function toyyibpay_payment_declined_msg( $content ) {
	return '<div class="woocommerce-error">The payment was declined. Please check with your bank. Thank you.</div>' . $content;
}

function toyyibpay_success_msg( $content ) {
	return '<div class="woocommerce-info">The payment was successful. Thank you.</div>' . $content;
}
