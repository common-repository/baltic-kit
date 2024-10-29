<?php
/**
 * Helper class
 *
 * @package Baltic Kit
 */

namespace BalticKit;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Utils {

	/**
	 * Helper function for getting the script/style `.min` suffix for minified files.
	 *
	 * @static
	 * @access public
	 * @return string
	 */
	public static function get_min_suffix() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * CSS minification.
	 *
	 * @param  [type] $css [description]
	 * @return [type]      [description]
	 */
	public static function minify_css( $css ) {
		// Normalize whitespace
		$css = preg_replace( '/\s+/', ' ', $css );

		// Remove spaces before and after comment
		$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );
		// Remove comment blocks, everything between /* and */, unless
		// preserved with /*! ... */ or /** ... */
		$css = preg_replace( '~/\*(?![\!|\*])(.*?)\*/~', '', $css );
		// Remove ; before }
		$css = preg_replace( '/;(?=\s*})/', '', $css );
		// Remove space after , : ; { } */ >
		$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
		// Remove space before , ; { } ( ) >
		$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );
		// Strips leading 0 on decimal values (converts 0.5px into .5px)
		$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
		// Strips units if value is 0 (converts 0px to 0)
		$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );
		// Converts all zeros value into short-hand
		$css = preg_replace( '/0 0 0 0/', '0', $css );
		// Shortern 6-character hex color codes to 3-character where possible
		$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );
		return trim( $css );
	}

	/**
	 * Get an array of posts.
	 *
	 * @static
	 * @access public
	 * @param array $args Define arguments for the get_posts function.
	 * @return array
	 */
	public static function get_posts( $args ) {

		if ( is_string( $args ) ) {
			$args = add_query_arg(
				array(
					'suppress_filters' => false,
				)
			);
		} elseif ( is_array( $args ) && ! isset( $args['suppress_filters'] ) ) {
			$args['suppress_filters'] = false;
		}

		// Get the posts.
		$posts = get_posts( $args );

		// Properly format the array.
		$items = [];
		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}
		wp_reset_postdata();

		return $items;

	}

	/**
	 * Get an array of publicly-querable taxonomies.
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function get_taxonomies() {

		$items = [];

		// Get the taxonomies.
		$taxonomies = get_taxonomies(
			array(
				'public' => true,
			)
		);

		// Build the array.
		foreach ( $taxonomies as $taxonomy ) {
			$id         = $taxonomy;
			$taxonomy   = get_taxonomy( $taxonomy );
			$items[$id] = $taxonomy->labels->name;
		}

		return $items;

	}

	/**
	 * Get an array of publicly-querable post-types.
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function get_post_types() {

		$items = [];

		// Get the post types.
		$post_types = get_post_types(
			array(
				'public' => true,
			), 'objects'
		);

		// Build the array.
		foreach ( $post_types as $post_type ) {
			$items[ $post_type->name ] = $post_type->labels->name;
		}

		if ( array_key_exists( 'elementor_library', $items ) ) {
			unset( $items['elementor_library'] );
		}

		if ( array_key_exists( 'attachment', $items ) ) {
			unset( $items['attachment'] );
		}

		return $items;

	}

	/**
	 * Get available user roles
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function get_available_roles(){

		$users = [];

		$result  = count_users();
		$users['all'] = __( 'All Users', 'baltic-kit' );
		foreach ( $result ['avail_roles'] as $role => $count ) {
			$users[$role] = $role;
		}
		unset( $users['none'] );
		return $users;

	}

	/**
	 * Get an array of terms from a taxonomy.
	 *
	 * @param string|array $taxonomies See https://developer.wordpress.org/reference/functions/get_terms/ for details.
	 * @return array
	 */
	public static function get_terms( $taxonomies ) {

		$items = [];

		// Get the post types.
		$terms = get_terms( array(
	    	'taxonomy' 		=> $taxonomies
		) );

		// Build the array.
		foreach ( $terms as $term ) {
			$items[ $term->term_id ] = $term->name;
		}

		return $items;

	}

	/**
	 * Get an array of terms from a taxonomy.
	 *
	 * @param string|array $taxonomies See https://developer.wordpress.org/reference/functions/get_terms/ for details.
	 * @return array
	 */
	public static function get_terms_slug( $taxonomies ) {

		$items = [];

		// Get the post types.
		$terms = get_terms( [
	    	'taxonomy' 		=> $taxonomies
		]);

		// Build the array.
		foreach ( $terms as $term ) {
			$items[ $term->slug ] = $term->name;
		}

		return $items;

	}

	/**
	 * Get categories transient.
	 *
	 * @return array Categories transient cache
	 */
	public static function get_categories() {

		$items = [];

		if ( false === ( $items = get_transient( 'baltic_kit_get_categories' ) ) ) {

			$cats = self::get_terms( 'category' );

			set_transient( 'baltic_kit_get_categories', $cats, 12 * HOUR_IN_SECONDS );
		}

		return $items;

	}

	/**
	 * Get categories transient.
	 *
	 * @return array Categories transient cache
	 */
	public static function get_slug_categories() {

		$items = [];

		if ( false === ( $items = get_transient( 'baltic_kit_get_slug_categories' ) ) ) {

			$cats = self::get_terms_slug( 'category' );

			set_transient( 'baltic_kit_get_slug_categories', $cats, 12 * HOUR_IN_SECONDS );
		}

		return $items;

	}

	/**
	 * Get product categories transient.
	 *
	 * @return array Categories transient cache
	 */
	public static function get_product_cats() {

		$items = [];

		if ( false === ( $items = get_transient( 'baltic_kit_get_product_cats' ) ) ) {

			$cats = self::get_terms( 'product_cat' );

			set_transient( 'baltic_kit_get_product_cats', $cats, 12 * HOUR_IN_SECONDS );
		}

		return $items;

	}

	/**
	 * Get categories transient.
	 *
	 * @return array Categories transient cache
	 */
	public static function get_slug_product_cats() {

		$items = [];

		if ( false === ( $items = get_transient( 'baltic_kit_get_slug_product_cats' ) ) ) {

			$cats = self::get_terms_slug( 'product_cat' );

			set_transient( 'baltic_kit_get_slug_product_cats', $cats, 12 * HOUR_IN_SECONDS );
		}

		return $items;

	}

	/**
	 * Add notification for plugin requirement.
	 *
	 * @param  string  $support name of the required plugin
	 * @return boolean
	 */
	public static function unsupported_blocks( $support ) {

		if ( $support == 'woocommerce' ) {

			if ( ! class_exists( 'WooCommerce' ) ) {

				if ( is_customize_preview() ) {
					echo '<div class="baltic-require-plugin">';
					// Translators: %s is a plugin name
					echo sprintf( esc_html__( 'This homepage block require %s plugin.', 'baltic-kit' ),
						esc_attr( 'WooCommerce' )
					);
					echo '</div>';
				}

				return true;
			}

		} elseif( $support == 'twitter' ) {

			if ( ! defined( 'CAMPAIGNKIT_TWITTER_NAME' ) ) {

				if ( is_customize_preview() ) {
					echo '<div class="baltic-require-plugin">';
					// Translators: %s is a plugin name
					echo sprintf( esc_html__( 'This homepage block require %s plugin.', 'baltic-kit' ),
						esc_attr( 'Campaign Kit Twitter Widget' )
					);
					echo '</div>';
				}

				return true;
			}

		}

	}

	/**
	 * Button style.
	 *
	 * @return array
	 */
	public static function get_button_style() {

		$button = [
			'primary'   		=> esc_attr__( 'Primary', 'baltic-kit' ),
			'secondary' 		=> esc_attr__( 'Secondary', 'baltic-kit' ),
			'success' 			=> esc_attr__( 'Success', 'baltic-kit' ),
			'danger'  			=> esc_attr__( 'Danger', 'baltic-kit' ),
			'warning'  			=> esc_attr__( 'Warning', 'baltic-kit' ),
			'info'  			=> esc_attr__( 'Info', 'baltic-kit' ),
			'white'  			=> esc_attr__( 'White', 'baltic-kit' ),
			'outline-primary'   => esc_attr__( 'Primary Outline', 'baltic-kit' ),
			'outline-secondary' => esc_attr__( 'Secondary Outline', 'baltic-kit' ),
			'outline-success'  	=> esc_attr__( 'Success Outline', 'baltic-kit' ),
			'outline-danger'  	=> esc_attr__( 'Danger Outline', 'baltic-kit' ),
			'outline-warning'  	=> esc_attr__( 'Warning Outline', 'baltic-kit' ),
			'outline-info'  	=> esc_attr__( 'Info Outline', 'baltic-kit' ),
			'outline-white'  	=> esc_attr__( 'White Outline', 'baltic-kit' ),
		];

		return apply_filters( 'baltic_button_style', $button );

	}

}
