<?php
/**
 * One click demo import config
 *
 * @package Baltic_Kit
 */

namespace BalticKit;

class Import {

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

	public function __construct() {

		add_action( 'pt-ocdi/after_import', [ $this, 'update_permalink'] );

	}

	public function update_permalink() {

		// Update permalink
		update_option( 'permalink_structure', '/%category%/%postname%/' );
		flush_rewrite_rules();
		wp_cache_flush();

	}


}
Import::instance();
