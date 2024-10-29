<?php
/**
 * Plugin Name: Baltic Kit
 * Description: Baltic Kit is a core features for Baltic theme.
 * Plugin URI: https://wordpress.org/plugins/baltic-kit
 * Author: Campaignkit
 * Version: 1.0.3
 * Author URI: https://campaignkit.co/
 * Text Domain: baltic-kit
 *
 * @package   Baltic_Kit
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'baltic_kit_get_data' ) ) :
/**
 * Get plugin data
 *
 * @param  $data [plugin slug data]
 * @access public
 * @since 1.4
 * @return void
 */
function baltic_kit_get_data( $data ) {

	if ( ! function_exists( 'get_plugins' ) ){
	    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$plugin_folder 	= get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file 	= basename( ( __FILE__ ) );

	return $plugin_folder[$plugin_file][$data];

}
endif;

if ( ! defined( 'BALTIC_KIT_THEME' ) ) {
	define( 'BALTIC_KIT_THEME', 'baltic' );
}

define( 'BALTIC_KIT_NAME', baltic_kit_get_data( 'Name' ) );
define( 'BALTIC_KIT_VERSION', baltic_kit_get_data( 'Version' ) );

define( 'BALTIC_KIT_DIR', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
define( 'BALTIC_KIT_INC', BALTIC_KIT_DIR . 'includes' );
define( 'BALTIC_KIT_URI', plugin_dir_url( __FILE__ ) );


if( ! function_exists( 'baltic_kit_load_plugin' ) ) :
/**
 * Load Baltic Pro plugins
 * @return  void
 */
function baltic_kit_load_plugin() {

	$themes = search_theme_directories();

	load_plugin_textdomain( 'baltic-kit' );

	if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
		add_action( 'admin_notices', 'baltic_kit_require_php' );
	} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
		add_action( 'admin_notices', 'baltic_kit_require_wp' );
	} elseif ( ! isset( $themes[BALTIC_KIT_THEME] ) || ( BALTIC_KIT_THEME != get_option( 'template' ) ) ) {
		add_action( 'admin_notices', 'baltic_kit_require_theme' );
	} else {
		require_once( BALTIC_KIT_DIR . 'includes/init.php' );
		require_once( BALTIC_KIT_DIR . 'includes/import.php' );
	}

}
endif;
add_action( 'plugins_loaded', 'baltic_kit_load_plugin' );

/**
 * Show in WP Dashboard notice about the plugin is not activated (PHP version).
 *
 * @since 1.0.0
 * @return void
 */
function baltic_kit_require_php() {
	/** translators: %1$s: Theme name, %2$s: Current PHP version */
	$message = sprintf( esc_html__( '%1$s plugin requires at least PHP version 5.4. You are running version %2$s. Please ask your hosting provider.', 'baltic-kit' ),
		esc_html( BALTIC_KIT_NAME ),
		absint( PHP_VERSION )
	);
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Show in WP Dashboard notice about the plugin is not activated (WP version).
 *
 * @since 1.0.0
 * @return void
 */
function baltic_kit_require_wp() {

	/** translators: %1$s: Theme name, %2$s: Current WordPress version */
	$message = sprintf( __( '%1$s plugin requires at least WordPress version 4.7. You are running version %2$s. Please upgrade and try again.', 'baltic-kit' ),
		esc_html( BALTIC_THEME_NAME ),
		absint( $GLOBALS['wp_version'] )
	);
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );

}

/**
 * Baltic Kit admin notice
 *
 * @since  1.0.0
 * @return  string
 */
function baltic_kit_require_theme() {

	$screen = get_current_screen();
	$themes = search_theme_directories();

	if ( ! isset( $screen->parent_file ) ) {
		return;
	}

	/** If Baltic Theme is not installed */
	if ( ! isset( $themes[BALTIC_KIT_THEME] ) ) {

		if ( ! current_user_can( 'install_themes' ) ) {
			return;
		}

		$install_link 		= wp_nonce_url( self_admin_url( 'update.php?action=install-theme&theme=' . BALTIC_KIT_THEME ), 'install-theme_' . BALTIC_KIT_THEME );
		/* translators: %s: Theme name */
		$install_text 		= sprintf( __( 'Install %s Theme', 'baltic-kit' ), BALTIC_KIT_THEME );
		$install_url 		= '<a class="button button-primary" href="'. $install_link .'">'. esc_html( $install_text ) .'</a>';

		/* translators: %1$s: Plugin name, %2$s: Required theme name */
		$message = sprintf( __( 'In order to use <b>%1$s</b> plugin, you need to install <b>%2$s</b> WordPress theme. %3$s', 'baltic-kit' ),
			BALTIC_KIT_NAME,
			ucfirst( BALTIC_KIT_THEME ),
			$install_url
		);

		echo '<div class="error notice is-dismissible">'. wp_kses_post( wpautop( $message ) ) .'</div>';

	/** If Baltic Theme is installed but not activated */
	} elseif ( isset( $themes[BALTIC_KIT_THEME] ) && BALTIC_KIT_THEME != get_option( 'template' ) ) {

		if ( ! current_user_can( 'switch_themes' ) ) {
			return;
		}

		$activation_link 	= wp_nonce_url( self_admin_url( 'themes.php?action=activate&stylesheet=' . BALTIC_KIT_THEME ), 'switch-theme_' . BALTIC_KIT_THEME );
		$activation_url 	= '<a class="button button-primary" href="'. $activation_link .'">'. __( 'Activate Baltic Theme', 'baltic-kit' ) .'</a>';
		/* translators: %1$s: Plugin name, %2$s: Required theme name */
		$message = sprintf( __( 'In order to use %1$s plugin, you need to activate free %2$s WordPress theme. %3$s', 'baltic-kit' ),
			BALTIC_KIT_NAME,
			ucfirst( BALTIC_KIT_THEME ),
			$activation_url
		);

		echo '<div class="error notice is-dismissible">'. wp_kses_post( wpautop( $message ) ) .'</div>';

	}

}
