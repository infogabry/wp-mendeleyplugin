<?php
/**
 * Collab Mendeley Plugin
 *
 * @package   CollabMendeleyPlugin
 * @author    Davide Parisi <davideparisi@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 --
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-collab-mendeley-plugin-admin.php`
 *
 * @package CollabMendeleyPlugin
 * @author  Davide Parisi <davideparisi@gmail.com>
 *
 */

/*if ( ! class_exists( 'MendeleyApi' ) ) {
	require_once plugin_dir_path( __DIR__ ) . "includes/class-mendeley-api.php";
}*/

date_default_timezone_set( get_option( 'timezone_string' ) != '' ? get_option( 'timezone_string' ) : 'Europe/Rome' );

class CollabMendeleyPlugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'collab-mendeley-plugin';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'register_shortcode' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		//todo: define single activation functionality
		//do_action('activate_plugin', 'wp-mendeleyplugin/collab-mendeley-plugin.php', false);
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
		//do_action('deactivate_plugin', 'wp-mendeleyplugin/collab-mendeley-plugin.php', false);
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	/*----------------------------------------------------------------------------/
	 *
	 * Process Shortcode
	 *
	 *---------------------------------------------------------------------------*/
	public function register_shortcode() {
		add_shortcode( 'mendeley', array( $this, 'authored_publications' ) );
	}

	public function authored_publications( $atts, $content = null ) {
		$options = $this->get_options();
		if ( false == $options ) { // if cannot get options
			return; // exit and do nothing
		}

		$tag = $atts['tag'];
		if ( isset( $tag ) ) {
			$return_string = '<' . $tag . '>' . $content . '</' . $tag . '>';
			$return_string .= '<br/>';
		}


		$token_data_array = $options['access_token']['result'];
		if ( ! isset( $token_data_array ) ) {
			//@todo: perhaps returning an empty string should be better...
			return "you must set up mendeley plugin before using his shortcodes...";
		}
		$token = $token_data_array['access_token'];

		$client = MendeleyApi::get_instance();
		$client->set_up(
			$options['client_id'],
			$options['client_secret'],
			admin_url( 'options-general.php?page=' . $this->plugin_slug )
		);

		if ( time() > $options['expire_time'] ) {
			$response                = $client->refresh_access_token( $token_data_array['refresh_token'] );
			$options['access_token'] = $response;
			$this->update_options( $options );

			if ( ! $response['code'] == 200 ) { // if there is a problem with the response
				// @FIXME: Manage this situation
				return ''; // return a void string and do no harm...
			}

			$token_data = $response['result'];
			$token      = $token_data['access_token'];
		}

		$client->set_client_access_token( $token );
		$publications = $client->get_authored_publications();
		$formatted    = DocumentFormatter::format( $publications );

		$return_string .= $formatted;

		return $return_string;
	}

	/*----------------------------------------------------------------------------/
	 *
	 * Utilities
	 *
	 *---------------------------------------------------------------------------*/

	private function get_options() {
		// $opts = array();
		/*if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$opts = get_site_option( $this->plugin_slug );
		} else {
			$opts = get_option( $this->plugin_slug );
		}*/
		$opts = get_option( $this->plugin_slug );

		return $opts;
	}

	private function update_options( $options ) {
		/*if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			update_site_option( $this->plugin_slug, $options );
		} else {
			update_option( $this->plugin_slug, $options );
		}*/
		update_option( $this->plugin_slug, $options );
	}
}
