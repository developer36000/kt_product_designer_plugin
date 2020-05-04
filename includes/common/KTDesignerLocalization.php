<?php
namespace includes\common;


class KTDesignerLocalization {
	private static $instance = null;
	private function __construct() {
		add_action('plugins_loaded', array($this, 'localization'));
	}
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	public function localization(){
		/**
		* load_plugin_textdomain ($ domain, $ deprecated, $ plugin_rel_path)
		* $domain - Unique identifier for receiving a translation string.
		* (constant MIFISTAPI_PlUGIN_TEXTDOMAIN specified in config-path.php file)
		* $deprecated - Canceled argument, works before version 2.7. A path similar to ABSPATH, to the .mo file.
		* $plugin_rel_path - The path (with a closing slash) to the .mo file directory relative to WP_PLUGIN_DIR.
		* This argument should be used instead of $ abs_rel_path.
		* (constant MIFISTAPI_PlUGIN_DIR_LOCALIZATION specified in the config-path.php file)
		*/
		load_plugin_textdomain(kt_plugin_textdomain, false, kt_plugin_dir_localization);
	}
}