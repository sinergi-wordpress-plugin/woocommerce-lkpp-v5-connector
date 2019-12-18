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
     * Function to add LKPP Statuses filter link above the table
     */
    protected function get_views() { 

        $views = array();
        $current = ( !empty($_REQUEST['lkpp_status']) ? $_REQUEST['lkpp_status'] : 'all');

        //All link
        $class = ($current == 'all' ? ' class="current"' :'');
        $all_url = remove_query_arg('lkpp_status');
        $views['all'] = "<a href='{$all_url }' {$class} >All</a>";

        //Publish link
        $publish_url = add_query_arg('lkpp_status','publish');
        $class = ($current == 'publish' ? ' class="current"' :'');
        $views['publish'] = "<a href='{$publish_url}' {$class} >Publish</a>";

        //Unpublish link
        $unpub_url = add_query_arg('lkpp_status','unpublish');
        $class = ($current == 'unpublish' ? ' class="current"' :'');
        $views['unpublish'] = "<a href='{$unpub_url}' {$class} >Unpublish</a>";

        return $views;
    }

    /**
     * Function to add dropdown filter
     */
    /*function extra_tablenav($which){
        if ( $which == 'top' ){
            ?>
            <form id="product_table_filter" method="POST">
                <select id="lkpp-categ-filter" name="lkpp-categ-filter">
                    <option value="">Filter by LKPP Category</option>
                    <?php
                    $lkpp_categs = get_terms( 'lkpp_product_category', array(
                        'hide_empty' => false,
                    ) );
                    foreach($lkpp_categs as $categ){
                        $categ_id = $categ->term_id;
                        $categ_name = $categ->name;
                        $selected = $_REQUEST['lkpp-categ-filter'] == $categ_id ? 'selected="selected"' : '';
                        echo '<option value="' . $categ_id . '"' . $selected . '>' . $categ_name . '</option>';
                    }
	                ?>
                </select>
                <select id="lkpp-brand-filter" name="lkpp-brand-filter" data-placeholder="<?php _e( 'Search for LKPP Product Brand&hellip;', 'woocommerce' ); ?>">
                    <option value="">Filter by LKPP Brand</option>
                    <?php
                    $lkpp_brands = get_terms( 'lkpp_product_brand', array(
                        'hide_empty' => false,
                    ) );
                    foreach($lkpp_brands as $brand){
                        $brand_id = $brand->term_id;
                        $brand_name = $brand->name;
                        $selected = $_REQUEST['lkpp-brand-filter'] == $brand_id ? 'selected="selected"' : '';
                        echo '<option value="' . $brand_id . '"' . $selected . '>' . $brand_name . '</option>';
                    }
	                ?>
                </select>
                <input type="submit" name="Submit"  class="button action" value="Filter" />
            <?php
        }
    }*/

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
        $columns['web_price'] = __('Harga Web', 'woocommerce');
        $columns['lkpp_price'] = __( 'Harga LKPP', 'woocommerce' );
        $columns['lkpp_disc'] = __( 'Diskon LKPP', 'woocommerce' );
        $columns['lkpp_product_category'] = __( 'LKPP Category', 'woocommerce' );
        $columns['lkpp_product_brand'] = __( 'Brand', 'woocommerce' );
        $columns['lkpp_unit'] = __( 'Satuan Unit', 'woocommerce' );
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

        // Pagination handler
        $per_page     = $this->get_items_per_page( 'products_per_page', 20 );
        $current_page = $this->get_pagenum(); 

        $args = array(
            'lkpp_active'   => 'active',
            'status'        => 'publish',
            'paginate'      => true,
            'limit'         => $per_page,
            'page'          => $current_page
        );

        // Query filter handler
        if( isset($_GET['lkpp_status']) && !empty($_GET['lkpp_status']) ){
            if($_GET['lkpp_status'] == 'publish'){
                $args['lkpp_publish'] = 'publish';
            } elseif($_GET['lkpp_status'] == 'unpublish'){
                $args['lkpp_publish'] = 'unpublish';
            }
        }

        // Query dropdown filter handler
        if( isset($_POST['s']) && !empty($_POST['s']) ){
            $args['sku'] = $_POST['s'];
        }

        $product_list = wc_get_products( $args );

        $total_items  = $product_list->total;
        
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $this->items = $product_list->products;

        $this->process_bulk_action();   
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
     * Render Web Price column
     */
    function column_web_price($item) {
        $web_price_exc = $item->get_regular_price();
        $web_price_inc = $web_price_exc + ($web_price_exc * 10/100);
        $column_val = 'Rp ' . number_format ( (float)$web_price_inc , 0 , "," , "." );
        return $column_val;    
    }

    /**
     * Render LKPP Price column
     */
    function column_lkpp_price($item) {
        $lkpp_price = get_post_meta($item->get_id(),'lkpp_price', true);
        $column_val = 'Rp ' . number_format ( (float)$lkpp_price , 0 , "," , "." );
        return $column_val;    
    }

    /**
     * Render LKPP Price column
     */
    function column_lkpp_disc($item) {
        return get_post_meta($item->get_id(),'lkpp_disc', true) . '%';    
    }

    /**
     * Render LKPP Category column
     */
    function column_lkpp_product_category($item) {

        $lkpp_product_category_id = get_post_meta( $item->get_id(), 'lkpp_categ_id', true );
		if ( $lkpp_product_category_id ) {
		    $lkpp_categ = get_terms( array(
                'hide_empty' => false, // also retrieve terms which are not used yet
                'meta_query' => array( array(
                    'key'       => 'lkpp_categ_id',
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
        $term_list = wp_get_post_terms($item->get_id(), 'product_brand');
        $term_name = $term_list[0]->name;
        /*$lkpp_brand_id = get_post_meta( $item->get_id(), 'lkpp_brand_id', true );
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
        }*/    
        return $term_name ;    
    }

    /**
     * Render LKPP Unit column
     */
    function column_lkpp_unit($item) {

        $lkpp_unit_id = get_post_meta( $item->get_id(), 'lkpp_unit_id', true );
		if ( $lkpp_unit_id ) {
		    $lkpp_unit = get_terms( array(
                'hide_empty' => false, // also retrieve terms which are not used yet
                'meta_query' => array( array(
                    'key'       => 'lkpp_unit_id',
                    'value'     => $lkpp_unit_id,
                    'compare'   => 'LIKE'
                        )
                    ),
                'taxonomy'  => 'lkpp_unit',
                )
            );
            $lkpp_unit_name = $lkpp_unit[0]->name;
        }    
        return $lkpp_unit_name ;    
    }

    /**
     * Render LKPP Publish column
     */
    function column_lkpp_publish($item) {

        return get_post_meta($item->get_id(),'lkpp_publish', true);    
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-publish'      => 'Publish',
            'bulk-unpublish'    => 'Unpublish',
            'bulk-inactive'     => 'Inactive'
        ];
        
        return $actions;
    }

    public function process_bulk_action() {

        if( ( isset($_POST['action']) && !empty($_POST['action']) ) || ( isset($_POST['action2']) && !empty($_POST['action2']) ) ){
            if( ($_POST['action'] == 'bulk-publish') || ($_POST['action2'] == 'bulk-publish') ) {
                $pub_ids = esc_sql( $_POST['lkpp_product'] );
      
                // loop over the array of record IDs and delete them
                foreach ( $pub_ids as $id ) {
                    update_post_meta( $id, 'lkpp_publish', 'publish' );
                }
      
                wp_redirect( admin_url('admin.php?page=lkpp-products') );
                exit;

            } elseif( ($_POST['action'] == 'bulk-unpublish') || ($_POST['action2'] == 'bulk-unpublish') ){
                $unpub_ids = $_POST['lkpp_product'];
      
                // loop over the array of record IDs and delete them
                foreach ( $unpub_ids as $id ) {
                    update_post_meta( $id, 'lkpp_publish', 'unpublish' );
                }
      
                wp_redirect( admin_url('admin.php?page=lkpp-products') );
                exit; 

            } elseif( ($_POST['action'] == 'bulk-inactive') || ($_POST['action2'] == 'bulk-inactive') ){
                $inactive_ids = esc_sql( $_POST['lkpp_product'] );
      
                // loop over the array of record IDs and delete them
                foreach ( $inactive_ids as $id ) {
                    update_post_meta( $id, 'lkpp_active', 'inactive' );
                }
      
                wp_redirect( admin_url('admin.php?page=lkpp-products') );
                exit;
            }
        }
    }

}
