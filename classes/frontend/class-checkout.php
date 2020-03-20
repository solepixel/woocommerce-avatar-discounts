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

		/** Save uploads before order processed */
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'process_checkout' ), 10, 2 );

		/** Order processed hook to store current user profile photo. */
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_user_avatar' ), 10, 3 );

		/** Display Manage Avatars interface at Checkout below Order Notes */
		add_action( 'woocommerce_after_order_notes', array( $this, 'manage_avatars' ) );

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

		$avatar_id = false;

		// First check for Posted Avatar ID.
		if ( ! empty( $posted_data['woocommerce_avatar_discounts_avatar'] ) ) {
			$avatar_id = (int) $posted_data['woocommerce_avatar_discounts_avatar'];

			// Make sure avatar belongs to this user.
			if ( ! $avatar_id || ! woocommerce_avatar_discounts()->avatars()->validate( $avatar_id ) ) {
				return;
			}
		}

		if ( ! $avatar_id ) {
			// Now check for active avatar in DB.
			$current_avatar = woocommerce_avatar_discounts()->avatars()->get_current_avatar();
			if ( $current_avatar ) {
				$avatar_id = $current_avatar->id;
			}
		}

		if ( ! $avatar_id ) {
			return;
		}

		update_post_meta( $order_id, woocommerce_avatar_discounts()->avatars()->get_avatar_meta_key(), $avatar_id );

	}


	/**
	 * Save Upload at checkout.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $data        Order Data.
	 * @param \WP_error $errors  WP Error object.
	 */
	public function process_checkout( $data, $errors ) {

		$user_id = get_current_user_id();

		woocommerce_avatar_discounts()->avatars()->handle_featured_avatar( $user_id, $errors );
		woocommerce_avatar_discounts()->avatars()->handle_avatar_upload( $user_id, $errors );

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
