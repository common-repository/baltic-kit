<?php
/**
 * Widgets
 *
 * @package Baltic_Kit;
 */

namespace BalticKit\Widgets;

use BalticKit\Instance;

if ( ! defined( 'ABSPATH' ) ) exit;

class Init {

	use Instance;

	public function __construct() {

		add_action( 'widgets_init', [ $this, 'register' ] );

	}

	/**
	 * Register Baltic custom widgets.
	 *
	 * @return void
	 */
	public function register() {

		if ( class_exists( 'WooCommerce' ) ) {
			register_widget( '\BalticKit\Widgets\Ajax_Recent_Viewed_Product' );
		}

	}


}
