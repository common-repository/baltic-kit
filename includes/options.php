<?php
/**
 * Options Class.
 *
 * @package Baltic_Kit
 */

namespace BalticKit;

if ( ! defined( 'ABSPATH' ) ) exit;

class Options {

	public static function get_option() {

	    $option = wp_parse_args(
	        get_option( 'baltic_kit_settings', array() ),
	        self::defaults()
	    );

	    return $option;

	}

	public static function get_theme_mod( $name ) {

		$default = self::mods_default();

		if ( array_key_exists( $name, $default ) ) {
			return get_theme_mod( esc_attr( $name ), $default[$name] );
		} else {
			return get_theme_mod( esc_attr( $name ) );
		}

	}

	public static function options_default() {

		$defaults = [];

		return apply_filters( 'baltic_kit_options_default', $defaults );

	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function mods_default() {

		$defaults = [
			'color__meta' => '#ff5722',
		];

		return apply_filters( 'baltic_kit_mods_default', $defaults );

	}

}
