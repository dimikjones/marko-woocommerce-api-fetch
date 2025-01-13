<?php
/**
 * Register common api.
 *
 * @class       CommonApi
 * @version     1.0.0
 * @package     Marko_WooCommerce_Api_Fetch/Classes/
 */

namespace Marko_WooCommerce_Api_Fetch\Common;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common api class
 */
final class Api {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		add_action( 'updated_user_meta', array( __CLASS__, 'when_update_any_user_meta' ) );
	}

	/**
	 * Get the post response after making a request to the receiver.
	 *
	 * @return    mixed | void    $html    The modified content included the post information.
	 */
	public static function get_post_response() {

		// Request variable $site_url.
		$site_url = site_url();

		// Request variable $api_url.
		$api_url = 'https://httpbin.org/post';

		$user_id = get_current_user_id();

		$get_plugin_options = get_option( 'marko_woocommerce_api_fetch_options' );

		$transient_option = maybe_unserialize( $get_plugin_options['marko-woocommerce-api-fetch-field-transient'] );
		$transient_option = isset( $transient_option ) && ! empty( $transient_option ) ? $transient_option : 0;

		if ( 0 !== $transient_option && get_transient( 'marko_woocommerce_api_fetch_transient_id_' . $user_id ) ) {
			$html = get_transient( 'marko_woocommerce_api_fetch_transient_id_' . $user_id );
		} else {

			if ( $user_id <= 0 ) {
				return;
			}

			$user_posts_filter_name          = get_user_meta( $user_id, 'marko_waf_user_posts_filter_name', true );
			$user_posts_filter_pizza_size    = get_user_meta( $user_id, 'marko_waf_user_posts_filter_pizza_size', true );
			$user_posts_filter_pizza_topping = get_user_meta( $user_id, 'marko_waf_user_posts_filter_pizza_topping', true );

			if ( empty( $user_posts_filter_name ) || empty( $user_posts_filter_pizza_size ) || empty( $user_posts_filter_pizza_topping ) ) {
				return;
			}

			// Request variables $body.
			$body = [
				'name'          => $user_posts_filter_name,
				'pizza_size'    => $user_posts_filter_pizza_size,
				'pizza_topping' => $user_posts_filter_pizza_topping,
			];

			$body = wp_json_encode( $body );

			// Complete request variables $options.
			$options = [
				'body'    => $body,
				'timeout' => 10,
				'address' => esc_url( $site_url ),
			];

			// Make the remote request and retrieve the response.
			$response = wp_remote_post(
				esc_url( $api_url ),
				$options
			);

			// If there's an error, display a message.
			if ( is_wp_error( $response ) ) {
				$html = '<div id="post-error">';
				$html .= esc_html__( 'There was a problem retrieving the response from the server.', 'marko-woocommerce-api-fetch' );
				$html .= '</div>';
			} else {
				$initial_response = json_decode( $response['body'], true );

				$needed_response = array_keys( $initial_response['form'] )[0];
				$needed_response = json_decode( $needed_response );

				$html = $needed_response;

				set_transient( 'marko_woocommerce_api_fetch_transient_id_' . $user_id, $html, intval( $transient_option ) );
			}
		}

		return $html;
	}

	public static function get_formatted_response() {
		$response = self::get_post_response();

		$response_arr = array();

		! empty( $response->name ) ? $response_arr[ esc_html__( 'Recipient Name', 'marko-woocommerce-api-fetch' ) ] = $response->name : [];
		! empty( $response->pizza_size ) ? $response_arr[ esc_html__( 'Pizza Size', 'marko-woocommerce-api-fetch' ) ] = $response->pizza_size : [];
		! empty( $response->pizza_topping ) ? $response_arr[ esc_html__( 'Pizza Topping', 'marko-woocommerce-api-fetch' ) ] = $response->pizza_topping : [];

		$html = '<div class="marko-waf-public user-preferences">';

		$html .= "<h4 class='marko-waf-heading'>" . esc_html__( 'User Preferences from API Response', 'marko-woocommerce-api-fetch' ) . "</h4>";

		foreach ( $response_arr as $key => $preference ) :
			$preference = is_array( $preference ) ? implode( ', ', $preference ) : $preference;
			$html .= "<p><strong>" . $key . "</strong> " . $preference . '</p>';
		endforeach;

		$html .= '</div>';

		return $html;
	}

	// When user meta gets updated delete transient.
	public static function when_update_any_user_meta() {
		$user_id = get_current_user_id();

		delete_transient( 'marko_woocommerce_api_fetch_transient_id_' . $user_id );
	}
}
