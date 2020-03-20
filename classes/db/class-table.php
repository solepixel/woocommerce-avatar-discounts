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

// use \WooCommerce_Avatar_Discounts_Loader as Loader;
use \WooCommerceAvatarDiscounts\Db\Core as Core;
use \WooCommerceAvatarDiscounts\Debug as Debug;

/**
 * Abstract class for handling Database CRUD.
 *
 * @since 1.0.0
 */
abstract class Table {

	/**
	 * Primary Key Column
	 *
	 * @var string
	 */
	public $primary_key = 'id';

	/**
	 * Local object cache storage
	 *
	 * @var array
	 */
	public $object_cache = array();


	/**
	 * Handles Table creation/update. Must be used.
	 *
	 * @since 1.0.0
	 */
	abstract public function get_schema();


	/**
	 * Returns full table name.
	 *
	 * @since 1.0.0
	 *
	 * @return string  Table Name.
	 */
	public function table() {
		$tablename = strtolower( str_replace( __NAMESPACE__ . '\\', '', get_called_class() ) );
		$tablename = Core::prefix_table( $tablename );
		return $tablename;
	}


	/**
	 * Get single row by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id  Record ID.
	 *
	 * @return object  Result row object.
	 */
	public function get( $id ) {
		if ( ! empty( $this->object_cache[ $id ] ) ) {
			return $this->object_cache[ $id ];
		}
		global $wpdb;
		$sql = sprintf( 'SELECT * FROM `%s` WHERE `%s` = %%s;', $this->table(), $this->primary_key );
		$row = $wpdb->get_row( $wpdb->prepare( $sql, (int) $id ) );

		$this->object_cache[ $id ] = $row;

		return $row;
	}


	/**
	 * Update or Insert record.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data  Array of data.
	 *
	 * @return bool|int  Result of insert or update.
	 */
	public function save( $data ) {
		if ( empty( $data[ $this->primary_key ] ) ) {
			if ( isset( $data[ $this->primary_key ] ) ) {
				unset( $data[ $this->primary_key ] );
			}
			$result = $this->insert( $data );
		} else {
			$where = array(
				$this->primary_key => $data[ $this->primary_key ],
			);
			unset( $data[ $this->primary_key ] );
			$result = $this->update( $data, $where );
		}

		return $result;
	}


	/**
	 * Update existing based on lookup, or perform save().
	 *
	 * @since 1.0.0
	 *
	 * @param array $data    Update/Insert data array.
	 * @param array $lookup  Used to lookup record.
	 *
	 * @return bool|int  Result of insert or update.
	 */
	public function save_or_update( $data, $lookup ) {
		if ( ! empty( $data[ $this->primary_key ] ) ) {
			return $this->save( $data );
		}

		if ( ! empty( $lookup ) && ! is_array( $lookup ) ) {
			$lookup = array( $lookup );
		}

		global $wpdb;
		$sql   = sprintf( 'SELECT `%s` FROM `%s` WHERE ', $this->primary_key, $this->table() );
		$where = '';
		foreach ( $lookup as $col ) {
			if ( empty( $data[ $col ] ) ) {
				$where = false;
				break;
			}
			$where .= $where ? ' AND ' : '';
			$where .= $wpdb->prepare( sprintf( '`%s` = %%s', $col ), $data[ $col ] );
		}
		if ( ! $where ) {
			return $this->save( $data );
		}
		$find = $wpdb->get_row( $sql . $where . ';' );
		if ( $find ) {
			$data[ $this->primary_key ] = $find->{$this->primary_key};
		}
		return $this->save( $data );
	}


	/**
	 * Insert new row.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data  Data to insert.
	 *
	 * @return int  Insert ID.
	 */
	public function insert( $data ) {
		global $wpdb;
		$wpdb->insert( $this->table(), (array) $data );
		return $this->insert_id();
	}


	/**
	 * Update record by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data   Array of data to update.
	 * @param array $where  A named array of WHERE clauses (in column => value pairs).
	 *                      Multiple clauses will be joined with ANDs.
	 *                      Both $where columns and $where values should be "raw".
	 *                      Sending a null value will create an IS NULL comparison
	 *                      - the corresponding format will be ignored in this case.
	 *
	 * @return bool  Result of update.
	 */
	public function update( $data, $where ) {
		global $wpdb;
		if ( ! empty( $where[ $this->primary_key ] ) ) {
			unset( $this->object_cache[ $where[ $this->primary_key ] ] );
		}
		return $wpdb->update( $this->table(), (array) $data, (array) $where );
	}


	/**
	 * Build WHERE statement from array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array          Array of col => val.
	 * @param bool  $include_where  Include the word WHERE.
	 *
	 * @return string  WHERE statement.
	 */
	private function create_where_from_array( $array, $include_where = true ) {
		$where = '';
		foreach ( $array as $col => $val ) {
			if ( $where ) {
				$where .= ' AND ';
			} elseif ( $include_where ) {
				$where .= 'WHERE ';
			}
			$where .= sprintf(
				'`%s` = %%s',
				$col
			);
		}

		return $where;
	}


	/**
	 * Delete row by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int|array $id  Row ID or Array of Where conditions.
	 *
	 * @return bool  Result of delete query.
	 */
	public function delete( $id ) {
		global $wpdb;
		if ( is_array( $id ) ) {
			if ( ! count( $id ) ) {
				return false;
			}
			$where = $this->create_where_from_array( $id );
			if ( ! $where ) {
				return false;
			}
			$sql = sprintf( 'DELETE FROM `%s` %s;', $this->table(), $where );
			return $wpdb->query( $wpdb->prepare( $sql, array_values( $id ) ) );
		}

		unset( $this->object_cache[ $id ] );
		$sql = sprintf( 'DELETE FROM `%s` WHERE `%s` = %%s;', $this->table(), $this->primary_key );
		return $wpdb->query( $wpdb->prepare( $sql, (int) $id ) );
	}


	/**
	 * Get the last insert ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int  Insert ID.
	 */
	public function insert_id() {
		global $wpdb;
		return (int) $wpdb->insert_id;
	}


	/**
	 * Convert timestamp to MySQL date format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $time  Unix timestamp.
	 *
	 * @return string  MySQL date format.
	 */
	public function time_to_date( $time ) {
		return gmdate( 'Y-m-d H:i:s', $time );
	}


	/**
	 * Current date/time in MySQL Format.
	 *
	 * @since 1.0.0
	 *
	 * @return string  Current date/time in MySQL format.
	 */
	public function now() {
		return $this->time_to_date( time() );
	}


	/**
	 * Convert a string date to timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @param string $date  Date string.
	 *
	 * @return int  Unix timestamp.
	 */
	public function date_to_time( $date ) {
		return strtotime( $date . ' GMT' );
	}


	/**
	 * Check if table exists.
	 *
	 * @since 1.0.0
	 *
	 * @return bool  If table exists.
	 */
	public function table_exists() {
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE '%s';", $this->table() ) );
		if ( ! $results || ! is_array( $results ) || count( $results ) <= 0 ) {
			return false;
		}

		$row   = get_object_vars( $results[0] );
		$row   = array_values( $row );
		$table = $row[0];
		if ( $this->table() !== $table ) {
			return false;
		}
		return true;
	}


	/**
	 * Dump table info.
	 *
	 * @since 1.0.0
	 */
	public function info() {
		if ( ! $this->table_exists() ) {
			echo 'TABLE `' . esc_html( $this->table() ) . '` DOES NOT EXIST.';
			return;
		}

		global $wpdb;

		$sql     = sprintf( "DESCRIBE `%s`;", $this->table() );
		$results = $wpdb->get_results( $sql );
		if ( ! $results ) {
			echo 'COULD NOT FETCH SCHEMA FOR TABLE `' . esc_html( $this->table() ) . '`.';
			return;
		}

		echo '<p><strong>SCHEMA FOR `' . esc_html( $this->table() ) . '`:</strong></p>';
		foreach ( $results as $result ) {
			printf(
				'`%s` %s %s %s<br>',
				esc_html( $result->Field ),
				esc_html( $result->Type ),
				esc_html( $result->Key ),
				esc_html( $result->Extra )
			);
		}
	}


}
