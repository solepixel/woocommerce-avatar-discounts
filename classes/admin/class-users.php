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

		/** Insert Manage Avatars inteface where user avatar is displayed */
		add_filter( 'get_avatar', array( $this, 'manage_avatars' ), 10, 6 );

		/** Process User profile save. */
		add_action( 'profile_update', array( $this, 'process_profile_update' ), 10, 2 );

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

		// Make sure we can get the current screen.
		if ( ! did_action( 'user_edit_form_tag' ) ) {
			return $avatar;
		}

		// Make sure we're only changing the Avatar on the Edit User screen.
		$screen = get_current_screen();
		if ( ! $screen ) {
			return $avatar;
		}

		if ( empty( $screen->id ) || ! in_array( $screen->id, array( 'profile', 'user-edit' ), true )  ) {
			return $avatar;
		}

		// Check permission if current user can modify active user.
		if ( ! current_user_can( 'edit_users' ) ) {
			return $avatar;
		}

		// Run only once.
		remove_filter( 'get_avatar', array( $this, 'manage_avatars' ), 10, 6 );

		woocommerce_avatar_discounts()->avatars()->set_original( $avatar );
		woocommerce_avatar_discounts()->avatars()->set_original_args( $args );

		$user_id = ! empty( $_GET['user_id'] ) ? (int) sanitize_text_field( $_GET['user_id'] ) : get_current_user_id();

		return woocommerce_avatar_discounts()->avatars()->manage( $user_id, false, 'admin' );

	}


	/**
	 * Save avatar information when user profile is updated.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id  The edited user's ID.
	 * @param array $old_user_data  Array of old user data.
	 */
	public function process_profile_update( $user_id, $old_user_data ) {
		// Only do this once.
		remove_action( 'profile_update', array( $this, 'process_profile_update' ), 10, 2 );

		woocommerce_avatar_discounts()->avatars()->handle_avatar_upload( $user_id );
		woocommerce_avatar_discounts()->avatars()->handle_featured_avatar( $user_id );
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
