<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class UnitListTable extends WP_List_Table {
    
    /** Class constructor */
	public function __construct() {
        parent::__construct( [
		    'singular' => __( 'Unit', 'woocommerce' ), //singular name of the listed records
		    'plural'   => __( 'Units', 'woocommerce' ), //plural name of the listed records
		    'ajax'     => true //should this table support ajax?
        ] );
    }

    /**
     * Set default column for data table
     */
    function get_columns(){
        
        $columns = array();
        $columns['unit_id'] = __( 'ID', 'woocommerce' );
        $columns['unit_name'] = __( 'Unit Name', 'woocommerce' );
        
        return $columns;
    }
    
    /**
     * Get LKPP Units
     */  
    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // Pagination handler
        $per_page     = $this->get_items_per_page( 'unit_per_page', 20 );
        $current_page = $this->get_pagenum(); 
        $offset = $per_page * ( $current_page - 1);

        $args = array(
            'taxonomy'      => 'lkpp_unit',
            'number'        => $per_page,
            'offset'        => $offset,
            'hide_empty'    => false
        );

        // Query dropdown filter handler
        if( isset($_POST['s']) && !empty($_POST['s']) ){
            $args['name__like'] = $_POST['s'];
        }

        $term_query = new WP_Term_Query($args);
        $lkpp_units = $term_query->terms;

        $total_items  = count( get_terms( 'lkpp_unit', array('hide_empty'=>'0') ));
        
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $this->items = $lkpp_units;
  
    }

    /**
     * Render Unit ID column
     */
    function column_unit_id($item) {

        $unit_id = get_term_meta($item->term_id, 'lkpp_unit_id', true);

        return $unit_id;    
    }

    /**
     * Render LKPP Unit name column
     */
    function column_unit_name($item) {

        return $item->name;    
    }

}
