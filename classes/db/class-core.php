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

use \WooCommerceAvatarDiscounts\Debug as Debug;

/**
 * Core Database Class.
 *
 * @since 1.0.0
 */
class Core {

	/**
	 * Database version.
	 *
	 * @var string
	 */
	private static $db_version = '1.0';

	/** @var string  Prefix for options table */
	private static $settings_prefix;

	/** @var string  Prefix for db tables */
	private static $table_prefix;

	/** @var array Storage for table schemas */
	private static $tables = array();

	/** @var Core DB Core class instance */
	protected static $instance;


	/**
	 * This runs on plugin activation and plugins_loaded. Place all table deltas here.
	 *
	 * @since 1.0.0
	 */
	public function delta() {

		$update = false;

		foreach ( self::$tables as $sql ) {
			if ( $sql ) {
				$update = true;
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				$result = dbDelta( $sql );
				foreach ( $result as $message ) {
					Debug::debug( $message );
				}
			}
		}

		if ( $update ) {
			self::update_version();
		}
	}


	/**
	 * Add table schema to tables array.
	 *
	 * @param string $schema Table Schema
	 */
	public function add_table( $schema ) {
		self::$tables[] = $schema;
	}


	/**
	 * Set the settings prefix.
	 *
	 * @param string $prefix  Prefix for settings.
	 */
	public function set_setting_prefix( $prefix ) {
		self::$settings_prefix = $prefix;
	}


	/**
	 * Set the table prefix.
	 *
	 * @param string $prefix  Prefix for tables.
	 */
	public function set_table_prefix( $prefix ) {
		self::$table_prefix = $prefix;
	}


	/**
	 * Get Database version for current site.
	 *
	 * @since 1.0.0
	 *
	 * @return string|false  Version or false when none.
	 */
	public static function get_version() {
		return get_site_option( self::$settings_prefix . 'db_version', false );
	}


	/**
	 * Update site DB version to latest/current version.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version  Version to update to. Default is current.
	 */
	public static function update_version( $version = false ) {
		$version = false === $version ? self::current_version() : $version;
		update_site_option( self::$settings_prefix . 'db_version', $version );
	}


	/**
	 * Returns the current DB version in plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string  Current DB version.
	 */
	public static function current_version() {
		return self::$db_version;
	}


	/**
	 * Gets the table prefix, including the WordPress prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string  Full table prefix.
	 */
	public static function get_table_prefix() {
		global $wpdb;
		return $wpdb->prefix . self::$table_prefix;
	}


	/**
	 * Auto-prefix the table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table  Table name.
	 *
	 * @return string  Table name with full prefix.
	 */
	public static function prefix_table( $table ) {
		return self::get_table_prefix() . $table;
	}


	/**
	 * Gets the singleton instance of the core db class.
	 *
	 * @since 1.0.0
	 *
	 * @return \Db\Core
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
