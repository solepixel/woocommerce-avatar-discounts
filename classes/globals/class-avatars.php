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

		/** Avatar AJAX Upload */
		add_action( 'wp_ajax_wcad_upload_avatar', array( $this, 'ajax_avatar_upload' ) );
		add_action( 'wp_ajax_nopriv_wcad_upload_avatar', array( $this, 'ajax_avatar_upload' ) );

		/** Avatar AJAX Delete */
		add_action( 'wp_ajax_wcad_delete_avatar', array( $this, 'ajax_delete_avatar' ) );
		add_action( 'wp_ajax_nopriv_wcad_delete_avatar', array( $this, 'ajax_delete_avatar' ) );

		/** Avatar Set Featured */
		add_action( 'wp_ajax_wcad_feature_avatar', array( $this, 'ajax_feature_avatar' ) );
		add_action( 'wp_ajax_nopriv_wcad_feature_avatar', array( $this, 'ajax_feature_avatar' ) );

		/** Add multipart form-data to form */
		add_filter( 'woocommerce_edit_account_form_tag', array( $this, 'account_form_attr' ) );

		/** Handle uploads, stop on error */
		add_action( 'woocommerce_save_account_details_errors', array( $this, 'process_account_details' ), 10, 2 );

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

		$avatars = $this->db->all( $args );
		$avatars = $this->add_mime_type_info( $avatars );
		return $avatars;

	}


	/**
	 * Inject any missing mime type information into the results.
	 *
	 * @param array $avatars  Array of Avatars.
	 *
	 * @param array  Updated avatars array
	 */
	public function add_mime_type_info( $avatars ) {
		foreach ( $avatars as &$avatar ) {
			if ( $avatar->mime_type ) {
				continue;
			}
			$image             = $avatar->url;
			$avatar->mime_type = exif_imagetype( $image );
		}
		return $avatars;
	}


	/**
	 * Get a single avatar by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $avatar_id  The avatar ID.
	 * @param int $user_id    The associated User ID. Defaults to current user ID.
	 *
	 * @return object|false  Avatar object or false.
	 */
	public function get( $avatar_id, $user_id = false ) {
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}
		$avatars = $this->all(
			array(
				'user_id' => $user_id,
				'id'      => $avatar_id,
			)
		);

		if ( 1 !== count( $avatars ) ) {
			return false;
		}

		return $avatars[0];
	}


	/**
	 * Add enctype attribute to EditAccountForm
	 *
	 * @since 1.0.0
	 */
	public function account_form_attr() {

		echo 'enctype="multipart/form-data"';

	}


	/**
	 * Save new Avatar uploads and update selected avatar.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id  The User ID.
	 */
	public function process_account_details( &$errors, &$user ) {
		// Only do it once.
		remove_action( 'woocommerce_save_account_details_errors', array( $this, 'process_account_details' ), 10, 2 );

		$this->handle_featured_avatar( $user->ID, $errors );
		$this->handle_avatar_upload( $user->ID, $errors );
	}


	/**
	 * Handle upload of avatar.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id  User ID.
	 * @param \WP_Error $errors   Errors object.
	 */
	public function handle_avatar_upload( $user_id, &$errors = false ) {
		$file_array = isset( $_FILES['woocommerce_avatar_discounts_upload'] ) ? $_FILES['woocommerce_avatar_discounts_upload'] : false;

		if ( empty( $file_array ) || UPLOAD_ERR_NO_FILE === $file_array['error'] ) {
			return;
		}

		$allowed_types = array(
			'image/jpeg',
			'image/png',
			'image/gif',
			'image/tiff',
			'image/bmp',
		);

		if ( ! in_array( $file_array['type'], $allowed_types, true ) ) {
			$errors->add( 'woocommerce-avatar-discounts-invalid-file', __( 'Only images are allowed for your avatar. Note: SVG is not allowed.', 'woocommerce-avatar-discounts' ) );
			return false;
		}

		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		$avatars = $this->get_user_avatars( $user_id, true );
		$count   = count( $avatars );
		$limit   = woocommerce_avatar_discounts()->admin_settings()->get_setting( 'limit' );
		if ( $limit > 0 && $count >= $limit ) {
			$errors->add( 'woocommerce-avatar-discounts-limit-reached', __( 'You have reached the maximum number of avatars', 'woocommerce-avatar-discounts' ) . ': ' . $limit . '.' );
			return false;
		}

		$post_data  = array(
			'post_author' => $user_id,
		);
		$attachment = media_handle_upload( 'woocommerce_avatar_discounts_upload', 0, $post_data );
		if ( is_wp_error( $attachment ) ) {
			$errors->add( 'woocommerce-avatar-discounts-upload-error', __( 'There was an error processing your upload.', 'woocommerce-avatar-discounts' ) . ': ' . $attachment->get_error_message() );
			return false;
		}

		$image  = wp_get_attachment_image_src( $attachment, 'full' );
		$avatar = $this->get_current_avatar( $user_id );
		$status = $avatar ? 'active' : 'featured';
		$data   = array(
			'user_id'       => $user_id,
			'attachment_id' => $attachment,
			'status'        => $status,
			'url'           => $image[0],
		);

		$save = $this->db->save( $data );

		if ( false === $save ) {
			$errors->add( 'woocommerce-avatar-discounts-save-error', __( 'There was an error saving your profile.', 'woocommerce-avatar-discounts' ) . ': ' . $attachment->get_error_message() );
			return false;
		}

		$this->set_featured_avatar( $save );
	}


	/**
	 * Handle switch of featured avatar
	 *
	 * @since 1.0.0
	 *
	 * @param int       $user_id  User ID.
	 * @param \WP_Error $errors   Errors object.
	 */
	public function handle_featured_avatar( $user_id, &$errors = false ) {
		if ( ! empty( $_POST['woocommerce_avatar_discounts_avatar'] ) ) {
			$selected = (int) sanitize_text_field( $_POST['woocommerce_avatar_discounts_avatar'] );

			// Make sure avatar belongs to this user.
			if ( ! $this->validate( $selected, $user_id ) ) {
				$errors->add( 'woocommerce-avatar-discounts-validation-error', __( 'Avatar ID does not match user.', 'woocommerce-avatar-discounts' ) );
				return;
			}

			$this->set_featured_avatar( $selected );
		}
	}


	/**
	 * Set the user's featured avatar photo.
	 *
	 * @since 1.0.0
	 *
	 * @param int $avatar_id  The avatar ID.
	 * @param bool $user_id  The User ID.
	 */
	public function set_featured_avatar( $avatar_id, $user_id = false ) {
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}
		$this->db->update(
			array(
				'status' => 'active',
			),
			array(
				'user_id' => $user_id,
				'status'  => 'featured',
			)
		);
		$this->db->update(
			array(
				'status'   => 'featured',
				'modified' => current_time( 'mysql' ),
			),
			array(
				'user_id' => $user_id,
				'id'      => $avatar_id,
			)
		);
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

		// TODO: Insert Gravatar URL into database (once).
	}


	/**
	 * Get Current Users Avatars.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $user_id  The User ID.
	 * @param bool $active_only  Exclude deleted.
	 *
	 * @return array  Array of Avatars.
	 */
	public function get_user_avatars( $user_id = false, $active_only = false ) {

		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		$args = array(
			'user_id' => $user_id,
			'orderby' => 'modified',
			'order'   => 'DESC',
		);
		if ( $active_only ) {
			$args['active_only'] = true;
		}

		return $this->all( $args );

	}


	/**
	 * Get the current featured avatar for user.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $user_id  The user ID to get.
	 *
	 * @return object  Avatar object.
	 */
	public function get_current_avatar( $user_id = false ) {
		$avatars = $this->get_user_avatars( $user_id, true );
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
	 * Get order meta key where avatar is stored.
	 *
	 * @since 1.0.0
	 *
	 * @return string  Meta key.
	 */
	public function get_avatar_meta_key() {

		$settings_prefix = woocommerce_avatar_discounts()->admin_settings()->get_prefix();
		return '_' . $settings_prefix . 'featured-avatar';

	}


	/**
	 * Get the order avatar ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id  The Order ID.
	 * @param int $user_id   The User ID.
	 *
	 * @return int  Order Avatar ID.
	 */
	public function get_order_avatar( $order_id, $user_id = false ) {
		$avatar_id = (int) get_post_meta( $order_id, $this->get_avatar_meta_key(), true );
		if ( ! $avatar_id ) {
			return false;
		}
		return $this->get( $avatar_id, $user_id );
	}


	/**
	 * Make sure Avatar belongs to this user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $avatar_id  Avatar ID.
	 * @param int $user_id  The User ID.
	 *
	 * @return bool  If valid.
	 */
	public function validate( $avatar_id, $user_id ) {
		$validate = $this->get( $avatar_id, $user_id );
		if ( ! $validate ) {
			return false;
		}
		return true;
	}


	/**
	 * Outputs the Manage Avatars interface
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id  The user ID.
	 * @param bool $echo    Either echo or return.
	 *
	 * @return string|null  HTML for manage avatars.
	 */
	public function manage( $user_id = false, $echo = true ) {

		// TODO: Add Gravatar support.

		woocommerce_avatar_discounts()->enqueue_script( 'manage-avatars' );
		woocommerce_avatar_discounts()->enqueue_style( 'avatars' );

		$output = $this->manage_avatars( $user_id );

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

		if ( $order ) {
			$this->order = $order;
		}

		if ( ! $this->order ) {
			return;
		}

		$avatar = $this->get_order_avatar( $this->order->get_order_number(), $this->order->get_customer_id() );
		if ( ! $avatar ) {
			return;
		}

		woocommerce_avatar_discounts()->enqueue_style( 'avatars' );
		Loader::load_view( 'order-avatar', compact( 'avatar' ) );

	}


	/**
	 * Display Manage Avatars interface.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id  The user ID.
	 *
	 * @return string  HTML for Manage Avatars.
	 */
	private function manage_avatars( $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$avatars = $this->get_user_avatars( $user_id, true );

		$encourage_text = woocommerce_avatar_discounts()->admin_settings()->get_setting( 'encourage_text' );

		$selected    = '';
		$classname   = empty( $avatars ) || is_admin() ? ' expanded' : '';
		$count       =  0;
		$button_text = __( 'Select Your Photo', 'woocommerce-avatar-discounts' );
		$badge_title = '';

		if ( ! empty( $avatars ) ) {
			$button_text = __( 'Upload Another Photo', 'woocommerce-avatar-discounts' );
			$count       = count( $avatars );
			$badge_title = $count . ' ' . __( 'Avatars to choose from', 'woocommerce-avatar-discounts' );
			$selected    = $avatars[0]->id; // Default to first avatar.
		}

		if ( is_admin() && ! $count ) {
			if ( $this->original ) {
				return $this->original;
			}
			return esc_html__( 'No User Avatars found.', 'woocommerce-avatar-discounts' );
		}

		$vars = compact(
			'user_id',
			'avatars',
			'encourage_text',
			'selected',
			'classname',
			'count',
			'button_text',
			'badge_title'
		);

		return Loader::get_view( 'manage-avatars', $vars );
	}


	/**
	 * Handle Avatar upload via AJAX
	 *
	 * @since 1.0.0
	 */
	public function ajax_avatar_upload() {
		$response = array(
			'success' => false,
			'status'  => null,
			'error'   => '',
			'data'    => array(),
		);

		$file_array = isset( $_FILES['woocommerce_avatar_discounts_upload'] ) ? $_FILES['woocommerce_avatar_discounts_upload'] : false;

		if ( empty( $file_array ) || UPLOAD_ERR_NO_FILE === $file_array['error'] ) {
			$response['status'] = 'empty';
			wp_send_json( $response );
			exit();
		}

		$allowed_types = array(
			'image/jpeg',
			'image/png',
			'image/gif',
			'image/tiff',
			'image/bmp',
		);

		if ( ! in_array( $file_array['type'], $allowed_types, true ) ) {
			$response['status'] = 'invalid';
			$response['error']  = esc_html__( 'Only images are allowed for your avatar. Note: SVG is not allowed.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		if ( isset( $_POST['user'] ) ) {
			$user_id =  (int) sanitize_text_field( $_POST['user'] );
		} else {
			$user_id = get_current_user_id();
		}

		if ( ! $user_id ) {
			$response['status'] = 'unauthorized';
			$response['error']  = esc_html__( 'No user found.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		$avatars = $this->get_user_avatars( $user_id, true );
		$count   = count( $avatars );
		$limit   = woocommerce_avatar_discounts()->admin_settings()->get_setting( 'limit' );
		if ( $limit > 0 && $count >= $limit ) {
			$response['status'] = 'limited';
			$response['error']  = esc_html__( 'You have reached the maximum number of avatars', 'woocommerce-avatar-discounts' ) . ': ' . $limit . '.';
			wp_send_json( $response );
			exit();
		}

		$post_data  = array(
			'post_author' => $user_id,
		);
		$attachment = media_handle_upload( 'woocommerce_avatar_discounts_upload', 0, $post_data );
		if ( is_wp_error( $attachment ) ) {
			$response['status'] = 'upload';
			$response['error']  = esc_html__( 'There was an error processing your upload.', 'woocommerce-avatar-discounts' ) . ': ' . $attachment->get_error_message();
			wp_send_json( $response );
			exit();
		}

		$image  = wp_get_attachment_image_src( $attachment, 'full' );
		$avatar = $this->get_current_avatar( $user_id );
		$status = $avatar ? 'active' : 'featured';
		$data   = array(
			'user_id'       => $user_id,
			'attachment_id' => $attachment,
			'status'        => $status,
			'url'           => $image[0],
		);

		$save = $this->db->save( $data );

		if ( false === $save ) {
			$response['status'] = 'failed';
			$response['error']  = __( 'There was an error saving your avatar.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		$this->set_featured_avatar( $save );

		$avatar = $this->get( $save, $user_id );

		$response['success'] = true;
		$response['status']  = 'OK';
		$response['html']    = Loader::get_view( 'avatar', compact( 'avatar' ) );
		wp_send_json( $response );
		exit();
	}


	/**
	 * Handle Avatar delete via AJAX
	 *
	 * @since 1.0.0
	 */
	public function ajax_delete_avatar() {
		$response = array(
			'success' => false,
			'error'   => '',
		);

		$avatar_id = ! empty( $_POST['avatar'] )  ? (int) sanitize_text_field( $_POST['avatar'] ) : false;

		if ( isset( $_POST['user'] ) ) {
			$user_id =  (int) sanitize_text_field( $_POST['user'] );
		} else {
			$user_id = get_current_user_id();
		}

		if ( ! $user_id || ! $avatar_id ) {
			$response['status'] = 'invalid';
			$response['error']  = esc_html__( 'Missing required data.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		if ( ! $this->validate( $avatar_id, $user_id ) ) {
			$response['error'] = __( 'Error trying to delete avatar.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		if ( ! $this->delete_avatar( $avatar_id ) ) {
			$response['error'] = __( 'There was an error deleting your avatar.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		$response['success'] = true;
		wp_send_json( $response );
		exit();
	}


	/**
	 * Delete Avatar from user's account.
	 *
	 * @since 1.0.0
	 *
	 * @param int $avatar_id  Avatar ID.
	 *
	 * @return bool  If deleted.
	 */
	public function delete_avatar( $avatar_id ) {
		$data = array(
			'status'   => 'deleted',
			'modified' => current_time( 'mysql' ),
		);

		if ( false === $this->db->update( $data, array( 'id' => $avatar_id ) ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Handle Feature Avatar via AJAX
	 *
	 * @since 1.0.0
	 */
	public function ajax_feature_avatar() {
		$response = array(
			'success' => false,
			'error'   => '',
		);

		$avatar_id = ! empty( $_POST['avatar'] )  ? (int) sanitize_text_field( $_POST['avatar'] ) : false;

		if ( isset( $_POST['user'] ) ) {
			$user_id =  (int) sanitize_text_field( $_POST['user'] );
		} else {
			$user_id = get_current_user_id();
		}

		if ( ! $user_id || ! $avatar_id ) {
			$response['status'] = 'invalid';
			$response['error']  = esc_html__( 'Missing required data.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		if ( ! $this->validate( $avatar_id, $user_id ) ) {
			$response['error'] = __( 'Error trying to set featured avatar.', 'woocommerce-avatar-discounts' );
			wp_send_json( $response );
			exit();
		}

		$this->set_featured_avatar( $avatar_id );

		$response['success'] = true;
		wp_send_json( $response );
		exit();
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
