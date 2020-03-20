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
	 */
	public function manage( $interface = 'frontend' ) {
		if ( 'frontend' === $interface ) {
			$this->frontend();
		} elseif ( 'admin' === $interface ) {
			$this->admin();
		}
	}


	/**
	 * Display the avatar used at time of order.
	 */
	public function order() {

		// TODO: Display the user's avatar for the current order.

	}


	/**
	 * Displays the Frontend Manage Avatars Interface
	 */
	private function frontend() {

		// TODO: Display Frontend Manage Avatars Interface

	}


	/**
	 * Displays the Admin Manage Avatars Interface
	 */
	private function admin() {

		// TODO: Display Admin Manage Avatars Interface

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
