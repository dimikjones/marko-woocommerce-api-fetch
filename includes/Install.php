<?php
/**
 * Handle plugin's install actions.
 *
 * @class       Install
 * @version     1.0.0
 * @package     Marko_WooCommerce_Api_Fetch/Classes/
 */

namespace Marko_WooCommerce_Api_Fetch;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Install class
 */
final class Install {

	/**
	 * Install action.
	 */
	public static function install( $sitewide = false ) {

		// Perform install actions here.

		// Trigger action.
		do_action( 'marko_woocommerce_api_fetch_installed', $sitewide );
	}

	/**
	 * Deactivate action.
	 */
	public static function deactivate( $sitewide = false ) {

		// Perform install actions here.

		// Trigger action.
		do_action( 'marko_woocommerce_api_fetch_deactivated', $sitewide );
	}
}
