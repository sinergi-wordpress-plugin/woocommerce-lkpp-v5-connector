<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class ProductListTable extends WP_List_Table {
    
    /** Class constructor */
	public function __construct() {
        parent::__construct( [
		    'singular' => __( 'Product', 'woocommerce' ), //singular name of the listed records
		    'plural'   => __( 'Products', 'woocommerce' ), //plural name of the listed records
		    'ajax'     => true //should this table support ajax?
		] );
	}
    
    /**
     * Set default column for data table
     */
    function get_columns(){
        
        $columns = array();
        $columns['cb'] = '<input type="checkbox" />';
        $columns['thumb'] = '<span class="wc-image tips" data-tip="' . esc_attr__( 'Image', 'woocommerce' ) . '">' . __( 'Image', 'woocommerce' ) . '</span>';
        $columns['name'] = __( 'Name', 'woocommerce' );
        if ( wc_product_sku_enabled() ) {
            $columns['sku'] = __( 'SKU', 'woocommerce' );
        }
        $columns['lkpp_price'] = __( 'Harga LKPP', 'woocommerce' );
        $columns['lkpp_product_category'] = __( 'LKPP Product Category', 'woocommerce' );
        $columns['lkpp_product_brand'] = __( 'Brand', 'woocommerce' );
        $columns['lkpp_publish'] = __( 'Status', 'woocommerce' );
        
        return $columns;
      }
    
    /**
     * Get LKPP Active Product
     */  
    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = wc_get_products( array(
            'lkpp_active' => 'active'
        ) );
    }

    /**
     * Render row checkboxes
     */
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="lkpp_product[]" value="%s" />', $item->get_id()
        );    
    }

    /**
     * Render row image thumbnail
     */
    function column_thumb($item) {
        return '<a href="' . esc_url( get_edit_post_link( $item->get_id() ) ) . '">' . $item->get_image( 'woocommerce_thumbnail' ) . '</a>'; // WPCS: XSS ok.    
    }

    /**
     * Render name column
     */
    function column_name($item) {

        $product_name = '<strong>' . $item->get_name() . '</strong>';
        $actions = [
            'edit' => sprintf('<a href="%s"> Edit </a>', esc_url( get_edit_post_link( $item->get_id() ) ))
        ];

        return $product_name . $this->row_actions($actions);    
    }

    /**
     * Render SKU column
     */
    function column_sku($item) {

        return $item->get_sku();    
    }

    /**
     * Render LKPP Price column
     */
    function column_lkpp_price($item) {

        return get_post_meta($item->get_id(),'lkpp_price', true);    
    }

    /**
     * Render LKPP Category column
     */
    function column_lkpp_product_category($item) {

        $lkpp_product_category_id = get_post_meta( $item->get_id(), 'lkpp_product_category_id', true );
		if ( $lkpp_product_category_id ) {
		    $lkpp_categ = get_terms( array(
                'hide_empty' => false, // also retrieve terms which are not used yet
                'meta_query' => array( array(
                    'key'       => 'lkpp_product_category_id',
                    'value'     => $lkpp_product_category_id,
                    'compare'   => 'LIKE'
                        )
                    ),
                'taxonomy'  => 'lkpp_product_category',
                )
            );
            $lkpp_categ_name = $lkpp_categ[0]->name;
        }    
        return $lkpp_categ_name;    
    }

    /**
     * Render LKPP Brand column
     */
    function column_lkpp_product_brand($item) {

        $lkpp_brand_id = get_post_meta( $item->get_id(), 'lkpp_brand_id', true );
		if ( $lkpp_brand_id ) {
		    $lkpp_brand = get_terms( array(
                'hide_empty' => false, // also retrieve terms which are not used yet
                'meta_query' => array( array(
                    'key'       => 'lkpp_brand_id',
                    'value'     => $lkpp_brand_id,
                    'compare'   => 'LIKE'
                        )
                    ),
                'taxonomy'  => 'lkpp_product_brand',
                )
            );
            $lkpp_brand_name = $lkpp_brand[0]->name;
        }    
        return $lkpp_brand_name ;    
    }

    /**
     * Render LKPP Publish column
     */
    function column_lkpp_publish($item) {

        return get_post_meta($item->get_id(),'lkpp_publish', true);    
    }
}
