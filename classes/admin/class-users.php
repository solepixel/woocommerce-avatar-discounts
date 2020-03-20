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
 * The Admin Users class.
 *
 * @since 1.0.0
 */
class Users {

	/** @var Users class instance */
	protected static $instance;


	/**
	 * Admin Users hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/** Bail early if we're not in the admin. */
		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'get_avatar', array( $this, 'manage_avatars' ), 10, 6 );

	}


	/**
	 * Get Manage Avatars Interface
	 *
	 * @since 1.0.0
	 *
	 * @param string $avatar      &lt;img&gt; tag for the user's avatar.
	 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
	 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @param int    $size        Square avatar width and height in pixels to retrieve.
	 * @param string $default     URL for the default image or a default type. Accepts '404', 'retro', 'monsterid',
	 *                            'wavatar', 'indenticon','mystery' (or 'mm', or 'mysteryman'), 'blank', or 'gravatar_default'.
	 *                            Default is the value of the 'avatar_default' option, with a fallback of 'mystery'.
	 * @param string $alt         Alternative text to use in the avatar image tag. Default empty.
	 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
	 *
	 * @return string  HTML for manage avatars interface.
	 */
	public function manage_avatars( $avatar, $id_or_email, $size, $default, $alt, $args ) {

		// TODO: Check permission if current user can modify active user.

		woocommerce_avatar_discounts()->avatars()->set_original( $avatar );
		woocommerce_avatar_discounts()->avatars()->set_original_args( $args );

		return woocommerce_avatar_discounts()->avatars()->manage( false, 'admin' );
	}


	/**
	 * Gets the singleton instance of the admin users class.
	 *
	 * @since 1.0.0
	 *
	 * @return Users
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
