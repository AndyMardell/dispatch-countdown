<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       mardell.me
 * @since      1.0.0
 *
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/admin
 * @author     Andy Mardell <mardell.me>
 */
class Dispatch_Countdown_Admin {

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
	 * The options for the settings pages
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $options    The options from the settings page
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name  The name of this plugin.
	 * @param string $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the scripts and stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_files() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dispatch-countdown-admin.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dispatch-countdown-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add settings section
	 *
	 * @param array $sections Array of existing sections
	 * @since       1.0.0
	 */
	public function add_settings_section( $sections ) {

		$sections['dispatch_countdown'] = __( 'Dispatch Countdown', 'dispatch-countdown' );

		return $sections;

	}

	/**
	 * Init plugin settings page.
	 *
	 * @param array  $settings        Array of settings
	 * @param string $current_section ID of current section
	 * @since        1.0.0
	 */
	public function settings_page_init( $settings, $current_section ) {

		if ( 'dispatch_countdown' !== $current_section ) {
			return $settings;
		}

		$dispatch_settings = array();

		$dispatch_settings[] = array(
			'name' => __( 'Dispatch Countdown Settings', 'dispatch-countdown' ),
			'type' => 'title',
			'css'  => 'min-width:300px;',
			'desc' => __( 'The following options are used to configure the dispatch countdown banner', 'dispatch-countdown' ),
			'id'   => 'dispatch_countdown',
		);

		$dispatch_settings[] = array(
			'name' => __( 'Enable', 'dispatch-countdown' ),
			'id'   => 'dispatch_countdown_enabled',
			'type' => 'checkbox',
			'css'  => 'min-width:300px;',
			'desc' => __( 'Enable the dispatch countdown', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Wording', 'dispatch-countdown' ),
			'desc_tip' => __( 'The text to be shown before the countdown', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_wording',
			'type'     => 'text',
			'default'  => __( '🕐 Same day dispatch if you order within', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'type' => 'sectionend',
			'id'   => 'dispatch_countdown',
		);

		$dispatch_settings[] = array(
			'name' => __( 'Schedule', 'dispatch-countdown' ),
			'type' => 'title',
			'desc' => __( 'Decides when the banner should show. If the start time is 12:00 and the end time is 16:00, the banner will show from midday and countdown until 4pm. Leave blank to disable.', 'dispatch-countdown' ),
			'id'   => 'dispatch_countdown_schedule',
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Monday', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 09:00-16:00', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_monday',
			'type'     => 'text',
			'default'  => __( '12:00-16:00', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Tuesday', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 09:00-16:00', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_tuesday',
			'type'     => 'text',
			'default'  => __( '12:00-16:00', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Wednesday', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 09:00-16:00', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_wednesday',
			'type'     => 'text',
			'default'  => __( '12:00-16:00', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Thursday', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 09:00-16:00', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_thursday',
			'type'     => 'text',
			'default'  => __( '12:00-16:00', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Friday', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 09:00-16:00', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_friday',
			'type'     => 'text',
			'default'  => __( '12:00-16:00', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Saturday', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 09:00-16:00', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_saturday',
			'type'     => 'text',
			'default'  => __( '09:00-12:00', 'dispatch-countdown' ),
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Sunday', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 09:00-16:00', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_sunday',
			'type'     => 'text',
		);

		$dispatch_settings[] = array(
			'type' => 'sectionend',
			'id'   => 'dispatch_countdown_schedule',
		);

		$dispatch_settings[] = array(
			'name' => __( 'Advanced Options', 'dispatch-countdown' ),
			'type' => 'title',
			'id'   => 'dispatch_countdown_schedule',
		);

		$dispatch_settings[] = array(
			'name'     => __( 'Blacklisted Products', 'dispatch-countdown' ),
			'desc'     => __( 'List of WooCommerce product IDs to blacklist', 'dispatch-countdown' ),
			'desc_tip' => __( 'Format: 12,24,122', 'dispatch-countdown' ),
			'id'       => 'dispatch_countdown_blacklist',
			'type'     => 'text',
		);

		$dispatch_settings[] = array(
			'type' => 'sectionend',
			'id'   => 'dispatch_countdown_schedule',
		);

		return $dispatch_settings;

	}

}
