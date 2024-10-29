<?php
/**
 * Instance
 *
 * @package Baltic_Kit
 */

namespace BalticKit;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Instance {

	/**
	 * Holds the theme instance.
	 *
	 * @access private
	 * @static
	 */
	private static $_instance;

	/**
	 * Instance
	 *
	 * @return void
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

}
