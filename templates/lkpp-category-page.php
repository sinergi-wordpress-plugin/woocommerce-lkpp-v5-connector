<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'CategoryListTable' ) ) {
    require_once( LKPP_CONNECTOR . '/includes/class-lkpp-category-page.php' );
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
    <?php $categ_list_table = new CategoryListTable(); ?>
    <h2>LKPP Category List</h2>
    <button class="button lcateg-sync button-primary">Synchronize Category</button>
    <form id="lkpp-categ-table" method="POST">
    <input type="hidden" name="page" value="lkpp-categ-page" />    
    <?php
        $categ_list_table->prepare_items();
        $categ_list_table->search_box('Search Category', 'search_categ');
        $categ_list_table->display(); 
    ?>
    </form>
</div>