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
	 * Outputs the Manage Avatars interface
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
	 * @param \WC_Order $order  WooCommerce Order object.
	 */
	public function order( $order ) {

		// TODO: Display the user's avatar for the current order.

	}


	/**
	 * Gets the Frontend Manage Avatars Interface
	 *
	 * @return string  HTML for Manage Avatars (Frontend)
	 */
	private function frontend() {

		// TODO: Display Frontend Manage Avatars Interface

		return '';

	}


	/**
	 * Gets the Admin Manage Avatars Interface
	 *
	 * @return string  HTML for Manage Avatars (Admin)
	 */
	private function admin() {

		// TODO: Display Admin Manage Avatars Interface

		return '';

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
