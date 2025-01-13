<?php
/**
 * Handle admin settings.
 *
 * @class       Admin
 * @version     1.0.0
 * @package     Marko_WooCommerce_Api_Fetch/Classes/
 */

namespace Marko_WooCommerce_Api_Fetch\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin main class
 */
final class Admin_Settings {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		// Hook to include additional modules before plugin loaded.
		do_action( 'marko_woocommerce_api_fetch_action_before_admin_options_loaded' );

		// Register our settings_init to the admin_init action hook.
		add_action( 'admin_init', array( __CLASS__, 'settings_init' ) );

		// Register our options_page to the admin_menu action hook.
		add_action( 'admin_menu', array( __CLASS__, 'options_page' ) );

		// Hook to include additional modules when plugin loaded.
		do_action( 'marko_woocommerce_api_fetch_action_after_admin_options_loaded' );
	}

	/**
	 * Custom option and settings.
	 */
	public static function settings_init() {
		// Register a new setting for "marko-woocommerce-api-fetch" page.
		register_setting( 'marko-woocommerce-api-fetch', 'marko_woocommerce_api_fetch_options' );

		// Register a new section in the "marko-woocommerce-api-fetch" page.
		add_settings_section(
			'marko_woocommerce_api_fetch_section_first',
			esc_html__( 'Adjust options here.', 'marko-woocommerce-api-fetch' ),
			array( __CLASS__, 'section_first' ),
			'marko-woocommerce-api-fetch'
		);

		// Register a new field in the "marko_woocommerce_api_fetch_section_first" section, inside the "marko-woocommerce-api-fetch" page.
		add_settings_field(
			'marko_woocommerce_api_fetch_field_transient_time',
			// Use $args' label_for to populate the id inside the callback.
			esc_html__( 'Transient Expiration Time', 'marko-woocommerce-api-fetch' ),
			array( __CLASS__, 'transient_time_cb' ),
			'marko-woocommerce-api-fetch',
			'marko_woocommerce_api_fetch_section_first',
			array(
				'label_for' => 'marko-woocommerce-api-fetch-field-transient',
				'class'     => 'marko-woocommerce-api-fetch-row',
				'marko_woocommerce_api_fetch_custom_data' => 'custom',
			)
		);
	}

	/**
	 * Transient Expiration Time field callback method.
	 *
	 * @param array $args
	 */
	public static function transient_time_cb( $args ) {
		// Get the value of the setting we've registered with register_setting().
		$options = get_option( 'marko_woocommerce_api_fetch_options' );
		?>
		<select
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			data-custom="<?php echo esc_attr( $args['marko_woocommerce_api_fetch_custom_data'] ); ?>"
			name="marko_woocommerce_api_fetch_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
			<option value="" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '', false ) ) : ( '' ); ?>>
				<?php esc_html_e( 'None', 'marko-woocommerce-api-fetch' ); ?>
			</option>
			<option value="<?php echo esc_attr( HOUR_IN_SECONDS ); ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], esc_attr( HOUR_IN_SECONDS ), false ) ) : ( '' ); ?>>
				<?php esc_html_e( 'Hour', 'marko-woocommerce-api-fetch' ); ?>
			</option>
			<option value="<?php echo esc_attr( DAY_IN_SECONDS ); ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], esc_attr( DAY_IN_SECONDS ), false ) ) : ( '' ); ?>>
				<?php esc_html_e( 'Day', 'marko-woocommerce-api-fetch' ); ?>
			</option>
			<option value="<?php echo esc_attr( WEEK_IN_SECONDS ); ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], esc_attr( WEEK_IN_SECONDS ), false ) ) : ( '' ); ?>>
				<?php esc_html_e( 'Week', 'marko-woocommerce-api-fetch' ); ?>
			</option>
			<option value="<?php echo esc_attr( MONTH_IN_SECONDS ); ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], esc_attr( MONTH_IN_SECONDS ), false ) ) : ( '' ); ?>>
				<?php esc_html_e( 'Month', 'marko-woocommerce-api-fetch' ); ?>
			</option>
		</select>
		<p class="description">
			<?php esc_html_e( 'By choosing None transient will be disabled.', 'marko-woocommerce-api-fetch' ); ?>
		</p>
		<?php
	}

	/**
	 * First section callback method.
	 *
	 * @param array $args  The settings array, defining title, id, callback.
	 */
	public static function section_first( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Options for the WooCommerce API Fetch Plugin.', 'marko-woocommerce-api-fetch' ); ?></p>
		<?php
	}

	/**
	 * Top level menu callback method.
	 */
	public static function options_page_html() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// WPCS: input var ok.
		if ( isset( $_REQUEST['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			// Add settings saved message with the class of "updated".
			add_settings_error( 'marko_woocommerce_api_fetch_messages', 'marko_woocommerce_api_fetch_message', esc_html__( 'Settings Saved', 'marko-woocommerce-api-fetch' ), 'updated' );
		}

		// Show error/update messages.
		settings_errors( 'marko_woocommerce_api_fetch_messages' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WooCommerce API Fetch Settings Page', 'marko-woocommerce-api-fetch' ); ?></h1>
			<form class="marko-woocommerce-api-fetch-admin-form" action="options.php" method="post">
				<?php
				// Output security fields for the registered setting "marko-woocommerce-api-fetch".
				settings_fields( 'marko-woocommerce-api-fetch' );
				// Output setting sections and their fields.
				// (sections are registered for "marko-woocommerce-api-fetch", each field is registered to a specific section).
				do_settings_sections( 'marko-woocommerce-api-fetch' );
				// output save settings button.
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Add the top level menu page.
	 */
	public static function options_page() {
		add_menu_page(
			'marko-woocommerce-api-fetch',
			'Woocommerce API fetch Options',
			'manage_options',
			'marko-woocommerce-api-fetch',
			array( __CLASS__, 'options_page_html' )
		);
	}
}