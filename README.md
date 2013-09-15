WordPress Symlinked Plugin Helper
=================================

A set of fixes for handling plugins linked into `WP_PLUGINS_DIR` from another path. Built as a mu-plugin.

I am not responsible for any loss of functionality, unintended operation, or disruptions to the space-time continuum if this plugin is itself loaded from a symlink.

Usage
-----

1. Drop `wp-symlink-plugin-fix.php` into your `mu-plugins` folder.
2. Link plugins into your plugin folder, e.g. : `ln -s /my/plugin/repo/buddypress/1.8 /my/wpsite/wp-content/plugins/buddypress`
3. Activate plugins and use your WP site as normal

Details
-------

### Fixes these issues with symlinked plugins:

* WordPress build URLs based on the real path of plugin files, leaving files symlinked from outside the web root inaccessible.
* Using `register_activation_hook( __FILE__, 'my_function' )` as directed creates a hook that WP will never call, so a symlinked plugin can't run its activation function.

