<?php
/**
 * Plugin Name: Bizappay for Woocommerce
 * Plugin URI: https://bizappay.my
 * Description: Enable online payments using online banking. Currently Bizappay service is only available to businesses that reside in Malaysia.
 * Version: 1.0.0
 * Author: Ansi Systems Sdn Bhd
 * Author URI: https://bizappay.my
 * WC requires at least: 2.6.0
 * WC tested up to: 3.3.0
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# Include Bizappay Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'bizappay_init', 0 );

function bizappay_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/bizappay.php' );

	add_filter( 'woocommerce_payment_gateways', 'add_bizappay_to_woocommerce' );
	function add_bizappay_to_woocommerce( $methods ) {
		$methods[] = 'Bizappay';

		return $methods;
	}
}

# Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bizappay_links' );

function bizappay_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=bizappay' ) . '">' . __( 'Settings', 'bizappay' ) . '</a>',
	);

	# Merge our new link with the default ones
	return array_merge( $plugin_links, $links );
}

add_action( 'init', 'bizappay_check_response', 15 );

function bizappay_check_response() {
	# If the parent WC_Payment_Gateway class doesn't exist it means WooCommerce is not installed on the site, so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/bizappay.php' );

	$bizappay = new bizappay();
	$bizappay->check_bizappay_response();
}

function bizappay_hash_error_msg( $content ) {
	return '<div class="woocommerce-error">The data that we received is invalid. Thank you.</div>' . $content;
}

function bizappay_payment_declined_msg( $content ) {
	return '<div class="woocommerce-error">The payment was declined. Please check with your bank. Thank you.</div>' . $content;
}

function bizappay_success_msg( $content ) {
	return '<div class="woocommerce-info">The payment was successful. Thank you.</div>' . $content;
}
