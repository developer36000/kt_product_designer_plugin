<?php

namespace includes\common;


trait NewInstance {
	public static function newInstance(){
		// TODO: Implement newInstance() method.
		$instance = new self;
		return $instance;
	}
}