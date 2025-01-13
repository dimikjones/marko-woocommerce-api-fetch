<?php
/**
 * Handle common hooks.
 *
 * @class       Common
 * @version     1.0.0
 * @package     Marko_WooCommerce_Api_Fetch/Classes/
 */

namespace Marko_WooCommerce_Api_Fetch\Common;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common main class
 */
final class Main {

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {
		Api::hooks();
	}
}
