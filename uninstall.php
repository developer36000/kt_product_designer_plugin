<?php
//use includes\models\admin\menu\KTProductDesignerSubMenuModel;

// If the file is accessed directly, close access
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
	exit();
	
} else {
	// debug.log
	error_log('KT Product Designer table of plugin '.kt_plugin_name.' has been removed');
	//Removing a plugin tables
	//KTProductDesignerSubMenuModel::deleteTable();
}

$option_name = kt_plugin_options;
// For a regular site.
if ( !is_multisite() ) {
	delete_option( $option_name );
}

