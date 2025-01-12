<?php
/**
 * Handle WooCommerce my-account settings.
 *
 * @class       Woo_My_Account_Settings
 * @version     1.0.0
 * @package     Marko_WooCommerce_Api_Fetch/Classes/
 */

namespace Marko_WooCommerce_Api_Fetch\Front;

use Marko_WooCommerce_Api_Fetch\Template;
use Marko_WooCommerce_Api_Fetch\Common;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin main class
 */
final class Woo_My_Account_Settings {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		add_action( 'init', array( __CLASS__, 'user_posts_endpoint' ) );
		add_filter( 'query_vars', array( __CLASS__, 'user_posts_query_vars' ), 0 );
		add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'add_user_posts_link_to_my_account' ) );
		add_action( 'woocommerce_account_user-posts_endpoint', array( __CLASS__, 'user_posts_tab_content' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'flush_rewrite_rules' ), 20 );
		add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'reorder_my_account_menu' ) );
		add_action( 'template_redirect', array( __CLASS__, 'save_account_details' ) );
	}

	// Register new endpoint for My Account page.
	public static function user_posts_endpoint() {
		add_rewrite_endpoint( 'user-posts', EP_ROOT | EP_PAGES );
	}

	// Add new query var.
	public static function user_posts_query_vars( $vars ) {
		$vars[] = 'user-posts';
		return $vars;
	}

	// Insert the new endpoint into the My Account menu.
	public static function add_user_posts_link_to_my_account( $items ) {
		$items['user-posts'] = 'User Posts';
		return $items;
	}

	// Add content to the new tab.
	// Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format.
	public static function user_posts_tab_content() {
		printf( '<h3>%s</h3>', esc_html__( 'User Specific Posts', 'marko-woocommerce-api-fetch' ) );
		Template::get_part( 'public', 'my-account-user-posts-form' );

		$response = Common\Api::get_formatted_response();

		echo wp_kses_post( $response );
	}

	// Flush permalinks once on plugin activation.
	public static function flush_rewrite_rules() {
		$permalinks_updated = (bool) get_option( 'marko_woocommerce_api_fetch_rewrite_rules', false );

		if ( ! $permalinks_updated ) {

			delete_option( 'rewrite_rules' );

			update_option( 'marko_woocommerce_api_fetch_rewrite_rules', true );
		}
	}

	// Rename, re-order my account menu items.
	public static function reorder_my_account_menu() {

		$new_order = array(
			'dashboard'       => __( 'Dashboard', 'marko-woocommerce-api-fetch' ),
			'orders'          => __( 'Orders', 'marko-woocommerce-api-fetch' ),
			'downloads'       => __( 'Downloads', 'marko-woocommerce-api-fetch' ),
			'edit-address'    => _n( 'Address', 'Addresses', ( 1 + (int) wc_shipping_enabled() ), 'marko-woocommerce-api-fetch' ),
			'payment-methods' => __( 'Payment methods', 'marko-woocommerce-api-fetch' ),
			'edit-account'    => __( 'Account details', 'marko-woocommerce-api-fetch' ),
			'user-posts'      => __( 'User Posts', 'marko-woocommerce-api-fetch' ),
			'customer-logout' => __( 'Log out', 'marko-woocommerce-api-fetch' ),
		);
		return $new_order;
	}

	/**
	 * Save user preferences in my-account/user-posts tab.
	 */
	public static function save_account_details() {
		$nonce_value = sanitize_key( wc_get_var( $_REQUEST['marko_woocommerce_api_fetch_nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ) );

		if ( ! wp_verify_nonce( $nonce_value, 'marko_woocommerce_api_fetch_action' ) ) {
			return;
		}

		if ( empty( $_POST['action'] ) || 'marko_woocommerce_api_fetch_action' !== $_POST['action'] ) {
			return;
		}

		$recipient_name = ! empty( $_POST['user_posts_filter_name'] ) ? sanitize_text_field( wp_unslash( $_POST['user_posts_filter_name'] ) ) : '';
		$pizza_size     = ! empty( $_POST['user_posts_filter_pizza_size'] ) ? sanitize_text_field( wp_unslash( $_POST['user_posts_filter_pizza_size'] ) ) : '';
		$pizza_topping  = ! empty( $_POST['user_posts_filter_pizza_topping'] ) ? map_deep( wp_unslash( $_POST['user_posts_filter_pizza_topping'] ), 'sanitize_text_field' ) : array();

		wc_nocache_headers();

		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return;
		}

		// Update user meta value.
		update_user_meta( $user_id, 'marko_waf_user_posts_filter_name', $recipient_name );
		update_user_meta( $user_id, 'marko_waf_user_posts_filter_pizza_size', $pizza_size );
		update_user_meta( $user_id, 'marko_waf_user_posts_filter_pizza_topping', $pizza_topping );
	}
}
