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

	// Load necessary core classes.
	WooCommerce_Avatar_Discounts_Loader::load_class( 'Core', 'class-core' );
	// TODO: Load Globals\Avatars globals/class-avatars.
	// TODO: Load Frontend\Profile frontend/class-profile.
	// TODO: Load Frontend\Checkout frontend/class-checkout.
	// TODO: Load Frontend\Orders frontend/class-orders.
	// TODO: Load Admin\Users admin/class-users.
	// TODO: Load Admin\Settings admin/class-settings.
	// TODO: Load Admin\Orders admin/class-orders.

	return \WooCommerceAvatarDiscounts\Core::instance();
}

