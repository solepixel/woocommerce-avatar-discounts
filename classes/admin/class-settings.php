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
 * The Admin Settings class.
 *
 * @since 1.0.0
 */
class Settings {

	/** @var Settings class instance */
	protected static $instance;


	/**
	 * Admin Settings hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/** Bail early if we're not in the admin. */
		if ( ! is_admin() ) {
			return;
		}

		/** Modify WooCommerce > Settings > Accounts & Privacy, Add Section for User Avatars. */
		add_filter( 'woocommerce_account_settings', array( $this, 'insert_avatar_settings' ) );

	}


	/**
	 * Modify Account Settings array to add new section for User Avatars.
	 *
	 * @param array $account_settings  Account Settings array.
	 *
	 * @return array  Modified account settings array.
	 */
	public function insert_avatar_settings( $account_settings ) {

		// TODO: Insert section for User Avatars.
		// TODO: Insert setting to limit number of allowed user profile photos.
		// TODO: Insert setting to add text to encourage customers to get their Avatar Discount.

		return $account_settings;
	}


	/**
	 * Gets the singleton instance of the admin settings class.
	 *
	 * @since 1.0.0
	 *
	 * @return Settings
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
