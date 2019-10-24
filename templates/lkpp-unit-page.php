<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'UnitListTable' ) ) {
    require_once( LKPP_CONNECTOR . '/includes/class-lkpp-unit-page.php' );
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
    <?php $unit_list_table = new UnitListTable(); ?>
    <h2>LKPP Unit List</h2>
    <button class="button lunit-sync button-primary">Synchronize Unit</button>
    <form id="lkpp-unit-table" method="POST">
    <input type="hidden" name="page" value="lkpp-unit-page" />    
    <?php
        $unit_list_table->prepare_items();
        $unit_list_table->search_box('Search Units', 'search_unit');
        $unit_list_table->display(); 
    ?>
    </form>
</div>