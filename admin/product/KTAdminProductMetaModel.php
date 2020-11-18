<?php
/**
 * Custom Product meta box (data) and settings
 * */
namespace admin\product;
use includes\common\NewInstance;


class KTAdminProductMetaModel extends BaseAdminProductController {
	
	public function __construct() {
		parent::__construct();
		// Added Custom product meta box
		add_action( "woocommerce_product_options_general_product_data", array( $this, 'ktwc_general_option_group' ) );
		add_action( "woocommerce_process_product_meta", array( $this, 'ktwc_general_save_fields' ), 10, 2 );
		
	}
	
	/**
	 * Added Custom product meta box
	 * */
	public function ktwc_general_option_group() {
		echo '<div class="options_group">';
		 $super_product = self::get_product_tab_meta('super_product');
		
		woocommerce_wp_checkbox( array(
			'id'      => 'super_product',
			'value'   => $super_product,
			'label'   => 'This is a DEFAULT product',
			'desc_tip' => true,
			'description' => 'If it is a DEFAULT WooCommerce product',
		) );
		if ( $super_product ) {
		   echo '<script> jQuery(document).find("input#super_product").prop("checked", true); </script>';
		} else {
			echo '<script> jQuery(document).find("input#super_product").prop("checked", false); </script>';
		}
		
		echo '</div>';
	}
	/**
	 * Save the custom product Fields
	 * @param $post_id
	 */
	public function ktwc_general_save_fields( $post_id, $post ) {
		$product = wc_get_product( $post_id );
		
		$label_amazon = isset( $_POST['super_product'] ) ? $_POST['super_product'] : '';
		$product->update_meta_data( 'super_product', sanitize_text_field( $label_amazon ) );
		
		$product->save();
		
	}
	
	

	
	use NewInstance;
}