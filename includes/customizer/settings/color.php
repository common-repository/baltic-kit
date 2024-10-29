<?php
/**
 * Customizer settings
 *
 * @package Baltic_Kit
 */

namespace BalticKit\Customizer\Settings;

use BalticKit\Instance;
use BalticKit\Options;

if ( ! defined( 'ABSPATH' ) ) exit;

class Color {

	use Instance;

	public $default;

	public function __construct() {

		$this->default = Options::mods_default();

		add_action( 'customize_register', [ $this, 'meta_colors' ] );

	}

	public function meta_colors( $wp_customize ) {

		$wp_customize->add_section( 'baltic-kit-color_selection', [
			'title' 		=> esc_html__( 'Meta Color', 'baltic-kit' ),
			'panel'			=> 'baltic-color',
			'priority'		=> 1
		] );

		$wp_customize->add_setting( 'color__meta', [
			'default'           => $this->default['color__meta'],
			'transport'			=> 'postMessage',
			'sanitize_callback' => [ __class__, 'sanitize_alpha_color' ],
		] );
		$wp_customize->add_control(	new \WP_Customize_Color_Control( $wp_customize, 'color__meta', [
			'label'    	=> esc_html__( 'Meta color', 'baltic-kit' ),
			'section'  	=> 'baltic-kit-color_selection',
		] ) );

	}

	/**
	 * Sanitize Alpha color
	 *
	 * @param  string $color setting input.
	 * @return string        setting input value.
	 */
	public static function sanitize_alpha_color( $color ) {

		if ( '' === $color ) {
			return '';
		}

		if ( false === strpos( $color, 'rgba' ) ) {
			/* Hex sanitize */
			return self::sanitize_hex_color( $color );
		}

		/* rgba sanitize */
		$color = str_replace( ' ', '', $color );
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';

	}

	/**
	 * Sanitize HEX color
	 *
	 * @param  string $color setting input.
	 * @return string        setting input value.
	 */
	public static function sanitize_hex_color( $color ) {

		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return '';
	}

}
