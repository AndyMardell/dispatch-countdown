<?php
/**
 * Dispatch Countdown
 *
 * This plugin is the home of the countdown on product pages. It gets some times
 * and displays "x hours left for same day dispatch".
 *
 * @link              mardell.me
 * @since             1.0.0
 * @package           Dispatch_Countdown
 *
 * @wordpress-plugin
 * Plugin Name:       Dispatch Countdown
 * Description:       Displays x hours left for same day dispatch on WooCommerce shop pages.
 * Version:           1.0.5
 * Author:            Andy Mardell
 * Author URI:        mardell.me
 * Text Domain:       dispatch-countdown
 * Domain Path:       /languages
 *
 * WC requires at least: 2.2
 * WC tested up to: 3.8
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'DISPATCH_COUNTDOWN_VERSION', '1.0.5' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dispatch-countdown.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function dispatch_run_countdown() {

	if (
		! in_array(
			'woocommerce/woocommerce.php',
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		)
	) {
		return false;
	}

	$plugin = new Dispatch_Countdown();
	$plugin->run();

}
dispatch_run_countdown();
