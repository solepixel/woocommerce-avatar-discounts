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

namespace WooCommerceAvatarDiscounts\Db;

use \WooCommerceAvatarDiscounts\Db\Table as Table;
use \WooCommerceAvatarDiscounts\Db\Core as Core;

/**
 * Customer Avatars table management.
 *
 * @since 1.0.0
 */
class Customer_Avatars extends Table {

	/** @var WPDB Alias on construct */
	private $wpdb;


	/**
	 * Store WPDB in property
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}


	/**
	 * Get Schema for new table creation
	 *
	 * @since 1.0.0
	 *
	 * @param string $db_version  Current database version.
	 *
	 * @return string|false  Create Table schema or false if unsupported.
	 */
	public function get_schema( $db_version = false ) {

		if ( false === $db_version ) {
			$db_version = Core::get_version();
		}

		$table_name      = $this->table();
		$charset_collate = $this->wpdb->get_charset_collate();
		$current_version = Core::current_version();

		if ( ! $this->table_exists() || version_compare( $db_version, $current_version ) < 0 ) {
			return "CREATE TABLE $table_name (
				{$this->primary_key} bigint(25) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				attachment_id bigint(20) DEFAULT NULL,
				url varchar(255) DEFAULT NULL,
				status varchar(25) DEFAULT NULL,
				modified timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  ({$this->primary_key}),
				KEY user_id (user_id)
			) $charset_collate;";
		}

		return false; // Unsupported version.
	}


	/**
	 * All Records (filterable by args array)
	 *
	 * @since 1.0.0
	 *
	 * @return array  Array of results.
	 */
	public function all( $args = array() ) {
		$where   = '';
		$orderby = '';

		if ( ! empty( $args['user_id'] ) ) {
			$where .= $where ? ' AND ' : 'WHERE ';
			$where .= $this->wpdb->prepare( '`a`.`user_id` = %d', (int) $args['user_id'] );
		}

		if ( ! empty( $args['id'] ) ) {
			$where .= $where ? ' AND ' : 'WHERE ';
			$where .= $this->wpdb->prepare( '`a`.`id` = %d', (int) $args['id'] );
		}

		if ( ! empty( $args['active_only'] ) ) {
			$where .= $where ? ' AND ' : 'WHERE ';
			$where .= $this->wpdb->prepare( '`a`.`status` <> %s', 'deleted' );
		} elseif ( ! empty( $args['status'] ) ) {
			$where .= $where ? ' AND ' : 'WHERE ';
			$where .= $this->wpdb->prepare( '`a`.`status` = %s', $args['status'] );
		}

		if ( ! empty( $args['orderby'] ) ) {
			$order    = ! empty( $args['order'] ) ? strtoupper( $args['order'] ) : 'ASC';
			$orderby .= sprintf( 'ORDER BY `a`.`%s` %s', $args['orderby'], $order );
		}

		$sql = sprintf(
			'SELECT * FROM `%s` `a` %s %s;',
			$this->table(),
			$where,
			$orderby
		);

		$rows = $this->wpdb->get_results( $sql );
		if ( ! $rows ) {
			return array();
		}
		return $rows;
	}


}
