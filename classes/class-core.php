<?php
/**
 * WooCommerce Avatar Discounts
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@woocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Avatar Discounts
 *  to newer versions in the future.
 *
 * @author  Brian DiChiara
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace WooCommerceAvatarDiscounts;

defined( 'ABSPATH' ) or exit;

use \WooCommerce_Avatar_Discounts_Loader as Loader;

use WooCommerceAvatarDiscounts\Globals\Avatars;
use WooCommerceAvatarDiscounts\API\Avatars as Avatars_API;
use WooCommerceAvatarDiscounts\Frontend\Profile;
use WooCommerceAvatarDiscounts\Frontend\Checkout;
use WooCommerceAvatarDiscounts\Frontend\Orders as Frontend_Orders;
use WooCommerceAvatarDiscounts\Admin\Users;
use WooCommerceAvatarDiscounts\Admin\Settings;
use WooCommerceAvatarDiscounts\Admin\Orders as Admin_Orders;

/**
 * The main plugin class.
 *
 * @since 1.0.0
 */
class Core {


	/** plugin version number */
	const VERSION = '1.0.0';

	/** plugin ID */
	const PLUGIN_ID = 'woocommerce-avatar-discounts';


	/** @var Globals\Avatars Avatars instance */
	private $avatars_handler;

	/** @var API\Avatars Rest API instance for Avatars */
	private $api_avatars_handler;

	/** @var Frontend\Profile Profile instance */
	private $frontend_profile_handler;

	/** @var Frontend\Checkout Checkout instance */
	private $frontend_checkout_handler;

	/** @var Frontend\Orders Orders instance */
	private $frontend_orders_handler;

	/** @var Admin\Users Users instance */
	private $admin_users_handler;

	/** @var Admin\Settings Settings instance */
	private $admin_settings_handler;

	/** @var Admin\Orders Orders instance */
	private $admin_orders_handler;


	/** @var Core plugin instance */
	protected static $instance;


	/**
	 * Constructs the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/**
		 * Fires upon plugin loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'woocommerce_avatar_discounts_loaded' );

		/** Init the plugin */
		$this->init();

		/**
		 * Fires upon plugin initialized.
		 *
		 * @since 1.0.0
		 */
		do_action( 'woocommerce_avatar_discounts_initialized' );

	}


	/**
	 * Initializes the general plugin functionality.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Init core functionality.
		$this->avatars_handler = new Avatars();

		$this->api_avatars_handler = new Avatars_API();

		$this->frontend_profile_handler  = new Profile();
		$this->frontend_checkout_handler = new Checkout();
		$this->frontend_orders_handler   = new Frontend_Orders();

		$this->admin_users_handler    = new Users();
		$this->admin_settings_handler = new Settings();
		$this->admin_orders_handler   = new Admin_Orders();

		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

	}


	/**
	 * Register CSS/JS Assets.
	 */
	public function assets() {

		$js_file = 'assets/js/manage-avatars.js';
		$js_url  = Loader::get_plugin_url() . $js_file;
		$js_path = Loader::get_plugin_path() . $js_file;

		if ( file_exists( $js_path ) ) {
			$version = filemtime( $js_path );
			wp_register_script( self::PLUGIN_ID . '-manage-avatars', $js_url, array( 'jquery' ), $version, true );
		}

	}


	/**
	 * Enqueue a JS file.
	 *
	 * @param string $script_name  Script name.
	 */
	public function enqueue_script( $script_name ) {
		wp_enqueue_script( self::PLUGIN_ID . '-' . $script_name );
	}


	/**
	 * Gets the Global Avatars instance.
	 *
	 * @return Globals\Avatars
	 */
	public function avatars() {

		return $this->avatars_handler;

	}


	/**
	 * Gets the Frontend Profile instance.
	 *
	 * @return Frontend\Profile
	 */
	public function frontend_profile() {

		return $this->frontend_profile_handler;

	}


	/**
	 * Gets the Frontend Checkout instance.
	 *
	 * @return Frontend\Checkout
	 */
	public function frontend_checkout() {

		return $this->frontend_checkout_handler;

	}


	/**
	 * Gets the Frontend Orders instance.
	 *
	 * @return Frontend\Orders
	 */
	public function frontend_orders() {

		return $this->frontend_orders_handler;

	}


	/**
	 * Gets the Admin Users instance.
	 *
	 * @return Admin\Users
	 */
	public function admin_users() {

		return $this->admin_users_handler;

	}


	/**
	 * Gets the Admin Settings instance.
	 *
	 * @return Admin\Settings
	 */
	public function admin_settings() {

		return $this->admin_settings_handler;

	}


	/**
	 * Gets the Admin Orders instance.
	 *
	 * @return Admin\Orders
	 */
	public function admin_orders() {

		return $this->admin_orders_handler;

	}


	/**
	 * Gets the singleton instance of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Core
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
