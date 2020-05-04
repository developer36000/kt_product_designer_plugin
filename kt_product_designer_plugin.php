<?php

/*
Plugin Name: KT Product Designer
Plugin URI:  https://github.com/developer36000/kt_product_designer_plugin
Description: Woocommerce Product Customization based in the React
Version: 1.0
Author: Developer36000
Author URI: https://github.com/developer36000/kt_product_designer_plugin
Text Domain: kt
Domain Path: /lang/
License: A "Slug" license name e.g. GPL2
    Copyright 2020  Developer36000  (email: developer36000@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// include basic plugin files
require_once plugin_dir_path(__FILE__) . '/config-path.php';
require_once kt_plugin_dir . '/includes/common/KTDesignerAutoload.php';
require_once kt_plugin_dir . '/includes/KTDesignerPlugin.php';


// call plugin activation and deactivation functions
register_activation_hook( __FILE__, array('includes\KTDesignerPlugin' ,  'activation' ) );
register_deactivation_hook( __FILE__, array('includes\KTDesignerPlugin' ,  'deactivation' ) );


