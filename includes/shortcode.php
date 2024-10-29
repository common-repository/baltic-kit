<?php
/**
 * Shortcode.
 *
 * @package Baltic_Kit
 */

namespace BalticKit;

if ( ! defined( 'ABSPATH' ) ) exit;

class Shortcode {

	/**
	 * Holds the theme instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Baltic_Init
	 */
	public static $instance;

	/**
	 * Ensures only one instance of the theme class is loaded or can be loaded.
	 *
	 * @access public
	 * @static
	 *
	 * @return Init an instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			do_action( 'baltic_kit_loaded' );
		}

		return self::$instance;
	}

	public function __construct() {

		add_shortcode( 'baltic-site_logo', [ $this, 'site_logo' ] );

	}

	/**
	 * Get dynamic site logo
	 *
	 * @param  array $atts shortcode attributes
	 * @return void
	 */
	public function site_logo( $atts ) {

		$defaults = array(
			'after'  => '',
			'before' => '',
		);
		$atts     = shortcode_atts( $defaults, $atts, 'baltic-site_logo' );

		$output = $atts['before'] . get_custom_logo() . $atts['after'];

		return apply_filters( 'baltic_kit_site_logo', $output, $atts );

	}

}
