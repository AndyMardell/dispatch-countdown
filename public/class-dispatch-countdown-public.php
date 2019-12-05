<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       mardell.me
 * @since      1.0.0
 *
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/public
 */

use Khill\Duration\Duration;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/public
 * @author     Andy Mardell <mardell.me>
 */
class Dispatch_Countdown_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The product
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $product    The current product.
	 */
	private $product;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the scripts and stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_files() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dispatch-countdown-public.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dispatch-countdown-public.js', array( 'jquery' ), $this->version, true );

		wp_localize_script(
			$this->plugin_name,
			'dispatch_countdown',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'dispatch_countdown' ),
			)
		);

	}

	/**
	 * Display the countdown
	 *
	 * @since    1.0.0
	 */
	public function display_countdown() {

		do_action( 'dispatch_countdown_before_display_countdown' );

		$countdown = $this->countdown();

		if ( ! $countdown ) {
			return;
		}

		$product_id = $this->product->get_id();
		$settings   = get_option( 'dispatch_countdown_options' );
		$wording    = $settings['wording'];

		require_once plugin_dir_path( __FILE__ ) . 'partials/dispatch-countdown-public-display.php';

		do_action( 'dispatch_countdown_after_display_countdown' );

	}

	/**
	 * Countdown function
	 *
	 * @since 1.0.0
	 */
	public function countdown() {
		$time_settings = $this->get_time_settings();

		$timestamp = current_time( 'mysql' );
		$now       = new DateTime( $timestamp );
		$today     = strtolower( $now->format( 'l' ) );

		if ( ! $time_settings[ $today ] ||
			! $this->product ||
			! $this->product_is_purchasable() ||
			$this->product_is_blacklisted()
		) {
			return false;
		}

		$times     = explode( '-', $time_settings[ $today ] );
		$time_from = date_create_from_format( 'Gi', (int) $times[0] );
		$time_to   = date_create_from_format( 'Gi', (int) $times[1] );
		$diff      = $time_to->getTimestamp() - ( round( $now->getTimestamp() / 60 ) * 60 );

		if ( $now < $time_to && $now > $time_from ) {
			$duration = new Duration( $diff );
			return $duration->humanize();
		}

		return false;

	}

	/**
	 * Get time settings
	 *
	 * TODO: Something better when checking for times
	 *
	 * @since 1.0.0
	 */
	public function get_time_settings() {
		$time_settings   = get_option( 'dispatch_countdown_options' );
		$processed_times = array();

		foreach ( $time_settings as $key => $value ) {
			if ( 'wording' === $key ||
				'blacklist' === $key ||
				'enabled' === $key
			) {
				continue;
			}

			$new_value               = $value ? $value : false;
			$processed_times[ $key ] = $new_value;
		}

		return $processed_times;
	}

	/**
	 * Ajax get countdown
	 *
	 * @since 1.0.0
	 */
	public function get_countdown() {
		if ( ! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dispatch_countdown' )
		) {
			die( 'Permission Denied' );
		}

		if ( ! isset( $_POST['product'] ) ) {
			die( 'No product' );
		}

		$product = sanitize_text_field( wp_unslash( $_POST['product'] ) );
		$this->get_product( (int) $product );

		$response = array(
			'status'  => 200,
			'content' => $this->countdown(),
		);

		wp_die( wp_json_encode( $response ) );

	}

	/**
	 * Get product
	 *
	 * @since     1.0.0
	 * @param int $id ID of product.
	 */
	public function get_product( $id = false ) {
		$id = $id ? $id : false;

		do_action( 'dispatch_countdown_before_get_product' );

		if ( ! $this->product ) {
			$this->product = wc_get_product( $id );
		}

		do_action( 'dispatch_countdown_after_get_product' );

		return $this->product;

	}

	/**
	 * Check if product is purchasable
	 *
	 * @since 1.0.0
	 */
	public function product_is_purchasable() {

		if ( ! $this->product ) {
			return false;
		}

		return $this->product->is_purchasable() && $this->product->is_in_stock();

	}

	/**
	 * Check if product is blacklisted
	 *
	 * @since 1.0.0
	 */
	public function product_is_blacklisted() {

		if ( ! $this->product ) {
			return false;
		}

		$settings      = get_option( 'dispatch_countdown_options' );
		$blacklist     = $settings['blacklist'];
		$blacklist_arr = explode( ',', $blacklist );

		if ( in_array( (string) $this->product->get_id(), $blacklist_arr, true ) ) {
			return true;
		}

		return false;
	}

}
