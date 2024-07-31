<?php
/*
 * Plugin Name: WC Price History
 * Description: Track WooCommerce Products prior prices history and display the lowest price in the last 30 days (fully configurable). This plugin allows your WC shop to be compliant with European Commission Directive 98/6/EC Article 6a which specifies price reduction announcement policy.
 * Author: Konrad Karpieszuk
 * Author URI: https://wpzlecenia.pl
 * Version: 2.0.1
 * Text Domain: wc-price-history
 * Domain Path: /languages/
 * Requires at least: 5.8
 * Requires PHP: 7.2
 * Plugin URI: https://github.com/kkarpieszuk/wc-price-history
 */

use PriorPrice\Hooks;

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/constants.php';

define( 'WC_PRICE_HISTORY_VERSION', '2.0.1' );

/**
 * Get the plugin version.
 *
 * @since 2.0.1
 *
 * @return string
 */
function get_wc_price_history_version(): string {
	return WC_PRICE_HISTORY_VERSION;
}

// Handle missing WooCommerce.
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	add_action( 'admin_notices', function () {
		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'WooCommerce Price History plugin requires WooCommerce to be installed and active.', 'wc-price-history' ); ?></p>
		</div>
		<?php
	} );
	return;
}

$hooks = new Hooks();
$hooks->register_hooks();
