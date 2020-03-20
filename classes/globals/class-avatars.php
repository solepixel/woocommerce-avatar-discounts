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

namespace WooCommerceAvatarDiscounts\Globals;

defined( 'ABSPATH' ) or exit;


/**
 * The Avatars class.
 *
 * @since 1.0.0
 */
class Avatars {

	/** @var string Original Avatar image HTML */
	private $original;

	/** @var array Original Avatar args */
	private $original_args;

	/** @var WC_Order WC_Order object */
	private $order;

	/** @var Avatars class instance */
	protected static $instance;


	/**
	 * Avatars init.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Probably do nothing here.

	}


	/**
	 * Sets the original img from `get_avatar`.
	 *
	 * @since 1.0.0
	 *
	 * @param string $original  Original avatar image.
	 */
	public function set_original( $original ) {
		$this->original = $original;
	}


	/**
	 * Sets the original args from `get_avatar`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args  Array of args.
	 */
	public function set_original_args( $args ) {
		$this->original_args = $args;
	}


	/**
	 * Outputs the Manage Avatars interface
	 *
	 * @since 1.0.0
	 *
	 * @param string $interface  Either frontend or admin.
	 *
	 * @return string|null  HTML for manage avatars.
	 */
	public function manage( $echo = true, $interface = 'frontend' ) {
		$output = '';

		if ( 'frontend' === $interface ) {
			$output = $this->frontend();
		} elseif ( 'admin' === $interface ) {
			$output = $this->admin();
		}

		if ( true !== $echo ) {
			return $output;
		}

		echo $output;
	}


	/**
	 * Display the avatar used at time of order.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Order $order  WooCommerce Order object.
	 */
	public function order( $order = false ) {

		if ( $order ) {
			$this->order = $order;
		}

		\WooCommerce_Avatar_Discounts_Loader::load_view( 'order-avatar' );

	}


	/**
	 * Gets the Frontend Manage Avatars Interface
	 *
	 * @since 1.0.0
	 *
	 * @return string  HTML for Manage Avatars (Frontend)
	 */
	private function frontend() {

		// TODO: Display Frontend Manage Avatars Interface

		return \WooCommerce_Avatar_Discounts_Loader::get_view( 'manage-avatars' );

	}


	/**
	 * Gets the Admin Manage Avatars Interface
	 *
	 * @since 1.0.0
	 *
	 * @return string  HTML for Manage Avatars (Admin)
	 */
	private function admin() {

		// TODO: Display Admin Manage Avatars Interface
		$return = '';
		if ( $this->original ) {
			$return = $this->original;
		}

		return \WooCommerce_Avatar_Discounts_Loader::get_view( 'manage-avatars', compact( 'return' ) );

	}


	/**
	 * Gets the singleton instance of the avatars class.
	 *
	 * @since 1.0.0
	 *
	 * @return Avatars
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
