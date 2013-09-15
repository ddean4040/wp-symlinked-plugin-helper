=== Symlink Plugin Fix ===
Contributors: ddean
Tags: plugin, symlink, plugins_url, activation
Requires at least: 3.5
Tested up to: 3.6.1

Fixes plugins_url() path for plugins symlinked into the wp-content/plugins directory
Enables activation function for symlinked plugins using register_activation_hook(__FILE__)
