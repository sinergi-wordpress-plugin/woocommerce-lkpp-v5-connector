<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'ProductListTable' ) ) {
    require_once( LKPP_CONNECTOR . '/includes/class-lkpp-product-page.php' );
}   
?>
<style type="text/css">
    table.wp-list-table td.column-thumb img {
        margin: 0;
        width: auto;
        height: auto;
        max-width: 40px;
        max-height: 40px;
        vertical-align: middle;
    }
</style>
<div class="wrap">
    <?php $product_list_table = new ProductListTable(); ?>
    <h2>LKPP Product List</h2>
    <?php $product_list_table->views(); ?>
    <form id="lkpp-product-table" method="POST">
    <input type="hidden" name="page" value="lkpp-products" />    
    <?php
        $product_list_table->prepare_items();
        $product_list_table->search_box('Search SKU', 'search_sku');
        $product_list_table->display(); 
    ?>
    </form>
</div>