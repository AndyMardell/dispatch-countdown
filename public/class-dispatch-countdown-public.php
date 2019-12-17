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
	 * @param    string    $plugin_name  The name of the plugin.
	 * @param    string    $version      The version of this plugin.
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
	 * Initial display of the countdown before it's updated with ajax
	 *
	 * @since    1.0.0
	 */
	public function display_countdown() {

		if ( ! $this->product ) {
			$this->set_product();
		}

		do_action( 'dispatch_countdown_before_display_countdown' );

		if ( ! $this->countdown() ) {
			return;
		}

		$dispatch_countdown_content = $this->get_countdown_content(
			get_option( 'dispatch_countdown_wording' ),
			$this->product->get_id(),
			$this->countdown()
		);

		require_once plugin_dir_path( __FILE__ ) . 'partials/dispatch-countdown-public-display.php';

		do_action( 'dispatch_countdown_after_display_countdown' );

	}

	/**
	 * The countdown content
	 *
	 * Generates HTML and applies filters
	 *
	 * @since    1.0.7
	 * @param    string   $wording The wording from the settings page
	 * @param    int      $product The product ID
	 * @param    string   $countdown the countdown content (e.g. 1h 53m)
	 * @return   string   The HTML and filters
	 */
	public function get_countdown_content( $wording, $product, $countdown ) {
		$countdown_html = sprintf(
			'<div class="dispatch-countdown__inner">
				<p>
					%1$s
					<span
						class="dispatch-countdown__time"
						id="dispatch-countdown__time"
						data-for="%2$s"
					>
						%3$s
					</span>
				</p>
			</div>',
			esc_html( $wording ),
			esc_attr( $product ),
			esc_html( $countdown )
		);

		return apply_filters(
			'dispatch_countdown_content',
			$countdown_html,
			$wording,
			$product,
			$countdown
		);

	}

	/**
	 * Countdown function
	 *
	 * Works out what the countdown should say.
	 *
	 * @since    1.0.0
	 * @return   string|boolean   '1h 13m'|false
	 */
	public function countdown() {

		$time_settings = $this->get_time_settings();

		$timestamp = current_time( 'mysql' );
		$now       = new DateTime( $timestamp );
		$today     = strtolower( $now->format( 'l' ) );

		if ( ! $time_settings[ $today ] || ! $this->should_display() ) {
			return false;
		}

		$times     = explode( '-', $time_settings[ $today ] );
		$time_from = date_create_from_format( 'Gi', (int) str_replace( ':', '', $times[0] ) );
		$time_to   = date_create_from_format( 'Gi', (int) str_replace( ':', '', $times[1] ) );
		$diff      = $time_to->getTimestamp() - ( round( $now->getTimestamp() / 60 ) * 60 );

		if ( $now < $time_to && $now > $time_from ) {
			$duration = new Duration( $diff );
			return $duration->humanize();
		}

		return false;

	}

	/**
	 * Should display for product
	 *
	 * @since    1.0.0
	 * @return   boolean   Whether or not we should display the countdown
	 */
	private function should_display() {

		return $this->product && $this->product_is_purchasable() && ! $this->product_is_blacklisted();

	}

	/**
	 * Get time settings
	 *
	 * @since    1.0.0
	 * @return   array   An array of times  e.g. [ 'monday' => '10:00-12:00' ]
	 */
	public function get_time_settings() {

		$processed_times = array();

		$processed_times['monday']    = get_option( 'dispatch_countdown_monday' );
		$processed_times['tuesday']   = get_option( 'dispatch_countdown_tuesday' );
		$processed_times['wednesday'] = get_option( 'dispatch_countdown_wednesday' );
		$processed_times['thursday']  = get_option( 'dispatch_countdown_thursday' );
		$processed_times['friday']    = get_option( 'dispatch_countdown_friday' );
		$processed_times['saturday']  = get_option( 'dispatch_countdown_saturday' );
		$processed_times['sunday']    = get_option( 'dispatch_countdown_sunday' );

		return $processed_times;

	}

	/**
	 * Ajax get countdown
	 *
	 * An Ajax function which gets the current countdown in order to update the
	 * node with JS.
	 *
	 * @since    1.0.0
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

		$product_id = (int) sanitize_text_field( wp_unslash( $_POST['product'] ) );
		$this->set_product( $product_id );

		$response = array(
			'status'  => 200,
			'content' => $this->countdown(),
		);

		wp_die( wp_json_encode( $response ) );

	}

	/**
	 * Get product
	 *
	 * @since                 1.0.0
	 * @param   int           $id ID of product
	 * @return  object|null   $this->product
	 */
	public function get_product( $id = false ) {

		do_action( 'dispatch_countdown_before_get_product' );

		if ( ! $this->product ) {
			$this->set_product( $id );
		}

		do_action( 'dispatch_countdown_after_get_product' );

		return $this->product;

	}

	/**
	 * Get product
	 *
	 * @since                 1.0.0
	 * @param   int           $id ID of product.
	 * @return  object|null   $this->product
	 */
	public function set_product( $id = false ) {

		if ( $id ) {
			$this->product = wc_get_product( $id );
			return $this->product;
		}

		global $product;

		if ( ! $product ) {
			return false;
		}

		$this->product = wc_get_product( $product->get_id() );

		return $this->product;

	}

	/**
	 * Check if product is purchasable
	 *
	 * @since           1.0.0
	 * @return   bool   Whether or not the current product is purchasable
	 */
	public function product_is_purchasable() {

		return $this->product->is_purchasable() && $this->product->is_in_stock();

	}

	/**
	 * Check if product is blacklisted
	 *
	 * @since           1.0.0
	 * @return   bool   Whether or not the current product is blacklisted
	 */
	public function product_is_blacklisted() {

		$blacklist     = get_option( 'dispatch_countdown_blacklist' );
		$blacklist_arr = explode( ',', $blacklist );

		if ( in_array( (string) $this->product->get_id(), $blacklist_arr, true ) ) {
			return true;
		}

		return false;

	}

}
