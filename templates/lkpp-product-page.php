<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'ProductListTable' ) ) {
    require_once( LKPP_CONNECTOR . 'includes/class-lkpp-product-page.php' );
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
    <h2>LKPP Product List</h2>
    <form id="lkpp-product-table" method="GET">
    <?php
        $product_list_table = new ProductListTable();
		$product_list_table->prepare_items();
        $product_list_table->display(); 
    ?>
    </form>
</div>