<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       mardell.me
 * @since      1.0.0
 *
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.7 Remove loader in favour of native WP add_action/add_filter
 * @since      1.0.0
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/includes
 * @author     Andy Mardell <mardell.me>
 */
class Dispatch_Countdown {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current instance of the public class
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var object|Dispatch_Countdown_Public
	 */
	protected static $public_instance;

	/**
	 * The current instance of the admin class
	 *
	 * @since 1.0.7
	 * @access protected
	 * @var object|Dispatch_Countdown_Admin
	 */
	protected static $admin_instance;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the
	 * plugin and load dependencies.
	 *
	 * @since    1.0.7 Run locale and hooks from $this->run()
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version     = defined( 'DISPATCH_COUNTDOWN_VERSION' ) ? DISPATCH_COUNTDOWN_VERSION : '1.0.0';
		$this->plugin_name = 'dispatch-countdown';

		$this->load_dependencies();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.7 Remove loader
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// General dependencies.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dispatch-countdown-i18n.php';

		// Admin and public classes.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dispatch-countdown-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-dispatch-countdown-public.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dispatch_Countdown_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.7 Use native WP add_action instead of loader
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Dispatch_Countdown_I18n();

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.7 Use native WP add_action and filter instead of loader
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Dispatch_Countdown_Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_files' ) );
		add_filter( 'woocommerce_get_sections_products', array( $plugin_admin, 'add_settings_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( $plugin_admin, 'settings_page_init' ), 10, 2 );

		self::$admin_instance = $plugin_admin;

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.7 Use native WP add_action instead of loader
	 * @since    1.0.6 Allow hook override by exposing the instance
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		if ( ! get_option( 'dispatch_countdown_enabled' ) ) {
			return false;
		}

		$plugin_public = new Dispatch_Countdown_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_files' ) );
		add_action( 'woocommerce_before_single_product', array( $plugin_public, 'display_countdown' ) );
		add_action( 'wp_ajax_nopriv_get_countdown', array( $plugin_public, 'get_countdown' ) );
		add_action( 'wp_ajax_get_countdown', array( $plugin_public, 'get_countdown' ) );

		self::$public_instance = $plugin_public;

	}

	/**
	 * Execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;

	}

	/**
	 * Get an instance of the public class
	 *
	 * @since 1.0.6 Allow hook override by exposing the instance
	 * @return object|Dispatch_Countdown_Public
	 */
	public static function get_public_instance() {

		return self::$public_instance;

	}

	/**
	 * Get an instance of the admin class
	 *
	 * @since 1.0.7 Allow hook override by exposing the instance
	 * @return object|Dispatch_Countdown_Admin
	 */
	public static function get_admin_instance() {

		return self::$admin_instance;

	}

}
