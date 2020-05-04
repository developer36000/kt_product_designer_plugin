<?php
/*
 * The main file of the plugin
 *
 * Register the function that will be triggered during deactivation and activation of the plugin in the plugin class
 * */


namespace includes;
use includes\common\KTDesignerDefaultOption;
use includes\common\KTDesignerLoader;
use includes\common\getInstance;

//use backend\guest_book\menu\models\GuestBookModel;

class KTDesignerPlugin {
	use getInstance;
	private function __construct() {
		KTDesignerLoader::getInstance();
		add_action('plugins_loaded', array($this, 'setDefaultOptions'));
		
		
	}
	
	/**
	 * Если не созданные настройки установить по умолчанию
	 */
	public function setDefaultOptions(){
		if( ! get_option(kt_plugin_options) ){
			update_option( kt_plugin_options, KTDesignerDefaultOption::getDefaultOptions() );
		}
		if( ! get_option(kt_plugin_version) ){
			update_option(kt_plugin_version, kt_plugin_version);
		}
	}
	
	static public function activation()
	{
		// debug.log
		error_log('plugin '.kt_plugin_name.' activation');
		// Create Table in the database
		//GuestBookModel::createTable();
		error_log('plugin '.kt_plugin_name.' create Default Pages');
		//BackPagesModel::createDefaultPage();
		
		
		
	}
	
	static public function deactivation()
	{
		// debug.log
		error_log('plugin '.kt_plugin_name.' deactivation');
		delete_option(kt_plugin_options);
		delete_option(kt_plugin_version);
		//GuestBookModel::deleteTable();
		error_log('plugin '.kt_plugin_name.' delete Table');
		error_log('plugin '.kt_plugin_name.' delete Default Pages Option');
		//BackPagesModel::deleteDefaultPageOption();
	}
	
}

KTDesignerPlugin::getInstance();