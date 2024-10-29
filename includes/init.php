<?php
/**
 * Baltic Kit class
 *
 * @package Baltic_Kit
 */

namespace BalticKit;

if ( ! defined( 'ABSPATH' ) ) exit;

class Init {

	/**
	 * Holds the theme instance.
	 *
	 * @access private
	 * @static
	 */
	public static $instance;

	private static $classes_map = [];

	private static $classes_aliases = [];

	public $suffix;

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

	/**
	 * Plugin constructor.
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {

		add_action( 'init', [ $this, 'init' ], 0 );

	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		spl_autoload_register( [ $this, 'autoload' ] );

		$this->components();

		$this->hooks();

	}

	/**
	 * Initiate components.
	 *
	 * @return void
	 */
	public function components() {

		Ajax::instance();

		Customizer\Settings\Color::instance();

		Widgets\Init::instance();

	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'baltic_meta', [ $this, 'meta_color'] );

		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );

	}

	/**
	 * Hook meta colors.
	 *
	 * @return void
	 */
	public function meta_color() {

		$meta_color = Options::get_theme_mod( 'color__meta' );

		if ( ! empty( $meta_color ) ) {
			echo '<meta name="theme-color" content="'. esc_attr( $meta_color ) .'">' . "\n";
			echo '<meta name="msapplication-navbutton-color" content="'. esc_attr( $meta_color ) .'">' . "\n";
			echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
			echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
		}

	}

	/**
	 * Enqueue plugin frontend assets.
	 *
	 * @return void
	 */
	public function frontend_assets() {

		$suffix = Utils::get_min_suffix();

		wp_enqueue_script( 'baltic-kit-frontend',
			BALTIC_KIT_URI . "assets/js/frontend{$suffix}.js",
			['jquery'],
			BALTIC_KIT_VERSION,
			true
		);

		$output = array(
			'ajax_url'		=> admin_url( 'admin-ajax.php' ),
			'error_msg'		=> esc_html__( 'Request error.', 'baltic-kit' )
		);
		wp_localize_script( 'baltic-kit-frontend', 'BalticKitl10n', $output );

	}

	/**
	 * Laod class.
	 *
	 * @param  [type] $relative_class_name [description]
	 * @return [type]                      [description]
	 */
	private static function load_class( $relative_class_name ) {

		if ( isset( self::$classes_map[ $relative_class_name ] ) ) {
			$filename = BALTIC_KIT_INC . '/' . self::$classes_map[ $relative_class_name ];
		} else {
			$filename = strtolower(
				preg_replace(
					[ '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$relative_class_name
				)
			);

			$filename = BALTIC_KIT_INC . '/' . $filename . '.php';
		}

		if ( is_readable( $filename ) ) {
			require $filename;
		}

	}

	/**
	 * Autoload function.
	 *
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function autoload( $class ) {

		if ( 0 !== strpos( $class, __NAMESPACE__ . '\\' ) ) {
			return;
		}

		$relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class );

		$has_class_alias = isset( self::$classes_aliases[ $relative_class_name ] );

		if ( $has_class_alias ) {
			$relative_class_name = self::$classes_aliases[ $relative_class_name ];
		}

		$final_class_name = __NAMESPACE__ . '\\' . $relative_class_name;

		if ( ! class_exists( $final_class_name ) ) {
			self::load_class( $relative_class_name );
		}

		if ( $has_class_alias ) {
			class_alias( $final_class_name, $class );
		}

	}

}

Init::instance();
