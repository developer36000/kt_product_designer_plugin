<?php
/**
 * Needed to know for sure that we have created ONE object of the main class.
 * */
namespace includes\common;


trait getInstance {
	private static $instance = null;
	
	private function __construct() { /* ... @return Singleton */ }  // Protect against creation through new Singleton
	private function __clone() { /* ... @return Singleton */ }  // Protect From Creation Through Cloning
//	private function __wakeup() { /* ... @return Singleton */ }  // Protect from creation through unserialize
	
	public static function getInstance(){
		if ( static::$instance === null ) {
			static::$instance = new static;
		}
		return static::$instance;
	}
}