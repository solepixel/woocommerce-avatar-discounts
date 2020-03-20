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

namespace WooCommerceAvatarDiscounts\Frontend;

defined( 'ABSPATH' ) or exit;


/**
 * The Frontend Checkout class.
 *
 * @since 1.0.0
 */
class Checkout {

	/** @var Checkout class instance */
	protected static $instance;


	/**
	 * Frontend checkout hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/** Order processed hook to store current user profile photo. */
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_user_avatar' ), 10, 3 );

		/** Display Manage Avatars interface at Checkout below Order Notes */
		add_action( 'woocommerce_after_order_notes', array( $this, 'manage_avatars' ) );

		/** Show Order Avatar on Order Confirmation page */
		add_action( 'woocommerce_thankyou', array( $this, 'show_avatar' ), 11 );

	}


	/**
	 * Save current user avatar into order meta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id  The order ID.
	 * @param array $posted_data  Posted data array.
	 * @param \WC_Order $order  WooCommerce Order object
	 */
	public function save_user_avatar( $order_id, $posted_data, $order ) {

		// TODO: Save user avatar.

	}


	/**
	 * Manage Avatar under Checkout, below Order Notes
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Checkout $checkout  WooCommerce Checkout object
	 */
	public function manage_avatars( $checkout ) {

		woocommerce_avatar_discounts()->avatars()->manage();

	}


	/**
	 * Show customer avatar on Order Confirmation page.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Order $order  WooCommerce Order object.
	 */
	public function show_avatar( $order = false ) {

		woocommerce_avatar_discounts()->avatars()->order( $order );

	}


	/**
	 * Gets the singleton instance of the frontend checkout class.
	 *
	 * @since 1.0.0
	 *
	 * @return Checkout
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
