<?php

namespace admin\product;

use admin\ICreatorInstance;

abstract class BaseAdminProductController implements ICreatorInstance {
	/**
	 * @var string - product post type
	 * */
	public $post_product = 'product';
	/**
	 * @var string - taxonomy name
	 * */
	public $taxonomy = 'product_cat';
	
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
	
	/**
	 * Get Product First Category
	 * @param int $product_id
	 * @return mixed
	 */
	public static function get_product_first_category_obj( $product_id = 0 ) {
		global $post;
		$post_id = $product_id ?: $post->ID;
		$terms = get_the_terms( $post_id, 'product_cat' );
		$product_cat_id = '';
		foreach ($terms as $key => $term) {
			if ( $key==0 ) {
				$product_cat_id = $term->term_id;
				break;
			}
		}
		$product_term = get_term( $product_cat_id, 'product_cat' );
		
		return $product_term;
	}

}