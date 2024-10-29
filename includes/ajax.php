<?php
/**
 * Frontend Ajax.
 *
 * @package Baltic_Kit
 */
namespace BalticKit;

if ( ! defined( 'ABSPATH' ) ) exit;

class Ajax {

	use Instance;

	public function __construct() {

		add_action( 'wp_ajax_baltic_kit_recently_viewed_products', [ $this, 'recently_viewed' ] );
		add_action( 'wp_ajax_nopriv_baltic_kit_recently_viewed_products', [ $this, 'recently_viewed' ] );

	}

	/**
	 * Simple minify output.
	 *
	 * @param  [type] $output [description]
	 * @return [type]         [description]
	 */
	public function output( $output ) {
		return str_replace( array( "\n", "\t", "\r" ), '', $output );
	}

	/**
	 * Recently viewed product response.
	 *
	 * @return string html
	 */
	public function recently_viewed() {

		if ( ! isset( $_REQUEST['count'] ) ) {
			die();
		}

		$viewed_products = ! empty( $_COOKIE['baltic_kit_recently_viewed_products'] ) ? (array) explode( '|', $_COOKIE['baltic_kit_recently_viewed_products'] ) : array();
		$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		if ( empty( $viewed_products ) ) {
			die();
		}

		$count = intval( $_REQUEST['count'] );

		$query_args = [
			'posts_per_page' => $count,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'post__in'       => $viewed_products,
			'orderby'        => 'post__in',
		];

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$query_args['tax_query'] = [
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'outofstock',
					'operator' => 'NOT IN',
				],
			];
		}

	    $r = new \WP_Query( $query_args );

		ob_start();
		if( $r->have_posts() ):
			echo '<ul>';
			while( $r->have_posts() ): $r->the_post();
				wc_get_template( 'content-widget-product.php' );
			endwhile;
			echo '</ul>';
		endif;
		wp_reset_postdata();

		$content = $this->output( ob_get_clean() );

		echo $content;

		die();

	}

}
