<?php
/**
 * Define Paths
 *
 * definition of global constants
 * define the path to the plugin folder inside the WordPress directory
 * */
define("kt_plugin_dir", plugin_dir_path(__FILE__));
define("kt_plugin_url", plugin_dir_url( __FILE__ ));
define("kt_plugin_slug", preg_replace( '/[^\da-zA-Z]/i', '_',  basename(kt_plugin_dir)));
define("kt_plugin_textdomain", str_replace( '_', '-', kt_plugin_slug ));
define("kt_plugin_option_version", kt_plugin_slug.'_version');
define("kt_plugin_options", kt_plugin_slug.'_options');
define("kt_plugin_ajax_url", admin_url('backend-ajax.php'));

// Check if the get_plugins () function is registered. This is for the front end.
// usually get_plugins () only works in the admin panel.
if ( ! function_exists( 'get_plugins' ) ) {
	// include the file with get_plugins() function
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// get plugin data
$plugin_data = get_plugin_data(kt_plugin_dir.'/'.basename(kt_plugin_dir).'.php', false, false);

define("kt_plugin_version", $plugin_data['Version']);
define("kt_plugin_name", $plugin_data['Name']);
// path to translation files
define("kt_plugin_dir_localization", plugin_basename(kt_plugin_dir.'/lang/'));