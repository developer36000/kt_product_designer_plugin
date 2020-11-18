<?php
 /**
  * Custom Admin Product Actions, Filters, Columns and Settings
  * */
namespace admin\product;
use includes\common\NewInstance;

class KTAdminProductActionsModel extends BaseAdminProductController {

	public function __construct() {
		parent::__construct();
		
		
		// Custom Product Row Action
		add_filter( 'post_row_actions', array( $this, 'kt_add_action_button'), 10, 2);
		add_filter( 'before_delete_post', array( $this, 'kt_before_product_delete'), 10, 2);
		
		// Custom Product column
		add_action( 'admin_head', array( $this, 'ktwc_product_column_css' ) );
		add_filter( "manage_{$this->post_product}_posts_columns", array( $this, 'ktwc_product_column' ), 4);
		add_filter( "manage_edit-{$this->post_product}_columns", array( $this, 'ktwc_remove_woo_columns' ) );
		add_filter( "manage_{$this->post_product}_posts_custom_column", array( $this, 'ktwc_fill_product_column' ), 5, 2);
		
		// Custom column filter by product default status slugs
		add_action( 'restrict_manage_posts', array( $this, 'ktwc_filter_product_meta_select' ), 10, 2 );
		add_filter( 'parse_query', array( $this, 'ktwc_sort_product_by_meta') );
		add_action( 'woocommerce_product_duplicate', array($this, 'update_product_duplicate_data'), 10, 2);

		// Custom product ajax action
		add_action( 'wp_ajax_make_as_default_product', array( $this, 'ajax_action') );
		add_action( 'wp_ajax_nopriv_make_as_default_product', array( $this, 'ajax_action') );
		
		// cron
		add_filter( 'cron_schedules', array( $this, 'cron_add_3_hour') );
		add_action( 'init', array( $this, 'ktwc_cron' ) );
		add_action( 'ktwc_remove_cutomize_product_event', array( $this, 'ktwc_remove_customize_action' ) );
		
	}
	
	
	/**
	 * Add new action to the row for products
	 * */
	public function kt_add_action_button( $actions, $post ) {
		
		if( get_post_type() === $this->post_product ) {
			$url = add_query_arg(
				array(
					'post_id' => $post->ID,
					'make_as_default_action' => 'make_as_default_product_action',
				)
			);
			$is_super_product = self::get_product_tab_meta('super_product', $post->ID);
			$post_status = get_post_status($post->ID);
			$actions['make_as_default'] = '<a class="make_as_default_action" href="#"
		data-post_id="'.$post->ID.'">'.($is_super_product ? 'Unmark as Default' : 'Mark as Default').'</a>';
			if ( $is_super_product && ($post_status == 'publish') ) {
				echo '<style>
					#post-'.$post->ID.' .row-actions span.trash { display: none !important; }
					#post-'.$post->ID.' .check-column { pointer-events: none; opacity: 0.4; }
				</style>';
			} else {
				echo '<style>
					#post-'.$post->ID.' .row-actions span.trash { display: inline-block !important; }
					#post-'.$post->ID.' .check-column { pointer-events: auto; opacity: 1; }
				</style>';
			}
		}
	
		return $actions;
	}
	public function ajax_action() {
		$post_id = $_POST['product_id'];
		$is_super_product = self::get_product_tab_meta('super_product', $post_id);
		$product_cat_obj = self::get_product_first_category_obj( $post_id );
		$product_cat_slug = $product_cat_obj->slug;
		$has_cat_default = false;
		$post_status = get_post_status( $post_id );
		
		// find another product in the same category
		$products = wc_get_products(array(
			'category' => array( $product_cat_slug ),
		));
		foreach ($products as $product) {
			if ( $post_id != $product->id ) {
				$is_super = self::get_product_tab_meta('super_product', $product->id);
				if ( $is_super ) {
					$has_cat_default = true;
					break;
				}
			}
		}
		
		// update as default
		if ( !$is_super_product && !$has_cat_default && ($post_status == "publish") ) {
			update_post_meta($post_id, 'super_product', true );
		} else if ( $is_super_product && !$has_cat_default ) {
			update_post_meta($post_id, 'super_product', false );
		}
		// send data to ajax
		$data = array(
			'product_cat_obj' => $post_status,
			'has_cat_default' => $has_cat_default,
			'is_super' => $is_super_product
		);
		echo json_encode($data);
		
		exit;
	}
	
	/**
	 * Change Column in the Product Admin Page
	 * */
	public function ktwc_product_column( $columns ) {
		$num = 1; // after which column under the account to insert new
		$new_columns = array(
			'default_product' => 'Default <span style="width: 15px;height: 15px;display: inline-block;" class="woocommerce-help-tip" data-tip="For the default product and it cannot be deleted."></span>'
		);
		return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
		
	}
	public function ktwc_remove_woo_columns( $columns ) {
		unset( $columns['featured'] ); // e.g unset( $cols['featured'] );
		return $columns;
	}
	public function ktwc_product_column_css() {
		if( get_current_screen()->base == 'edit')
			echo '<style type="text/css">.column-default_product{width:5%;}</style>';
	}
	// wp-admin/includes/class-wp-posts-list-table.php
	public function ktwc_fill_product_column( $colname, $post_id ){
		$super_product = self::get_product_tab_meta('super_product', $post_id);
	
		$mark_icon = '<img class="woocommerce-help-tip" width="40" height="40" data-tip="This is the default product and it cannot be deleted." src="data:image/png;base64,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"/>';
		$unmark_icon = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABmJLR0QA/wD/AP+gvaeTAAAD5klEQVRYhc3YXYhVVRQH8J+jEzUaheb4Ik3NSxBYYOlTnybTRKlFET5U9IEQGIpCkNPYQ2T2ZNNTEGESQkavWWqNBb3UU9I4OBopvQVR9EU4mtLDXtd75sw5955z7wj+YXM3a++91v/ufdZea22ucMybAx09WIOHMRiy0ziIo7jYrfJusBoTQeaWIHYm+gfxA1Z1aaNjrMd57MXSgvGl2IdzWNepkQUV512Du3FzrFmA3XgtfovwK57Fj/gYO/Cf9KfO4Buc7ZD3JVyLt/B3KDuBSZzCthp6tsWaydBxNnTuDhsdYQDH8ROeQl+FNb3R2mFh6DwdNgbqkuvDMckLr2szdwS3R/+daEK2o83a68PGMSUbUObFo0HscfxZMP4Q+qN/L56L/sJoQnZf9PsxXKDnDzwRREdKuMxCH37PGC3CEXwU/UexOfqDmnfh5hgTc4+00PcCfpOcsS3ukTwtv+XLMRT9O/BFFWWBL2ON0LE8N75Q8vC7qih7EVMF8qFQsrEGsTw2ho6hgrFT2JQXFn2D50vkRyTPu9AFwYuho+i451XVvUVy/QaGMY6VXRDLY2XofDAjmwzbLbEO/5p5Cd+AA9J3NFcYD51LMrLtYbs0LK6W4ma7u6sTLMF3eL7NvJHgMCvB6JG2eG9OvlP6XjZ0Se77aIsz8g2he2du/j7pE5vhB2sxbXZWsgJjKnwbFcgtyY1tCd0rcvJ+aRcfyArHpPxtLtGKXDt8jrdpbuNNOJmb1Is9eFczOtQh13CqtVKUyGMwdO8xO8GYklK7GedcJ/2/Fb9oRoe65GpjDJ/VmN+DD6SYnSXZzbFmcUja2Usoc5LblDtJnmRdclsVO8kyyUnW5I0dl1w8i1HpKlhfYiRLcqIGOZrXTD7N+lDBNUPzon61ooEsyffwbQ1yZRgNDneWTVgvhZvtGdnlCHVHlYe6R9ot3irVIQ1czmQhm2UXJgtFZec/ZqY9h6KR8rkL+KRDYk9Kn8QBuUiBq8L2DBTlfb2KnyuGsB/zOyTXsLdfccKqqu6qKf94DWLjWqf8i9RI+RtFU6vUKFs0Paa4aHopxmhfNG1So2iCXdLzxOKS8WHNsvOwdOHC+9GE7HD0y8pOYeNnvF6VHPUK9x1SxKGzwv0r6YKvvHsNDEjR4TSe1izIW6HO08cz0ilN4Ma65BpYJB33X1KsnpLuq5N4uYae7VL4mgwd06FzV9goRdUU62rJwwalHZovvXq9gTfbrB2V4u0r0h16TvP5bbqi/Y6wLozt03SYLJZJgb+rB8xusUo6unNStBmLdihkx7UI/FUwV4/o95v9iP4pvtblI/oVj/8Bdzvoz00s5FQAAAAASUVORK5CYII="/>';
		
		if( $colname === 'default_product' ){
			echo '<style>.woocommerce-help-tip {width: 40px;height: 40px;}</style>';
			echo $super_product ? $mark_icon : $unmark_icon;
		}
		
	}
	
	/**
	 * Filter by product default status slugs
	 * @return void
	 */
	public function ktwc_filter_product_meta_select() {
		global $typenow;
		// only add filter to 'product' type
		if ( $typenow == 'product' ) {
			$current_plugin = isset( $_GET['ADMIN_FILTER_STATUS_SLUG'] ) ? $_GET['ADMIN_FILTER_STATUS_SLUG']:''; //
			// Check if option has been selected ?>
			<select name="ADMIN_FILTER_STATUS_SLUG" id="status_slug">
				<option value="all" <?php selected( 'all', $current_plugin ); ?>><?php _e( 'Filter by default status', kt_textdomain ); ?></option>
				<option value="all" <?php selected( 'all', $current_plugin ); ?>><?php _e( 'All status', kt_textdomain ); ?></option>
				<option value="<?php echo 'show_super_product'; ?>" <?php selected( 'show_super_product', $current_plugin ); ?>>
					<?php echo _e( 'Only default', kt_textdomain ); ?></option>
				<option value="<?php echo 'show_super_product_first'; ?>" <?php selected( 'show_super_product_first', $current_plugin ); ?>>
					<?php echo _e( 'Default first', kt_textdomain ); ?></option>
				<option value="<?php echo 'hide_super_product'; ?>" <?php selected( 'hide_super_product',
					$current_plugin ); ?>>
					<?php echo _e( 'Hide Default', kt_textdomain ); ?></option>
			</select>
		<?php }
	}
	
	/**
	 * Update query by product default status for admin filter
	 * @return void
	 */
	public function ktwc_sort_product_by_meta( $query ) {
		global $pagenow;
		$current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
		$current_select = isset( $_GET['ADMIN_FILTER_STATUS_SLUG'] ) ? $_GET['ADMIN_FILTER_STATUS_SLUG']:'';
		if ( is_admin() &&
		     'edit.php' == $pagenow &&
		     'product' == $current_page &&
		     isset( $current_select ) &&
		     $current_select !== 'all'
		) {
			$query->query_vars['meta_key'] = 'super_product';
			$query->query_vars['orderby'] = $current_select == 'show_super_product_first' ? 'meta_value' : '';
			
			if ( $current_select == 'show_super_product' ) {
				$query->query_vars['meta_value'] = 'yes';
				$query->query_vars['meta_compare'] = '=';
			} else if ( $current_select == 'hide_super_product' ) {
				$query->query_vars['meta_value'] = 'yes';
				$query->query_vars['meta_compare'] = '!=';
			}
			
		}
		
	}
	
	/**
	 * Update product data in duplicate action in admin column
	 * */
	public function update_product_duplicate_data( $duplicate, $product ) {
		$product_id = $duplicate->get_id();
		update_post_meta($product_id, 'super_product', false );
	}
	
	 /**
	  * Delete Attachment from Media ( not for default product (super_product) )
	  * */
	public function kt_before_product_delete( $post_id, $post  ) {
		// Check if our post type is being deleted
		$post = get_post( $post_id );
		// if not, exit.
		if( !$post && $post->post_type !== $this->post_product )
			return;
		// Code that will do what we need when deleting
		
		// delete attachment
		$thumb_id = get_post_thumbnail_id( $post_id );
		$is_super_product = self::get_product_tab_meta('super_product', $post_id);
		if ( !$is_super_product ) {
			wp_delete_attachment( $thumb_id, true );
			delete_post_meta( $post_id,'super_product' );
		}
		
	}
	
	/*
	 * Remove New Customize Product from Server every 10 min use WP Cron
	 * */
	// register the 3 hour interval for cron event
	public function cron_add_3_hour( $schedules ) {
		$schedules['3_hour'] = array(
			'interval' => 60 * 60 * 3,
			'display' => 'Every 3 hour'
		);
		return $schedules;
	}
	// adds new cron task
	public function ktwc_cron() {
		// remove cron task
		// wp_clear_scheduled_hook( 'ktwc_remove_cutomize_product_event' );
		// kt_delete_product(568, true);
		if( ! wp_next_scheduled( 'ktwc_remove_cutomize_product_event' ) ) {
			wp_schedule_event( time(), '3_hour', 'ktwc_remove_cutomize_product_event');
		}
	}
	// add function to specified cron hook
	public function ktwc_remove_customize_action(){
		$empty_title = 'New Product';
		$args = array(
			'numberposts' => -1,
			'post_status' => 'publish'
		);
		$_products = wc_get_products( $args );
		
		foreach ( $_products as $product ) {
			$product_id = $product->get_id();
			$product_name = $product->name;
			$product_status = $product->status;
			$is_super_product = $product->get_meta( 'super_product' );
			if ( $product_name == $empty_title && $is_super_product !== 'yes'  )  {  /*$product_status == 'draft'*/
				kt_delete_product($product_id); // if force, you can add ==> true after ','
			}
		}
		
	}
	
	
	
	
	use NewInstance;
	
}