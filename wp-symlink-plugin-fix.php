<?php
/*

Plugin Name: Plugin Symlink Fix
Description: Allow plugins to load from symlinked directories without special names or paths
Author: David Dean
Version: 1.0
Author URI: http://www.generalthreat.com/

*/

add_action( 'activate_plugin', 'dd_symlink_activation_fix', 10, 2 );

add_filter( 'plugins_url', 'dd_symlink_url_fix', 10, 3 );


/**
 * When used as directed, __FILE__ hooks plugin activation to the real path, but
 *  WP will try to activate using the symlink path.
 * This quick hack creates the expected activate_* hook just in time and 
 *  copies the original function(s) over.
 *
 * WARNING: activate_plugin does not fire during 'error_scrape', so error messages
 *  echoed during activation will not be visible. Users will only see the generic
 *  "This plugin ... triggered a fatal error" message.
 */
function dd_symlink_activation_fix( $plugin, $network_wide ) {

	global $wp_filter;

//	echo( 'Intercepted activation request for: ' . $plugin );

	# If realpath doesn't match the generated path, create an action pointing to the relative path
	$real_path = realpath( WP_PLUGIN_DIR . '/' . $plugin );

//	echo( 'Got real path for: ' . $plugin . ' as: ' . $real_path . "<br>\n");

	if ( $real_path != WP_PLUGIN_DIR . '/' . $plugin )
		$wp_filter['activate_' . $plugin] = $wp_filter['activate_' . substr( $real_path, 1 )];
}

/**
 * Calls to plugins_url() are made using the realpath, but this is probably not within the web root.
 * This function rewrites the URL to use the fake path. Includes caching for the path translation.
 */
function dd_symlink_url_fix( $url, $path, $plugin ) {
	
	$plugin_base_url = WP_PLUGIN_URL;

	// We have nothing to work with -- bail
	if( empty( $path ) && empty( $plugin ) )
		return $url;

	$base_url = substr( $url, strlen( $plugin_base_url ) );

	// Scan cache first - HIGHLY recommended for production use
	if( $real_base_url = wp_cache_get( $base_url, 'plugin-realpath' ) ) {
		return WP_PLUGIN_URL . '/' . $real_base_url;
	}

	// Scan plugins folder looking for a symlink that matches the plugin path from the URL
	$plugin_dirs = scandir( WP_PLUGIN_DIR );

	foreach( $plugin_dirs as $plugin_dir ) {
		if( is_link( WP_PLUGIN_DIR . '/' . $plugin_dir ) ) {

			$real_plugin_dir = realpath( WP_PLUGIN_DIR . '/' . $plugin_dir );

			// Found a match! - the real path of the plugin folder is the start of the URL path
			if( 0 === strpos( $base_url, $real_plugin_dir ) ) {
				
				$real_base_url = str_replace( $real_plugin_dir, $plugin_dir, $base_url );
				$url = WP_PLUGIN_URL . '/' . $real_base_url;

				wp_cache_set( $base_url, $real_base_url, 'plugin-realpath' );

				break;
				
			}
		}
	}

	return $url;
}

?>
