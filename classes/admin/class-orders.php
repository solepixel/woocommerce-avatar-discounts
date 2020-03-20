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

		/** Bail early if we're not in the admin. */
		if ( ! is_admin() ) {
			return;
		}

		/** Add customer avatar field to Order Details meta box, below Billing/Shipping address */
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'insert_customer_avatar' ) );

	}


	/**
	 * Insert customer avatar into Order Detail meta box.
	 *
	 * @param \WC_Order $order  WooCommerce Order object.
	 */
	public function insert_customer_avatar( $order ) {

		woocommerce_avatar_discounts()->avatars()->order( $order );

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
