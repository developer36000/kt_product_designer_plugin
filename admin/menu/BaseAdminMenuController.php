<?php
/**
 * We tell WordPress about new pages.
 *
 * To do this, create a function that should be bound to the action 'admin_menu'.
 * */
namespace admin\menu;


use admin\ICreatorInstance;

abstract class BaseAdminMenuController implements ICreatorInstance {
	public function __construct(){
		/*
		 * Logs a hook event. When registering, a PHP function is specified,
		 * which will fire at the time of the event that is called using do_action ().
		 */
		add_action('admin_menu', array( &$this, 'action'));
	}
	
	abstract public function action();
	abstract public function render();
	/**
	 * View connection method
	 * @param $view
	 * @param int $type
	 * @param array $data
	 */
	protected function loadView($view, $type = 0, $data = array()){
		if (file_exists($view)) {
			switch($type){
				case 0:
					require_once $view;
					break;
				case 1:
					require $view;
					break;
				default:
					require_once $view;
					break;
			}
		} else {
			wp_die(sprintf(__('(View %s not found)',kt_plugin_textdomain), $view));
		}
	}
}