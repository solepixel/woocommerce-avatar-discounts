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

namespace WooCommerceAvatarDiscounts;

use \WooCommerce_Avatar_Discounts_Loader as Loader;

/**
 * General debug class.
 *
 * @since 1.0.0
 */
class Debug {

	/**
	 * Enable or disable logging at code level.
	 *
	 * @var bool
	 */
	private static $enabled = true;

	/**
	 * Debug file path.
	 *
	 * @var string
	 */
	private static $debug_file;

	/**
	 * Use WordPress debug.log file.
	 *
	 * @var bool
	 */
	private static $use_wp_log_file = false;

	/**
	 * Method for dumping data.
	 *
	 * @var string
	 */
	private static $dump_method = 'print_r';

	/**
	 * Allow HTML in log.
	 *
	 * @var bool
	 */
	private static $allow_html = false;

	/**
	 * Script debugging start time.
	 *
	 * @var int
	 */
	private $start_time;

	/** @var string  Plugin Slug */
	private static $plugin_slug;


	/** @var Debug class instance */
	protected static $instance;


	/**
	 * Logs debugging message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message      Message to log.
	 * @param mixed  $data         Additional data to append to log.
	 * @param bool   $dump_method  Override default dump method.
	 */
	public static function debug( $message, $data = null, $dump_method = false ) {
		if ( true !== self::$enabled ) {
			return;
		}

		if ( ! self::$debug_file ) {
			$plugin_path      = Loader::get_plugin_path();
			$logs_folder_path = str_replace( 'plugins/' . self::$plugin_slug . '/', '', $plugin_path );
			$subfolder        = self::$use_wp_log_file ? '' : 'uploads/' . self::$plugin_slug . '-logs/';
			if ( ! is_dir( $logs_folder_path . $subfolder ) ) {
				mkdir( $logs_folder_path . $subfolder );
			}
			self::$debug_file = $logs_folder_path . $subfolder . 'debug.log';
		}

		if ( ! file_exists( self::$debug_file ) ) {
			self::clear_file( self::$debug_file, true );
		}

		$dump_method = false !== $dump_method ? $dump_method : self::$dump_method;

		if ( null !== $data ) {
			if ( 'print_r' === $dump_method ) {
				$data_dump = print_r( $data, true );
			} elseif ( 'var_dump' === $dump_method ) {
				ob_start();
				$line = __LINE__ + 1;
				var_dump( $data );
				$data_dump = trim( ob_get_clean() );
				if ( true !== self::$allow_html ) {
					$data_dump = trim( strip_tags( $data_dump ) );
					$data_dump = str_replace( __FILE__ . ':' . $line . ':', '', $data_dump );
				}
			} elseif ( 'var_export' === $dump_method ) {
				$data_dump = var_export( $data, true );
			} elseif ( 'string' === $dump_method ) {
				$data_dump = (string) $data;
			} else {
				$data_dump = 'INVALID_DUMP_METHOD';
			}
			$message .= ': ' . $data_dump;
		}

		$line = '[' . date( 'Y-m-d H:i:s' ) . '] ' . $message . "\r\n";

		self::append_file( self::$debug_file, $line );
	}


	/**
	 * Set the plugin slug.
	 *
	 * @since 1.0.0
	 */
	public function set_plugin_slug( $slug ) {
		self::$plugin_slug = $slug;
	}


	/**
	 * Log debug message (non-static alias).
	 *
	 * @since 1.0.0
	 *
	 * @param string $message      Message to log.
	 * @param mixed  $data         Additional data to append to log.
	 * @param bool   $dump_method  Override default dump method.
	 */
	public function log( $message, $data = null, $dump_method = false ) {
		self::debug( $message, $data, $dump_method );
	}


	/**
	 * Log an error.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message      Error message.
	 * @param mixed  $data         Additional debug data.
	 * @param bool   $dump_method  Override default dump method.
	 */
	public function error( $message, $data = null, $dump_method = false ) {
		$this->log( 'ERROR - ' . $message, $data, $dump_method );
	}


	/**
	 * Log a warning.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message      Warning message.
	 * @param mixed  $data         Additional debug data.
	 * @param bool   $dump_method  Override default dump method.
	 */
	public function warning( $message, $data = null, $dump_method = false ) {
		$this->log( 'WARNING - ' . $message, $data, $dump_method );
	}


	/**
	 * Append text to a file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path  Path to file.
	 * @param string $content    Content to append.
	 */
	public static function append_file( $file_path, $content ) {
		if ( ! file_exists( $file_path ) ) {
			self::clear_file( $file_path, true );
		}
		$resource = fopen( $file_path, 'a+' );
		if ( ! $resource ) {
			return false;
		}
		fwrite( $resource, $content );
		fclose( $resource );
	}


	/**
	 * Clear file contents.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path  Path to file.
	 * @param bool   $force      Make sure an empty file exists.
	 *
	 * @return bool  If cleared.
	 */
	public static function clear_file( $file_path, $force = false ) {
		if ( ! file_exists( $file_path ) ) {
			if ( true !== $force ) {
				return false;
			}

			file_put_contents( $file_path, '' );
			return true;
		} else {
			$resource = fopen( $file_path, 'w' );
		}

		if ( ! $resource ) {
			return false;
		}

		fwrite( $resource, '' );
		fclose( $resource );
		return true;
	}


	/**
	 * Start logger with time.
	 *
	 * @since 1.0.0
	 */
	public function script_start() {
		$this->start_time = microtime( true );
		$this->log( 'TIME|SCRIPT_START - ' . $this->start_time );
	}


	/**
	 * Get elapsed time from script_start.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $human_readable  If return value should be human readable.
	 *
	 * @return bool|string|int  Script run time.
	 */
	public function elapsed_time( $human_readable = false ) {
		if ( ! $this->start_time ) {
			return false;
		}
		$current_time = microtime( true );
		$diff         = $current_time - $this->start_time;

		if ( false === $human_readable ) {
			return $diff;
		}
		if ( $diff < 1 ) {
			return round( $diff * 100, 2 ) . 'ms';
		} elseif ( $diff < 60 ) {
			return round( $diff, 2 ) . 's';
		} elseif ( $diff < 3600 ) {
			return '~' . round( $diff / 60, 2 ) . 'm';
		} else {
			return '~' . round( $diff / 3600, 2 ) . 'h';
		}
		return $diff;
	}


	/**
	 * Log script start time with marker.
	 *
	 * @since 1.0.0
	 *
	 * @param string $marker  Marker text.
	 */
	public function log_time( $marker = '' ) {
		$prefix = $marker ? 'TIME|' . $marker . ' - ' : 'TIME - ';
		$this->log( $prefix . $this->elapsed_time( true ) );
	}


	/**
	 * Gets the singleton instance of the Debug class.
	 *
	 * @since 1.0.0
	 *
	 * @return \Debug
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
