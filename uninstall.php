<?php
/**
 * Uninstall
 *
 * @package Baltic_Kit
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'baltic_kit\_%';" );

wp_cache_flush();
