<?php
/**
 * Plugin build file
 * */
namespace includes\common;

/*
 * Class space paths
 * */

// custom  menu
use admin\menu\MainAdminMenuController;
// Custom Product Row Action
use admin\product\KTAdminProductMetaModel;
use admin\product\KTAdminProductActionsModel;
// Product Customize Area
use admin\product\KTAdminProductCustomizeArea;


class KTDesignerLoader {
	use getInstance;
	
	// initialize the new class as an object
	private function __construct(){
		// is_admin () Conditional tag. It works when the admin panel of the site is displayed (console or any
		// another admin page).
		// Check whether we are in the admin panel or not
		if ( is_admin() ) {
			$this->admin(); // When in the admin area we call the backensd () method
		} else {
			$this->site(); // When on the site we call the site () method
		}
		$this->all();
	}
	
	/**
	 * The method will work when you are in the admin panel. Class loading for admin panel
	 */
	public function admin(){
		// main plugin page
		//MainAdminMenuController::newInstance();
		// Custom Product Settings
		KTAdminProductMetaModel::newInstance();
		KTAdminProductActionsModel::newInstance();
		// Product Customize Area
		KTAdminProductCustomizeArea::NewInstance();
		
	}
	
	/**
	 * The method will work when you are on the Site. Downloading classes for the Site
	 */
	public function site(){
	
	}
	
	/**
	 * The method will work everywhere. Class loading for Admin panel and Site
	 */
	public function all() {
		// plugin localization
		KTDesignerLocalization::getInstance();
		// load all sctyles and scripts for plugin
		KTDesignerLoaderScript::getInstance();
		
	}
}