<?php
/**
 * Plugin Name: WooCommerce Avatar Discounts
 * Version: 1.0.0
 * Plugin URI: https://github.com/solepixel/woocommerce-avatar-discounts
 * Description: Provide discounts to users based on their profile photo.
 * Author: Brian DiChiara
 * Author URI: https://www.briandichiara.com/
 * Text Domain: woocommerce-avatar-discounts
 * Domain Path: /lang/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author  Brian DiChiara
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * WC requires at least: 3.0.9
 * WC tested up to: 3.8.1
 */

defined( 'ABSPATH' ) or exit;


/**
 * The plugin loader class.
 *
 * @since 1.0.0
 */
class WooCommerce_Avatar_Discounts_Loader {

	/** minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '5.6.0';

	/** minimum WordPress version required by this plugin */
	const MINIMUM_WP_VERSION = '4.4';

	/** minimum WooCommerce version required by this plugin */
	const MINIMUM_WC_VERSION = '3.0.9';

	/** Name of the plugin */
	const PLUGIN_NAME = 'WooCommerce Avatar Discounts';


	/** Path to this plugin directory */
	private static $plugin_path;

	/** URL to this plugin directory */
	private static $plugin_url;

	/** @var WooCommerce_Avatar_Discounts_Loader single instance of this class */
	private static $instance;

	/** @var array the admin notices to add */
	private $notices = array();


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {

		self::$plugin_path = plugin_dir_path( __FILE__ );
		self::$plugin_url  = plugin_dir_url( __FILE__ );

		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init', array( $this, 'check_environment' ) );
		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		// Init plugin after WooCommerce is initialized.
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ), 11 );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init_plugin() {
		// Only init this plugin if environment is compatible.
		if ( ! $this->is_environment_compatible() ) {
			return;
		}

		$this->includes();

		// Setup Debugger.
		$core      = \WooCommerceAvatarDiscounts\Core::instance();
		$plugin_id = $core->get_plugin_id();

		$debug = \WooCommerceAvatarDiscounts\Debug::instance();
		$debug->set_plugin_slug( $plugin_id );

		// Need to setup Database table before loading everything else.
		$this->database_delta();

		// load required helper functions.
		require_once( plugin_dir_path( __FILE__ ) . 'includes/helpers.php' );

		// away we go!
		woocommerce_avatar_discounts();
	}


	/**
	 * Load necessary classes
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		$classes = array(
			// Global classes.
			'Globals\\Avatars' => 'globals/class-avatars',

			// Debugger class.
			'Debug' => 'class-debug',

			// DB classes.
			'Db\\Core'             => 'db/class-core',
			'Db\\Table'            => 'db/class-table',
			'Db\\Customer_Avatars' => 'db/class-customer-avatars',

			// Rest API classes.
			'API\\Avatars' =>  'api/class-avatars',

			// Frontend classes.
			'Frontend\\Profile'  => 'frontend/class-profile',
			'Frontend\\Checkout' => 'frontend/class-checkout',
			'Frontend\\Orders'   => 'frontend/class-orders',

			// Admin classes.
			'Admin\\Users'    => 'admin/class-users',
			'Admin\\Settings' => 'admin/class-settings',
			'Admin\\Orders'   => 'admin/class-orders',

			// The core Plugin class.
			'Core' => 'class-core',
		);

		self::autoload( $classes );

	}


	/**
	 * Trigger Database Deltas.
	 *
	 * @since 1.0.0
	 */
	public function database_delta() {

		if ( ! function_exists( 'woocommerce_avatar_discounts' ) ) {
			$settings        = \WooCommerceAvatarDiscounts\Admin\Settings::instance();
			$settings_prefix = $settings->get_prefix();

			$core         = \WooCommerceAvatarDiscounts\Core::instance();
			$table_prefix = $core->get_table_prefix();
		} else {
			$settings_prefix = woocommerce_avatar_discounts()->admin_settings()->get_prefix();
			$table_prefix    = woocommerce_avatar_discounts()->get_table_prefix();
		}

		// Init database deltas.
		$db = \WooCommerceAvatarDiscounts\Db\Core::instance();
		$db->set_setting_prefix( $settings_prefix );
		$db->set_table_prefix( $table_prefix );

		$avatars = \WooCommerceAvatarDiscounts\Globals\Avatars::instance();

		$db->add_table( $avatars->get_table_schema() );

		// Go!
		$db->delta();

	}


	/**
	 * Auto load array of classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes  Array of classes and file names.
	 */
	public static function autoload( $classes ) {
		foreach ( $classes as $class_name => $class_path ) {
			self::load_class( $class_name, $class_path );
		}
	}


	/**
	 * Load a class file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name  Name of class
	 * @param string $file_path   Path/Name of class file relative to the classes folder
	 */
	public static function load_class( $class_name, $file_path ) {
		if ( ! class_exists( '\\WooCommerceAvatarDiscounts\\' . $class_name ) ) {
			$class_path = self::$plugin_path . 'classes/' . $file_path . '.php';

			if ( ! file_exists( $class_path ) ) {
				die( 'Unable to load ' . self::PLUGIN_NAME . ' ' . $class_name . ' class (' . $file_path . ').' );
			}

			require_once( $class_path );
		}
	}


	/**
	 * Get the path to a view.
	 *
	 * @since 1.0.0
	 *
	 * @param string $view  View file.
	 *
	 * @return string|false  Path to view or false if doesn't exist.
	 */
	public static function locate_view( $view ) {

		$view_path     = self::$plugin_path . 'views/' . $view . '.php';
		$view_override = locate_template( array( 'woocommerce-avatar-discounts/' . $view . '.php', str_replace( '/', '-', 'woocommerce-avatar-discounts-' . $view . '.php' ) ) );

		if ( $view_override ) {
			$view_path = $view_override;
		}

		if ( ! file_exists( apply_filters( 'woocommerce_avatar_discounts_view', $view_path ) ) ) {
			return false;
		}

		return $view_path;

	}


	/**
	 * Display or return a view file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $_view  View to load.
	 * @param array  $vars   Array of variables to pass to view.
	 * @param bool   $echo   Echo or return.
	 *
	 * @return string|null|false  String if returning view, false on error, otherwise null.
	 */
	public static function load_view( $_view, $vars = array(), $echo = true ) {

		$_view_path = self::locate_view( $_view );

		if ( false === $_view_path ) {
			return false;
		}

		if ( ! is_array( $vars ) ) {
			$vars = array(); // Must be an array. Use compact.
		}

		if ( count( $vars ) ) {
			extract( $vars );
		}

		ob_start();
		include $_view_path;
		$output = ob_get_clean();

		if ( false === $echo ) {
			return $output;
		}

		echo $output;

	}


	/**
	 * Return a view file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $_view  View to load.
	 * @param array  $vars   Array of variables to pass to view.
	 *
	 * @return string|false  String if returning view, false on error.
	 */
	public static function get_view( $_view, $vars = array() ) {

		return self::load_view( $_view, $vars, false );

	}


	/**
	 * Get Plugin Path
	 *
	 * @since 1.0.0
	 *
	 * @return string  Path to plugin folder.
	 */
	public static function get_plugin_path() {
		return self::$plugin_path;
	}


	/**
	 * Get Plugin URL
	 *
	 * @since 1.0.0
	 *
	 * @return string  URL to plugin folder.
	 */
	public static function get_plugin_url() {
		return self::$plugin_url;
	}


	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 *
	 * @since 1.0.0
	 */
	public function activation_check() {

		if ( ! $this->is_environment_compatible() ) {

			$this->deactivate_plugin();

			wp_die( self::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @since 1.0.0
	 */
	public function check_environment() {

		if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();

			$this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Deactivates the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}


	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug the slug for the notice
	 * @param string $class the css class for the notice
	 * @param string $message the notice message
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message
		);
	}


	/**
	 * Displays any admin notices added with \self::add_admin_notice()
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {

		foreach ( (array) $this->notices as $notice_key => $notice ) {

			?>
			<div class="<?php echo esc_attr( $notice['class'] ); ?>">
				<p>
					<?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?>
				</p>
			</div>
			<?php
		}
	}


	/**
	 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_notices() {

		if ( ! $this->is_wp_compatible() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
				'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WP_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}

		if ( ! $this->is_wc_compatible() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', sprintf(
				'%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WC_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
				'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . self::MINIMUM_WC_VERSION . '.zip' ) . '">', '</a>'
			) );
		}
	}


	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function plugins_compatible() {

		return $this->is_wp_compatible() && $this->is_wc_compatible();
	}


	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_wp_compatible() {

		if ( ! self::MINIMUM_WP_VERSION ) {
			return true;
		}

		return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
	}


	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_wc_compatible() {

		if ( ! self::MINIMUM_WC_VERSION ) {
			return true;
		}

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
	}


	/**
	 * Checks to make sure the minimum required PHP, WP and WC version is available.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_environment_compatible() {
		if ( ! version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' ) ) {
			return false;
		}

		if ( ! $this->is_wp_compatible() ) {
			return false;
		}

		if ( ! $this->is_wc_compatible() ) {
			return false;
		}

		return true;
	}


	/**
	 * Gets the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_environment_message() {

		$message = sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );

		return $message;
	}


	/**
	 * Gets the main plugin loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return \WooCommerce_Avatar_Discounts_Loader
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}

// fire it up!
WooCommerce_Avatar_Discounts_Loader::instance();
