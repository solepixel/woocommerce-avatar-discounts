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

	/** @var string  Settings prefix */
	private $prefix = 'woocommerce_avatar_discounts_';

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
	 * Get a setting value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $setting  The Setting ID (without the prefix)
	 *
	 * @return string  The setting value.
	 */
	public function get_setting( $setting ) {

		$defaults = $this->defaults();
		$default  = isset( $defaults[ $setting ] ) ? $defaults[ $setting ] : '';
		return get_option( $this->prefix . $setting, $default );

	}


	/**
	 * Plugin settings defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return array  Array of default settings.
	 */
	public function defaults() {

		return array(
			'limit'          => 0,
			'encourage_text' => __( 'Upload your photo to get discounts on your orders!', 'woocommerce-avatar-discounts' ),
		);

	}


	/**
	 * Get Admin Settings array.
	 *
	 * @since 1.0.0
	 *
	 * @return array  Array of settings.
	 */
	public function get_settings() {

		$defaults = $this->defaults();

		return apply_filters(
			'woocommerce_avatar_discounts_settings',
			array(
				array(
					'title' => __( 'User Avatars', 'woocommerce-avatar-discounts' ),
					'type'  => 'title',
					'id'    => 'user_avatar_options',
					'desc'  => __( 'This section controls some of the aspects of Avatar Discounts', 'woocommerce-avatar-discounts' ),
				),

				/** Max Avatars Allowed */
				array(
					'title'            => __( 'Maximum Allowed Avatars', 'woocommerce-avatar-discounts' ),
					'desc_tip'         => __( 'Set a limit on how many avatars your customers can upload to their profile. Zero (0) will allow unlimited photo uploads.', 'woocommerce-avatar-discounts' ),
					'id'               => $this->prefix . 'limit',
					'default'          => $defaults['limit'],
					'type'             => 'number',
					'css'              => 'width: 60px; text-align: center;',
					'autoload'         => false,
					'custom_attributes' => array(
						'min'       => '0',
						'inputmode' => 'numeric',
						'pattern'   => '[0-9]*',
					),
				),

				/** Encouragement Text */
				array(
					'title'    => __( 'Avatar Instruction Text', 'woocommerce-avatar-discounts' ),
					'desc_tip' => __( 'Tell your users why they should upload a photo to their account.', 'woocommerce-avatar-discounts' ),
					'id'       => $this->prefix . 'encourage_text',
					'default'  => $defaults['encourage_text'],
					'type'     => 'textarea',
					'css'      => 'min-width: 50%; height: 75px;',
					'autoload' => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'user_avatar_options',
				),
			)
		);

	}


	/**
	 * Modify Account Settings array to add new section for User Avatars.
	 *
	 * // TODO: Check WC Version Compatibility.
	 *
	 * @since 1.0.0
	 *
	 * @param array $account_settings  Account Settings array.
	 *
	 * @return array  Modified account settings array.
	 */
	public function insert_avatar_settings( $account_settings ) {

		$modified_settings = array();

		foreach ( $account_settings as $setting_array ) {
			// Only insert our settings before the Privacy Policy Options Title.
			if (
				! empty( $setting_array['id'] ) &&
				! empty( $setting_array['type'] ) &&
				'privacy_policy_options' === $setting_array['id'] &&
				'title' === $setting_array['type']
			) {
				foreach ( $this->get_settings() as $plugin_setting ) {
					$modified_settings[] = $plugin_setting;
				}
			}

			$modified_settings[] = $setting_array;

		}

		return $modified_settings;
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
