<?php
namespace admin\menu;
use includes\common\NewInstance;

class MainAdminMenuController extends BaseAdminMenuController {
	
	public function action()
	{
		// TODO: Implement action() method.
		/**
		 * add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		 *
		 */
		$pluginPage = add_menu_page(
			_x(
				'KT Product Designer',
				'backend menu page' ,
				kt_plugin_textdomain
			),
			_x(
				'KT Product Designer',
				'backend menu page' ,
				kt_plugin_textdomain
			),
			'manage_options',
			kt_plugin_textdomain,
			array($this,'render'),
			kt_plugin_url .'assets/images/kt_designer.svg',
			10 // $position after Media
		);
	}
	
	/**
	 * Method responsible for the content of the page
	 */
	public function render() {
		// TODO: Implement render() method.
		$pathView = kt_plugin_dir . '/admin/menu/templates/MainAdminMenu.view.php';
		$this->loadView($pathView);
	}
	
	use NewInstance;
}