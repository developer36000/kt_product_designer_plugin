<?php
/**
 * Load All Styles and Scripts
 * */
namespace includes\common;

class KTDesignerLoaderScript {
	use GetInstance;
	
	private function __construct(){
		// We check in the admin panel or not
		if ( is_admin() ) {
			add_action('admin_enqueue_scripts', array(&$this, 'loadScriptBack' ) );
			add_action('admin_head', array(&$this, 'loadHeadScriptBack'));
		} else {
			add_action( 'wp_enqueue_scripts', array(&$this, 'loadScriptFront' ) );
			add_action( 'wp_head', array(&$this, 'loadHeadScriptFront'));
			add_action( 'wp_footer', array(&$this, 'loadFooterScriptFront'));
		}
		
	}
	
	public function loadScriptBack($hook){
		// SCRIPT
		wp_register_script(
			kt_plugin_slug.'-admins-js', //$handle
			kt_plugin_url.'assets/admin/js/kt_designer_admin_script.js',
			array(
				'jquery'
			),
			kt_plugin_version,
			true
		);
		
		// React SCRIPT
		wp_register_script(
			kt_plugin_slug.'-build-js', //$handle
			kt_plugin_url.'build/index.js',
			['wp-element'],
			kt_plugin_version,
			true
		);
		
		/**
		 * Adds a script only if it has not been added yet and other scripts on which it depends are registered.
		 * Dependent scripts are added automatically.
		 */
		wp_enqueue_script( kt_plugin_slug.'-admins-js' );
		//wp_enqueue_script( kt_plugin_slug.'-build-js' );
		
		$kt_jp = array(
			'kt_admin_nonce' => wp_create_nonce( 'kt_admin_nonce' ),
			'ajaxURL' => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( kt_plugin_slug.'-admins-js', 'KT_Designer_APIAjaxUrl', $kt_jp );
		
		
		// STYLE
		wp_register_style(
			kt_plugin_slug.'-admin', //$handle
			kt_plugin_url.'assets/admin/css/kt_designer_admin_styles.css',
			array(),
			kt_plugin_version,
			'all' //(all|screen|handheld|print)
		);
		wp_enqueue_style( kt_plugin_slug.'-admin' );
	}
	
	
	public function loadHeadScriptBack(){
		// Enter script here
		
		
	}
	public function loadScriptFront($hook) {
		
		// React SCRIPT
		wp_register_script(
			kt_plugin_slug.'-front-build-js', //$handle
			kt_plugin_url.'build/index.js',
			['wp-element'],
			kt_plugin_version,
			true
		);
		//wp_enqueue_script(kt_plugin_slug.'-build-js');
	
	/*
		$ajaxsome = array( 'ajaxurl' => kt_plugin_ajax_url);
		wp_localize_script(
			kt_plugin_slug.'-front-js', //$handle,
			kt_plugin_slug.'_ajax',
			$ajaxsome
		);*/
		
		// STYLE
		wp_register_style(
			kt_plugin_slug.'-front', //$handle
			kt_plugin_url.'assets/frontend/css/mapi-front.css',
			array(),
			kt_plugin_version,
			'all' // (all|screen|handheld|print)
		);
		
		
		//wp_enqueue_style(kt_plugin_slug.'-front');
		
	}
	public function loadHeadScriptFront(){
		
		
	}
	public function loadFooterScriptFront(){
		
		
	}
	
}