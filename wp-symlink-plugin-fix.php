<?php
/*

Plugin Name: Plugin Symlink Fix
Description: Allow plugins to load from a symlinked directory without special names or paths
Author: David Dean
Version: 1.0
Author URI: http://www.generalthreat.com/

*/
add_filter( 'plugins_url', 'dd_symlink_fix', 10, 3 );

/**
 * 
 */
function dd_symlink_fix( $url, $path, $plugin ) {
	
	$plugin_base_url = WP_PLUGIN_URL;
//	echo 'Called with:' . "<br>\n";
//	echo 'Path: ' . $path . "<br>\n";
//	echo 'Plugin: ' . $plugin . "<br>\n";
	
//	echo 'Got URL: ' . $url . "<br>\n";

	// We have nothing to work with -- bail
	if( empty( $path ) && empty( $plugin ) )
		return $url;

//	echo 'Removing base from retrieved URL' . "<br>\n";

	$base_url = substr( $url, strlen( $plugin_base_url ) );

//	echo 'New URL: ' . $base_url . "<br>\n";

	// TODO: cache and scan cache first
	if( $real_base_url = wp_cache_get( $base_url, 'plugin-realpath' ) ) {
		return WP_PLUGIN_URL . '/' . $real_base_url;
	}

//	echo "Scanning plugin dir: " . WP_PLUGIN_DIR . "...<br>\n";
	$plugin_dirs = scandir( WP_PLUGIN_DIR );

//	var_dump( $plugin_dirs );

	foreach( $plugin_dirs as $plugin_dir ) {
		if( is_link( WP_PLUGIN_DIR . '/' . $plugin_dir ) ) {

			$real_plugin_dir = realpath( WP_PLUGIN_DIR . '/' . $plugin_dir );
//			echo 'Found a link!: ' . $plugin_dir . ' = ' . $real_plugin_dir . "<br>\n";

			if( 0 === strpos( $base_url, $real_plugin_dir ) ) {
				
				$real_base_url = str_replace( $real_plugin_dir, $plugin_dir, $base_url );
				$url = WP_PLUGIN_URL . '/' . $real_base_url;

				wp_cache_set( $base_url, $real_base_url, 'plugin-realpath' );

				break;
				
			}

		}
	}

//	echo 'Returning: ' . $url . "<br>\n";
	return $url;
}

?>
