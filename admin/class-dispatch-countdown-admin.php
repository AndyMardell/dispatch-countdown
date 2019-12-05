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
	 * Add plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_page() {
		add_options_page(
			'Dispatch Countdown',
			'Dispatch Countdown',
			'manage_options',
			'dispatch-countdown-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Create plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function create_admin_page() {

		$this->options = get_option( 'dispatch_countdown_options' );

		require_once plugin_dir_path( __FILE__ ) . 'partials/dispatch-countdown-admin-display.php';

	}

	/**
	 * Init plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function page_init() {

		register_setting(
			'dispatch_countdown_settings_fields',
			'dispatch_countdown_options',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'dispatch_countdown_global_settings',
			'Global Settings',
			null,
			'dispatch-countdown-admin'
		);

		add_settings_field(
			'enabled',
			'Enabled',
			array( $this, 'enabled_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_global_settings'
		);

		add_settings_field(
			'wording',
			'Wording',
			array( $this, 'wording_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_global_settings'
		);

		add_settings_section(
			'dispatch_countdown_time_settings',
			'Time Settings',
			array( $this, 'time_settings_info' ),
			'dispatch-countdown-admin'
		);

		add_settings_field(
			'monday',
			'Monday',
			array( $this, 'day_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_time_settings',
			'monday'
		);

		add_settings_field(
			'tuesday',
			'Tuesday',
			array( $this, 'day_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_time_settings',
			'tuesday'
		);

		add_settings_field(
			'wednesday',
			'Wednesday',
			array( $this, 'day_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_time_settings',
			'wednesday'
		);

		add_settings_field(
			'thursday',
			'Thursday',
			array( $this, 'day_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_time_settings',
			'thursday'
		);

		add_settings_field(
			'friday',
			'Friday',
			array( $this, 'day_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_time_settings',
			'friday'
		);

		add_settings_field(
			'saturday',
			'Saturday',
			array( $this, 'day_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_time_settings',
			'saturday'
		);

		add_settings_field(
			'sunday',
			'Sunday',
			array( $this, 'day_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_time_settings',
			'sunday'
		);

		add_settings_section(
			'dispatch_countdown_product_blacklist',
			'Product Blacklist',
			array( $this, 'product_blacklist_info' ),
			'dispatch-countdown-admin'
		);

		add_settings_field(
			'blacklist',
			'Product IDs',
			array( $this, 'blacklist_callback' ),
			'dispatch-countdown-admin',
			'dispatch_countdown_product_blacklist'
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @since    1.0.0
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( $input ) {

		$new_input = array();

		foreach ( $input as $key => $value ) {
			$new_input[ $key ] = sanitize_text_field( $value );
		}

		return $new_input;

	}

	/**
	 * Print time settings info text
	 *
	 * @since    1.0.0
	 */
	public function time_settings_info() {

		echo 'Set the times the banner should show. Note that the end time is
		used to calculate the time remaining, and the start time is when the
		banner starts showing.<br>E.g. 1200-1600';

	}

	/**
	 * Print blacklist info text
	 *
	 * @since    1.0.0
	 */
	public function product_blacklist_info() {

		echo 'List of product IDs to blacklist. These products will not allow
		the banner to show, regardless of stock/status.<br>E.g. 363,221,125';

	}

	/**
	 * Enabled settings field callback
	 *
	 * @since    1.0.0
	 */
	public function enabled_callback() {

		$option_value = isset( $this->options['enabled'] ) && $this->options['enabled'] ? 1 : 0;

		echo '<input type="checkbox" id="enabled" name="dispatch_countdown_options[enabled]" value="1" ' . checked( 1, $option_value, false ) . ' />';

	}

	/**
	 * Wording settings field callback
	 *
	 * @since    1.0.0
	 */
	public function wording_callback() {

		$option_value = isset( $this->options['wording'] ) ? esc_attr( $this->options['wording'] ) : '';

		echo '<textarea id="wording" name="dispatch_countdown_options[wording]">' . esc_html( $option_value ) . '</textarea>';

	}

	/**
	 * Day settings field callback
	 *
	 * @since    1.0.0
	 * @param string $option_id option field ID such as monday.
	 */
	public function day_callback( $option_id ) {

		$option_value = isset( $this->options[ $option_id ] ) ? esc_attr( $this->options[ $option_id ] ) : '';

		echo '<input type="text" id="' . esc_attr( $option_id ) . '" name="dispatch_countdown_options[' . esc_attr( $option_id ) . ']" value="' . esc_attr( $option_value ) . '" />';

	}

	/**
	 * Blacklist field callback
	 *
	 * @since    1.0.0
	 */
	public function blacklist_callback() {

		$option_value = isset( $this->options['blacklist'] ) ? esc_attr( $this->options['blacklist'] ) : '';

		echo '<input type="text" id="blacklist" name="dispatch_countdown_options[blacklist]" value="' . esc_attr( $option_value ) . '" />';

	}

}
