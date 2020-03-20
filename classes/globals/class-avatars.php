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

use \WooCommerce_Avatar_Discounts_Loader as Loader;
use \WooCommerceAvatarDiscounts\Db\Customer_Avatars as Customer_Avatars;

/**
 * The Avatars class.
 *
 * @since 1.0.0
 */
class Avatars {

	/** @var \Db\Avatars Avatars database table */
	private $db;

	/** @var string Original Avatar image HTML */
	private $original;

	/** @var array Original Avatar args */
	private $original_args;

	/** @var WC_Order WC_Order object */
	private $order;

	/** @var string Upload error message */
	private $error;

	/** @var Avatars class instance */
	protected static $instance;


	/**
	 * Avatars init.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->db = new Customer_Avatars();
		$thumb    = 'woocommerce-avatar-discounts';

		add_image_size( $thumb, 300, 300, true );

		add_filter( 'woocommerce_edit_account_form_tag', array( $this, 'account_form_attr' ) );
		add_action( 'woocommerce_save_account_details', array( $this, 'save_avatar_upload' ) );

	}


	/**
	 * Get the Avatars table schema.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_table_schema() {

		return $this->db->get_schema();

	}


	/**
	 * Get All Avatars.
	 *
	 * Args can be passed to filter/sort data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args  Array of args.
	 *
	 * @return array  Array of avatars.
	 */
	public function all( $args = array() ) {

		return $this->db->all( $args );

	}


	/**
	 * Add enctype attribute to EditAccountForm
	 */
	public function account_form_attr() {

		echo 'enctype="multipart/form-data"';

	}


	/**
	 * Save new Avatar uploads.
	 *
	 * @param int $user_id  The User ID.
	 */
	public function save_avatar_upload( $user_id ) {
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		$post_data  = array(
			'post_author' => $user_id,
		);
		$attachment = media_handle_upload( 'woocommerce_avatar_discounts_upload', 0, $post_data );
		if ( is_wp_error( $attachment ) ) {
			$this->error = $attachment->get_error_message();
			return false;
		}

		$avatar = $this->get_current_avatar();
		$status = $avatar ? 'active' : 'featured';
		$data   = array(
			'user_id'       => $user_id,
			'attachment_id' => $attachment,
			'status'        => $status,
		);
		$this->db->save( $data );
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
	 * Get Current Users Avatars.
	 *
	 * @since 1.0.0
	 *
	 * @return array  Array of Avatars.
	 */
	public function get_user_avatars() {

		$args = array(
			'user_id' => get_current_user_id(),
		);

		return $this->all( $args );

	}


	/**
	 * Get the current featured avatar for user.
	 *
	 * @return object  Avatar object.
	 */
	public function get_current_avatar() {
		$avatars = $this->get_user_avatars();
		if ( empty( $avatars ) ) {
			return false;
		}
		$current = false;
		foreach ( $avatars as $avatar ) {
			if ( 'featured' === $avatar->status ) {
				$current = $avatar;
			}
		}

		if ( $current ) {
			// Replace URL with smaller thumbnail.
			if ( $current->attachment_id ) {
				$thumb        = 'woocommerce-avatar-discounts';
				$current->url = wp_get_attachment_thumb_url( $current->attachment_id, $thumb );
			}
		}

		return $current;
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
		woocommerce_avatar_discounts()->enqueue_script( 'manage-avatars' );
		woocommerce_avatar_discounts()->enqueue_style( 'avatars' );

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

		woocommerce_avatar_discounts()->enqueue_style( 'avatars' );

		if ( $order ) {
			$this->order = $order;
		}

		$avatars = $this->get_user_avatars();
		$avatar  = $avatars[0];

		Loader::load_view( 'order-avatar', compact( 'avatar' ) );

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
		return $this->manage_avatars();

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
		return $this->manage_avatars();

	}


	/**
	 * Temp function (maybe).
	 *
	 * @since 1.0.0
	 *
	 * @return string  HTML for Manage Avatars.
	 */
	private function manage_avatars() {
		$avatars = $this->get_user_avatars();

		$encourage_text = woocommerce_avatar_discounts()->admin_settings()->get_setting( 'encourage_text' );

		return Loader::get_view( 'manage-avatars', compact( 'avatars', 'encourage_text' ) );
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
