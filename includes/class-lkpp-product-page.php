<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists( 'ProductListTable' ) ):
class ProductListTable extends WP_List_Table {
    
    /** Class constructor */
	public function __construct() {
        parent::__construct( [
		    'singular' => __( 'Product', 'woocommerce' ), //singular name of the listed records
		    'plural'   => __( 'Products', 'woocommerce' ), //plural name of the listed records
		    'ajax'     => false //should this table support ajax?
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
        $columns['lkpp_disc'] = __( 'Discount', 'woocommerce' );
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
     * Render data in table column
     */
    function column_default( $item, $column_name ) {
        switch( $column_name ) { 
          case 'thumb':
            return '<a href="' . esc_url( get_edit_post_link( $item->get_id() ) ) . '">' . $item->get_image( 'thumbnail' ) . '</a>'; // WPCS: XSS ok.
          //case 'author':
          //case 'isbn':
            //return $item[ $column_name ];
          //default:
            //return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
      }
}
endif;

return new ProductListTable();
