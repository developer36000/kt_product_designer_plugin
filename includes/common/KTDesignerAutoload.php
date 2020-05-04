<?php
/**
 * Class Loading Automatically
 * */
namespace common;

class KTDesignerAutoload {
	private static $instance = null;
	private function __construct() {
		spl_autoload_register( array( $this, 'autoload_namespace' ) );
	}
	
	public static function getInstance(){
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	 * @param $className
	 */
	public function autoload_namespace($className){
		$fileClass = kt_plugin_dir.'/'.str_replace("\\","/",$className).'.php';
		if (file_exists($fileClass)) {
			if (!class_exists($fileClass, FALSE)) {
				require_once $fileClass;
			}
		}
	}
}
KTDesignerAutoload::getInstance();