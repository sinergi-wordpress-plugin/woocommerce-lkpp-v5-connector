<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Lkpp_Admin' ) ):
class Lkpp_Admin {

    public static $shop_order_columns = 1;

    /**
     * Register the hooks
     */
    public function __construct() {
        //add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        //add_action( 'admin_notices', array( $this, 'add_notices' ) );

        add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts'), 11 );
        add_action( 'wp_ajax_lkppgetcateg', 'lkpp_get_categ_callback' );
        //add_action( 'admin_footer', array($this, 'variable_script') );

        // metaboxes
        //add_action( 'woocommerce_process_product_meta', array($this, 'save_product_warranty') );
        //add_action( 'woocommerce_process_product_meta', array($this, 'save_variation_warranty') );
        //add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'save_variation_warranty') );

        // order actions
        // add_filter( 'woocommerce_order_actions', array($this, 'add_order_action') );
        // add_action( 'woocommerce_order_action_generate_rma', array($this, 'redirect_order_to_rma_form') );

        // variable products support
        // add_action( 'woocommerce_product_after_variable_attributes', array($this, 'variables_panel'), 10, 3 );

        // Update request from the admin
        // add_action( 'admin_post_warranty_create', array($this, 'create_warranty') );
        // add_action( 'admin_post_warranty_delete', array($this, 'warranty_delete') );
        // add_action( 'admin_post_warranty_print', array($this, 'warranty_print') );

        // add_action( 'admin_post_warranty_upload_shipping_label', array($this, 'attach_shipping_label') );

        // return stock
        // add_action( 'admin_post_warranty_return_inventory', array($this, 'return_inventory') );

        // refund order item
        // add_action( 'admin_post_warranty_refund_item', array($this, 'refund_item') );

        // CSV Import
        // add_filter( 'woocommerce_csv_product_post_columns', array($this, 'csv_import_fields') );

        // bulk edit
        // add_action( 'admin_post_warranty_bulk_edit', array($this, 'bulk_edit') );

        // save settings
        //add_action( 'admin_post_wc_warranty_settings_update', array($this, 'update_settings') );

        //add_filter( 'manage_shop_order_posts_columns', array( $this, 'count_shop_order_columns' ), 1000 );
        //add_action( 'woocommerce_admin_order_actions_end', array($this, 'order_inline_edit_actions') );
        //add_action( 'admin_footer', array($this, 'order_inline_edit_template') );

        //add_action( 'woocommerce_ajax_add_order_item_meta', array( $this, 'add_line_item_warranty_meta' ), 10, 2 );
        //add_action( 'woocommerce_before_order_itemmeta', array( $this, 'maybe_render_addon_options' ), 10, 3 );
        //add_action( 'woocommerce_before_order_itemmeta', array( $this, 'render_order_item_warranty' ), 10, 3 );
        //add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_item_meta' ) );
        //add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_line_item_warranty_indices' ), 10, 2 );
        //add_action( 'woocommerce_saved_order_items', array( $this, 'save_line_item_warranty_indices' ), 9, 2 );
        //add_action( 'woocommerce_saved_order_items', array( $this, 'add_addon_price_to_line_item' ), 10, 2 );

        //add_action( 'woocommerce_order_item_meta_end', array( $this, 'render_order_item_warranty' ), 10, 3 );
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'lkpp_product_tabs' ) );

        if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
            add_filter( 'woocommerce_product_data_tabs', array( $this, 'lkpp_options_product_tab_content' ) );
        } else {
            add_filter( 'woocommerce_product_data_panels', array( $this, 'lkpp_options_product_tab_content' ) );
        }
    }

    /**
     * Add a custom product tab.
     */
    function lkpp_product_tabs( $tabs) {

	    $tabs['lkpp'] = array(
		    'label'		=> __( 'LKPP', 'woocommerce' ),
		    'target'	=> 'lkpp_data_options',
		    'class'		=> array( 'show_if_simple', 'show_if_variable'  ),
	    );

	    return $tabs;

    }

    /**
     * Contents of the gift card options product tab.
     */
    function lkpp_options_product_tab_content() {

        global $post, $wpdb, $thepostid, $woocommerce;
        
        $lkpp_active = get_post_meta( $post->ID, 'lkpp_active', true );

        if (trim($lkpp_active) == '') {
            update_post_meta($post->ID, 'lkpp_active', 'inactive');
            $lkpp_active = 'inactive';
        }

        $lkpp_publish = get_post_meta( $post->ID, 'lkpp_publish', true );

        if (trim($lkpp_publish) == '') {
            update_post_meta($post->ID, 'lkpp_publish', 'unpublish');
            $warranty_type_value = 'unpublish';
        }

        $local_product = get_post_meta( $post->ID, 'local_product', true );

        if (trim($lkpp_active) == '') {
            update_post_meta($post->ID, 'local_product', 'no');
            $lkpp_active = 'no';
        }

        $tkdn = get_post_meta( $post->ID, 'tkdn', true );

        if (trim($tkdn) == '') {
            update_post_meta($post->ID, 'tkdn', '0');
            $tkdn = '0';
        }
	
	    // Note the 'id' attribute needs to match the 'target' parameter set above
        ?>
        <div id='lkpp_data_options' class='panel woocommerce_options_panel'>
            <?php ?>
            <div class='options_group'>
                <?php

                    woocommerce_wp_select( array( 
                        'id'      => 'lkpp_active', 
                        'label'   => __( 'LKPP Active', 'woocommerce' ), 
                        'options' => array(
                            'active'   => __( 'Active', 'woocommerce' ),
                            'inactive'   => __( 'Inactive', 'woocommerce' )
                            )
                        )
                    );

                    woocommerce_wp_select( array( 
                        'id'      => 'lkpp_publish', 
                        'label'   => __( 'LKPP Publish', 'woocommerce' ), 
                        'options' => array(
                            'publish'   => __( 'Publish', 'woocommerce' ),
                            'unpublish'   => __( 'Unpublish', 'woocommerce' )
                            )
                        )
                    );
                ?>
                <p class="form-field lkpp_product_category_id">
                    <label for="lkpp_product_category_id"><?php _e( 'LKPP Product Category', 'woocommerce' ); ?></label>
                    <select id="lkpp_product_category_id" name="lkpp_product_category_id[]" data-placeholder="<?php _e( 'Search for LKPP Product Category&hellip;', 'woocommerce' ); ?>" style="width:99%;max-width:25em;">
	                    <?php
        
                            $lkpp_product_category_id = get_post_meta( $post->ID, 'lkpp_product_category_id', true );
		                    if ( $lkpp_product_category_id ) {

				                $lkpp_categ = get_terms(
                                    array(
                                        'hide_empty' => false, // also retrieve terms which are not used yet
                                        'meta_query' => array(
                                            array(
                                               'key'       => 'lkpp_product_category_id',
                                               'value'     => $lkpp_product_category_id,
                                               'compare'   => 'LIKE'
                                            )
                                        ),
                                        'taxonomy'  => 'lkpp_product_category',
                                        )
                                );
                                $lkpp_categ_name = $lkpp_categ[0]->name;
				                echo '<option value="' . $lkpp_product_category_id . '" selected="selected">' . $lkpp_categ_name . '</option>';
			                    
		                    }
	                    ?>
                    </select> 
                </p>

                <p class="form-field lkpp_brand_id">
                    <label for="lkpp_brand_id"><?php _e( 'LKPP Product Brand', 'woocommerce' ); ?></label>
                    <select id="lkpp_brand_id" name="lkpp_brand_id[]" data-placeholder="<?php _e( 'Search for LKPP Product Brand&hellip;', 'woocommerce' ); ?>" style="width:99%;max-width:25em;">
	                    <?php
        
                            $lkpp_brand_id = get_post_meta( $post->ID, 'lkpp_brand_id', true );
		                    if ( $lkpp_brand_id ) {

				                $lkpp_brand = get_terms(
                                    array(
                                        'hide_empty' => false, // also retrieve terms which are not used yet
                                        'meta_query' => array(
                                            array(
                                               'key'       => 'lkpp_brand_id',
                                               'value'     => $lkpp_brand_id,
                                               'compare'   => 'LIKE'
                                            )
                                        ),
                                        'taxonomy'  => 'lkpp_product_brand',
                                        )
                                );
                                $lkpp_brand_name = $lkpp_brand->name;
				                echo '<option value="' . $lkpp_product_brand . '" selected="selected">' . $lkpp_brand_name . '</option>';
			                    
		                    }
	                    ?>
                    </select> 
                </p>
                <?php 
                
                ?>
            </div>

        </div>
        <?php

    }

    /**
     * Load Select 2 and JS Script.
     */
    function admin_scripts(){
 
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
     
        // please create also an empty JS file in your theme directory and include it too
        wp_enqueue_script('lkpp_admin', plugin_dir_path( __FILE__ ) . 'assets/js/admin.js', array( 'jquery', 'select2' ) ); 
     
    }

    /**
     * LKPP Get Product Categ Ajax Handler.
     */
    function lkpp_get_categ_callback(){
 
        // we will pass post IDs and titles to this array
        $return = array();
     
        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
        $search_results = new WP_Term_Query( array( 
            'name__like'=> $_GET['q'], // the search query
            'taxonomy' => 'lkpp_product_category', // if you don't want drafts to be returned
            'hide_empty' => false,
            'fields' => 'all'
        ) );

        if( ! empty( $search_results->terms ) ) {
            foreach ( $search_results->terms as $term ) {
                $lkpp_categ_name = $term->name;
                $lkpp_categ_id = get_term_meta($term->term_id, 'lkpp_product_category_id', true);
                $return[] = array( $lkpp_categ_id, $lkpp_categ_name );
            }
        }
        echo json_encode( $return );
        die;
    }

}
endif;

return new Lkpp_Admin();    