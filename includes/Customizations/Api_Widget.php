<?php
/**
 * Widgets Hooks
 *
 * @package     Marko_WooCommerce_Api_Fetch/Customizations
 * @version     1.0.0
 */

namespace Marko_WooCommerce_Api_Fetch\Customizations;

use Marko_WooCommerce_Api_Fetch\Common;
use \WP_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Api_Widget Class.
 */
class Api_Widget extends WP_Widget {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'widgets_init', array( __CLASS__, 'register_widget' ) );
	}

	/**
	 * Constructs the new widget.
	 *
	 * @see WP_Widget::__construct()
	 */
	public function __construct() {
		// Instantiate the parent object.
		$widget_ops = array(
			'description' => esc_html__( 'Widget for displaying user preferences response from API Call.' ),
		);

		parent::__construct( 'api_widget', 'API Widget', $widget_ops );
	}

	/**
	 * The widget's HTML output.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Display arguments including before_title, after_title,
	 *                        before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		if ( is_user_logged_in() ) {
			$response = Common\Api::get_formatted_response();

			echo wp_kses_post( $response );
		} else {
			echo esc_html__( 'Log in in order to access your preferences.', 'marko-woocommerce-api-fetch' );
		}
	}

	/**
	 * The widget update handler.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance The new instance of the widget.
	 * @param array $old_instance The old instance of the widget.
	 * @return array The updated instance of the widget.
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Output the admin widget options form HTML.
	 *
	 * @param array $instance The current widget settings.
	 * @return string The HTML markup for the form.
	 */
	public function form( $instance ) {
		echo '<p class="no-options-widget">' . esc_html__( 'There are no options for this widget.', 'marko-woocommerce-api-fetch' ) . '</p>';
		return '';
	}

	public static function register_widget() {
		register_widget( self::class );
	}
}
