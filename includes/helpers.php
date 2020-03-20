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

defined( 'ABSPATH' ) or exit;


/**
 * Gets the singleton instance of WooCommerce Avatar Discounts.
 *
 * @since 1.0.0
 *
 * return \WooCommerceAvatarDiscounts\Core
 */
function woocommerce_avatar_discounts() {

	// Global classes.
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Globals\\Avatars', 'globals/class-avatars' );

	// Rest API classes.
	WooCommerce_Avatar_Discounts_Loader::load_class( 'API\\Avatars', 'api/class-avatars' );

	// Frontend classes.
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Frontend\\Profile', 'frontend/class-profile' );
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Frontend\\Checkout', 'frontend/class-checkout' );
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Frontend\\Orders', 'frontend/class-orders' );

	// Admin classes.
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Admin\\Users', 'admin/class-users' );
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Admin\\Settings', 'admin/class-settings' );
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Admin\\Orders', 'admin/class-orders' );

	// Load the core Plugin class.
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Core', 'class-core' );

	return \WooCommerceAvatarDiscounts\Core::instance();
}

