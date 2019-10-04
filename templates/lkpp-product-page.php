<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'ProductListTable' ) ) {
    require_once( LKPP_CONNECTOR . 'includes/class-lkpp-product-page.php' );
}

?>
<div class="wrap">
    <h2>LKPP Product List</h2>
    <div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
				    <form method="post">
                        <?php
                        $product_list_table = new ProductListTable();
						$product_list_table->prepare_items();
						$product_list_table->display(); ?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>