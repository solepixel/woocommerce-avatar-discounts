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

namespace WooCommerceAvatarDiscounts\Admin;

defined( 'ABSPATH' ) or exit;


/**
 * The Admin Orders class.
 *
 * @since 1.0.0
 */
class Orders {

	/** @var Orders class instance */
	protected static $instance;


	/**
	 * Admin Orders hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// TODO: Hook into WooCommerce > Orders > Detail, Display user profile photo at time of purchase in Order details box, below Billing/Shipping, (Maybe) next to Email/Phone.

	}


	/**
	 * Gets the singleton instance of the admin orders class.
	 *
	 * @since 1.0.0
	 *
	 * @return Orders
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
