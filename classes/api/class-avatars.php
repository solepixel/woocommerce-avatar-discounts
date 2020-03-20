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

namespace WooCommerceAvatarDiscounts\API;

defined( 'ABSPATH' ) or exit;

use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Response;
use \WP_REST_Server;

/**
 * The Rest API Avatars class.
 *
 * @since 1.0.0
 */
class Avatars extends WP_REST_Controller {

	/** @var string API version */
	protected $version = '1';

	/** @var string API namespace */
	protected $slug = 'wc-avatar-discounts';

	/** @var string  API Endpoint */
	protected $route = 'avatars';


	/**
	 * Rest API Avatars hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/** Set the global namespace. */
		$this->namespace = $this->slug . '/v' . $this->version;

		/** Hook into Rest API init, Add endpoint for customer avatars. */
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

	}


	/**
	 * Register Rest API Route for Avatars.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		/** URL: /wp-json/wc-avatar-discounts/v1/avatars */
		register_rest_route(
			$this->namespace,
			'/' . $this->route,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_avatars' ),
				'permission_callback' => function () {
					return (bool) wp_get_current_user();
				},
			)
		);
	}


	/**
	 * API Endpoint: avatars
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request  Rest Request object.
	 *
	 * @return \WP_REST_Response  Rest Response object.
	 */
	public function get_avatars( WP_REST_Request $request ) {
		$data = array(
			'avatars' => woocommerce_avatar_discounts()->avatars()->all(),
		);

		return new WP_REST_Response( $data, 200 );
	}


}
