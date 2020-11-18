<?php
/**
 * Custom Product meta box (data) and settings
 * */
namespace admin\product;
use includes\common\NewInstance;


class KTAdminProductCustomizeArea extends BaseAdminProductController {
	
	public function __construct() {
		parent::__construct();
		
		// Meta boxes actions
		add_action( 'add_meta_boxes', array( $this, 'ktwc_product_customization_add_meta' ) );
		add_action( 'save_post', array( $this, 'ktwc_product_customization_save_meta' ), 1, 2 );
		
		add_action( 'edit_form_after_title', array( $this, 'ktwc_product_top_form_edit' ) );
		
		
		
		// Custom product ajax action
		//add_action( 'wp_ajax_render_coordinates_product', array( $this, 'ktwc_ajax_action') );
		//add_action( 'wp_ajax_nopriv_render_coordinates_product', array( $this, 'ktwc_ajax_action') );
		
	}

	public function ktwc_product_top_form_edit( $post ) {
		if( 'product' == $post->post_type ) {
			$is_default = self::get_product_tab_meta( 'super_product', $post->ID );
			$png_image = get_post_meta( $post->ID, '_png_image_coord_real', true );
			if ( $png_image && $is_default !== 'yes' ) {
				echo "<a download='".get_the_title($post->ID)."' href='".$png_image."' id='download-custom-img' target='_blank' class='custom_btn' >".__('Download Customized Image') ."</a>";
				echo "<a href='".$png_image."' id='print-custom-img' class='custom_btn'>".__('Print Customized Image') ."</a>";
			}
			
		}
		
	}
	
	/**
	 * Add the Metabox Data for Customization of Product
	 * @param $post_type
	 */
	public function ktwc_product_customization_add_meta( $post_type ) {
		// Set the post types to which the block will be added
		$screens = array( 'product' );
		if ( in_array( $post_type, $screens )) {
			add_meta_box(
				'ktwc_customization_coordinates',
				__( 'Product Customization: Generate Coordinates', kt_textdomain ),
				array( $this, 'ktwc_meta_render_callback' ),
				$screens, 'advanced', 'low',
				array( 'id' => '_coordinates' )
			);
			
		}
	}
	
	
	/**
	 * Save the Metabox Data for Customization of Product
	 * @param $post_id
	 * @param $post
	 */
	public function ktwc_product_customization_save_meta( $post_id, $post ) {
		// if it's autosave don't do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		
	
		$coord_labels = array(
			'_png_image' => 'Customize Image - png',
			'_rect_obg' => 'Canvas Object Data',
			'_scaleRatio' => 'Canvas Scale Ratio',
		);
		foreach ( $coord_labels as $key => $label ) {
			$key_l = $key.'_coord_real';
			$product_meta[$key_l] = $_POST[$key_l];
		}
		
		// Add values of $events_meta as custom fields
		foreach ( $product_meta as $key => $value ) { // Cycle through the $events_meta array!
			if ( $post->post_type == 'revision' ) return; // Don't store custom data twice
			$value = implode( ',', (array)$value ); // If $value is an array, make it a CSV (unlikely)
			if ( get_post_meta( $post->ID, $key, FALSE ) ) { // If the custom field already has a value
				update_post_meta( $post->ID, $key, $value );
			} else { // If the custom field doesn't have a value
				add_post_meta( $post->ID, $key, $value );
			}
			if ( !$value ) delete_post_meta( $post->ID, $key ); // Delete if blank
		}
		
	}
	
	/**
	 * Metabox Render
	 * */
	public function ktwc_meta_render_callback( $post, $args ) {
		global $post;
		// Use nonce for verification
	//	wp_nonce_field( plugin_basename( __FILE__ ), 'ktwc_customization_nonce' );
		
		$coord_labels = array(
			'_png_image' => 'Customize Image - png',
			'_scaleRatio' => 'Canvas Scale Ratio',
			'_rect_obg' => 'Canvas Object Data'
		);
		// The metabox HTML
		foreach ( $coord_labels as $key => $label ) {
			$meta_name = $key.'_coord_real';
			$meta_value = get_post_meta( $post->ID, $meta_name, true );
			
			if ( $key == '_rect_obg' ) {
				echo '<div style="display: flex; align-items: center; margin: 0 15px 15px 0; width: 100%">';
				echo '<label for="'.$key.'_coord_real" style="margin-right: 10px;">'.$label.':</label>';
				echo '<textarea name="' .$meta_name. '" id="' .$meta_name. '" cols="100" rows="8">'.$meta_value.'</textarea>';
			} elseif ( $key == '_png_image' ) {
				echo '<input type="hidden" name="' .$meta_name. '" id="' .$meta_name. '" value="' .$meta_value. '" />';
			} else {
				echo '<div style="display: flex; align-items: center; margin: 0 15px 15px 0; width: 100%">';
				echo '<label for="'.$key.'_coord_real" style="margin-right: 10px;">'.$label.':</label>';
				echo '<input type="text" name="' .$meta_name. '" id="' .$meta_name. '" value="' .$meta_value. '" />';
			}
			
			echo '</div>';
		}
		
		$canvas_active_object = isset($_COOKIE["canvas_active_object"]) ? $_COOKIE["canvas_active_object"] : '';
		$active_object_json = str_replace('\"', '"', $canvas_active_object);
		$obj_json = json_decode($active_object_json, true);
		
		$main_customize_img = get_field('main_customize_img',  $post->ID);
		$real_product_width = get_field('real_product_width',  $post->ID); // mm
		$real_product_height = get_field('real_product_height',  $post->ID); // mm
		$real_width_print = get_field('real_product_width_print',  $post->ID); // mm
		$real_height_print = get_field('real_product_height_print',  $post->ID); // mm
		
		$rect_height_print = $this->convert_mm_to_px($real_height_print); // mm
		$rect_width_print = $this->convert_mm_to_px($real_width_print); // mm
		
		$canvas_product_width = $this->convert_mm_to_px($real_product_width); // px
		$canvas_product_height = $this->convert_mm_to_px($real_product_height); // px
		
		$scaleRatio = get_post_meta( $post->ID, '_scaleRatio_coord_real', true );
		$r_rect_obg = get_post_meta( $post->ID, '_rect_obg_coord_real', true );
		
		?>
		<style>
			.main-canvas-wrap {
				max-width: 675px;
				width: 100%;
				min-height: 665px;
				height: 665px;
				margin: 0 auto;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				position: relative;
				background-size: contain;
				background-repeat: no-repeat;
				background-position: 50% 50%;
			}
			.canvas-container {
				border: 1px solid #A5A3B8;
			}
		</style>
		<div class="main-canvas-wrap">
		
			<canvas id="c_product_canvas_admin"
			        width="<?php echo $canvas_product_width; ?>"
			        height="<?php echo $canvas_product_height; ?>"
			        data-default_img="<?php echo $main_customize_img; ?>"
			        data-real_width="<?php echo $canvas_product_width; ?>"
			        data-real_height="<?php echo $canvas_product_height; ?>"
				<?php echo $rect_width_print ? 'data-print_width="'.$rect_width_print.'"' : ''; ?>
				<?php echo $rect_height_print ? 'data-print_height="'.$rect_height_print.'"' : ''; ?>
				<?php echo $r_rect_obg ? "data-r_acoords='".strval($r_rect_obg)."'" : ''; ?>
			>Your browser does not support the HTML5 canvas tag.</canvas>
		</div>
		<?php
		
	}
	
	
	function convert_mm_to_px($v) {
		// 1 mm = 3.779527559055 px
		return ($v * 3.779527559055);
	}
	
	
	
	
	use NewInstance;
	
}