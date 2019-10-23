<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'BrandListTable' ) ) {
    require_once( LKPP_CONNECTOR . '/includes/class-lkpp-brand-page.php' );
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
    <?php $brand_list_table = new BrandListTable(); ?>
    <h2>LKPP Brand List</h2>
    <button class="button lbrand-sync button-primary">Synchronize Brand</button>
    <form id="lkpp-brand-table" method="POST">
    <input type="hidden" name="page" value="lkpp-brand-page" />    
    <?php
        $brand_list_table->prepare_items();
        $brand_list_table->search_box('Search Brand', 'search_brand');
        $brand_list_table->display(); 
    ?>
    </form>
</div>