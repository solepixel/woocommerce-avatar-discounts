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

// TODO: Use Frontend Profile class.
// TODO: Use Frontend Checkout class.
// TODO: Use Frontend Orders class.

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

	// TODO: Frontend Profile class instance property.
	// TODO: Frontend Checkout class instance property.
	// TODO: Frontend Orders class instance property.
	// TODO: Admin Users class instance property.
	// TODO: Admin Settings class instance property.
	// TODO: Admin Orders class instance property.

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

		// TODO: Init core functionality:
		//   Frontend Profile class instance
		//   Frontend Checkout class instance
		//   Frontend Orders class instance
		//   Admin Users class instance
		//   Admin Settings class instance
		//   Admin Orders class instance

	}


	// TODO: Frontend Profile Getter.


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
