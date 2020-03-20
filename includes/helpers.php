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

	$classes = array(
		// Global classes.
		'Globals\\Avatars' => 'globals/class-avatars',

		// Rest API classes.
		'API\\Avatars' =>  'api/class-avatars',

		// Frontend classes.
		'Frontend\\Profile'  => 'frontend/class-profile',
		'Frontend\\Checkout' => 'frontend/class-checkout',
		'Frontend\\Orders'   => 'frontend/class-orders',

		// Admin classes.
		'Admin\\Users'    => 'admin/class-users',
		'Admin\\Settings' => 'admin/class-settings',
		'Admin\\Orders'   => 'admin/class-orders',

		// The core Plugin class.
		'Core' => 'class-core',
	);

	foreach ( $classes as $class_name => $class_path ) {
		WooCommerce_Avatar_Discounts_Loader::load_class( $class_name, $class_path );
	}

	return \WooCommerceAvatarDiscounts\Core::instance();
}

