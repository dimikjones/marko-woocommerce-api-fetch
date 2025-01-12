<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Marko_WooCommerce_Api_Fetch
 * @subpackage Marko_WooCommerce_Api_Fetch/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$user_id = get_current_user_id();

if ( $user_id <= 0 ) {
	return;
}

// set query.
global $wp;

$url = home_url( add_query_arg( array(), $wp->request ) );
$url = add_query_arg( 'action', 'marko_woocommerce_api_fetch_action', $url );
$url = wp_nonce_url( $url, 'marko_woocommerce_api_fetch_nonce' );

$holder_classes   = array();
$holder_classes[] = 'marko-waf-admin-form-section woocommerce';

$user_posts_filter_name          = get_user_meta( $user_id, 'marko_waf_user_posts_filter_name', true );
$user_posts_filter_pizza_size    = get_user_meta( $user_id, 'marko_waf_user_posts_filter_pizza_size', true );
$user_posts_filter_pizza_topping = get_user_meta( $user_id, 'marko_waf_user_posts_filter_pizza_topping', true );
?>
<div class="<?php echo esc_attr( implode( '', $holder_classes ) ); ?>">
	<form class="marko-waf-admin-form" method="post" action="<?php echo esc_url( $url ); ?>">
		<div class="marko-waf-admin-options-block">
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="user_posts_filter_name"><?php esc_html_e( 'Name', 'marko-woocommerce-api-fetch' ); ?></label>
				<input type="text" autocomplete="given-name" class="woocommerce-Input woocommerce-Input--text input-text" name="user_posts_filter_name" id="user_posts_filter_name" value="<?php echo esc_attr( $user_posts_filter_name ); ?>" />
				<span><em><?php esc_html_e( 'Enter name for the recipient.', 'marko-woocommerce-api-fetch' ); ?></em></span>
			</p>
			<fieldset>
				<legend><?php esc_html_e( 'Pizza Size', 'marko-woocommerce-api-fetch' ); ?></legend>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label>
						<input type="radio" <?php echo ! empty( $user_posts_filter_pizza_size ) && 'small' === $user_posts_filter_pizza_size ? esc_attr( 'checked' ) : ''; ?> name="user_posts_filter_pizza_size" value="<?php echo esc_attr( 'small' ); ?>">
						<?php esc_html_e( 'Small', 'marko-woocommerce-api-fetch' ); ?>
					</label>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label>
						<input type="radio" <?php echo ! empty( $user_posts_filter_pizza_size ) && 'medium' === $user_posts_filter_pizza_size ? esc_attr( 'checked' ) : ''; ?> name="user_posts_filter_pizza_size" value="<?php echo esc_attr( 'medium' ); ?>">
						<?php esc_html_e( 'Medium', 'marko-woocommerce-api-fetch' ); ?>
					</label>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label>
						<input type="radio" <?php echo ! empty( $user_posts_filter_pizza_size ) && 'large' === $user_posts_filter_pizza_size ? esc_attr( 'checked' ) : ''; ?> name="user_posts_filter_pizza_size" value="<?php echo esc_attr( 'large' ); ?>">
						<?php esc_html_e( 'Large', 'marko-woocommerce-api-fetch' ); ?>
					</label>
				</p>
			</fieldset>
			<fieldset>
				<legend><?php esc_html_e( 'Pizza Toppings', 'marko-woocommerce-api-fetch' ); ?></legend>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label>
						<input type="checkbox" <?php echo in_array( 'bacon', $user_posts_filter_pizza_topping ) ? 'checked' : ''; ?> name="user_posts_filter_pizza_topping[]" value="<?php echo esc_attr( 'bacon' ); ?>">
						<?php esc_html_e( 'Bacon', 'marko-woocommerce-api-fetch' ); ?>
					</label>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label>
						<input type="checkbox" <?php echo in_array( 'cheese', $user_posts_filter_pizza_topping ) ? 'checked' : ''; ?> name="user_posts_filter_pizza_topping[]" value="<?php echo esc_attr( 'cheese' ); ?>">
						<?php esc_html_e( 'Extra Cheese', 'marko-woocommerce-api-fetch' ); ?>
					</label>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label><input type="checkbox" <?php echo in_array( 'onion', $user_posts_filter_pizza_topping ) ? 'checked' : ''; ?> name="user_posts_filter_pizza_topping[]" value="<?php echo esc_attr( 'onion' ); ?>">
						<?php esc_html_e( 'Onion', 'marko-woocommerce-api-fetch' ); ?>
					</label>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label>
						<input type="checkbox" <?php echo in_array( 'mushroom', $user_posts_filter_pizza_topping ) ? 'checked' : ''; ?> name="user_posts_filter_pizza_topping[]" value="<?php echo esc_attr( 'mushroom' ); ?>">
						<?php esc_html_e( 'Mushroom', 'marko-woocommerce-api-fetch' ); ?>
					</label>
				</p>
			</fieldset>
		</div>
		<div class="marko-waf-admin-submit-block">
			<?php wp_nonce_field( 'marko_woocommerce_api_fetch_action', 'marko_woocommerce_api_fetch_nonce' ); ?>
			<button type="submit" class="marko-waf-admin-submit-button button">
				<?php esc_html_e( 'Save', 'marko-woocommerce-api-fetch' ); ?>
			</button>
			<input type="hidden" name="action" value="marko_woocommerce_api_fetch_action" />
		</div>
	</form>
</div>
