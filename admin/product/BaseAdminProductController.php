<?php

namespace admin\product;

use admin\ICreatorInstance;

abstract class BaseAdminProductController implements ICreatorInstance {
	public $post_product = 'product';
	
	public function __construct() {}
	
	/**
	 * Get Product meta data
	 * @param $key
	 * @return mixed
	 */
	public static function get_product_tab_meta( $key, $post_id = 0 ) {
		global $post;
		$post_id = $post_id ?: $post->ID;
		$product = wc_get_product( $post_id );
		return $product->get_meta( $key );
	}

}