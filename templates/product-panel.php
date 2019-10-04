<script>
    jQuery(document).ready(function($){
        "use strict";

        // multiple select with AJAX search
        $('#lkpp_product_category_id').select2({
            ajax: {
                url: ajaxurl, // AJAX URL is predefined in WordPress admin
                dataType: 'json',
                delay: 250, // delay in ms while typing when to perform a AJAX search
                data: function (params) {
                    return {
                        q: params.term, // search query
                        action: 'lkppgetcateg' // AJAX action for admin-ajax.php
                    };
                },
                processResults: function( data ) {
                    var options = [];
                    if ( data ) {

                        // data is the array of arrays, and each of them contains ID and the Label of the option
                        $.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
                            options.push( { id: text[0], text: text[1]  } );
                        });

                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 3 // the minimum of symbols to input before perform a search
        });

        // multiple select with AJAX search
        $('#lkpp_brand_id').select2({
            ajax: {
                url: ajaxurl, // AJAX URL is predefined in WordPress admin
                dataType: 'json',
                delay: 250, // delay in ms while typing when to perform a AJAX search
                data: function (params) {
                    return {
                        q: params.term, // search query
                        action: 'lkppgetbrand' // AJAX action for admin-ajax.php
                    };
                },
                processResults: function( data ) {
                    var options = [];
                    if ( data ) {

                        // data is the array of arrays, and each of them contains ID and the Label of the option
                        $.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
                            options.push( { id: text[0], text: text[1]  } );
                        });

                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 3 // the minimum of symbols to input before perform a search
        });

    // render datepicker
    $('.lkpp_expired_date').datepicker({
        dateFormat : 'yy-mm-dd'
    });

    // Hide or Show tkdn field
    $("#local_product").change(function() {
        $(".tkdn_field").hide();

        if ($(this).val() == "yes") {
            $(".tkdn_field").show();
        }
    }).change();

    // Calculate LKPP Disc Percentage when LKPP price changed
    $("#lkpp_price").change(function() {

        $lkpp_price_inc = $(this).val();
        $web_price = $("#_regular_price").val();
        $lkpp_price_exc = Math.round($lkpp_price_inc - ($lkpp_price_inc * (10/100)));
        $lkpp_disc = 100 - Math.round(($lkpp_price_exc/$web_price) * 100);
        document.getElementById('lkpp_disc').value = $lkpp_disc; 

    }).change();

    // Calculate LKPP Disc Percentage when Web price changed
    $("#_regular_price").change(function() {

        $web_price = $(this).val();
        $lkpp_price_inc = $("#lkpp_price").val();
        $lkpp_price_exc = Math.round($lkpp_price_inc - ($lkpp_price_inc * (10/100)));
        $lkpp_disc = 100 - Math.round(($lkpp_price_exc/$web_price) * 100);
        document.getElementById('lkpp_disc').value = $lkpp_disc; 

    }).change();
});
</script>
<div id='lkpp_data_options' class='panel woocommerce_options_panel'>
    <div class='options_group'>
        <?php
            woocommerce_wp_select( array( 
                'id'      => 'lkpp_active', 
                'label'   => __( 'LKPP Active', 'woocommerce' ), 
                'options' => array(
                    'active'   => __( 'Active', 'woocommerce' ),
                    'inactive'   => __( 'Inactive', 'woocommerce' )
                    )
                )
            );

            woocommerce_wp_select( array( 
                'id'      => 'lkpp_publish', 
                'label'   => __( 'LKPP Publish', 'woocommerce' ), 
                'options' => array(
                    'publish'   => __( 'Publish', 'woocommerce' ),
                    'unpublish'   => __( 'Unpublish', 'woocommerce' )
                    )
                )
            );
        ?>
        <p class="form-field lkpp_product_category_id">
            <label for="lkpp_product_category_id"><?php _e( 'LKPP Product Category', 'woocommerce' ); ?></label>
            <select id="lkpp_product_category_id" name="lkpp_product_category_id" data-placeholder="<?php _e( 'Search for LKPP Product Category&hellip;', 'woocommerce' ); ?>" style="width:50%;max-width:15em;">
	            <?php
                    $lkpp_product_category_id = get_post_meta( $post->ID, 'lkpp_product_category_id', true );
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
				        echo '<option value="' . $lkpp_product_category_id . '" selected="selected">' . $lkpp_categ_name . '</option>';    
		            }
	            ?>
            </select> 
        </p>

        <p class="form-field lkpp_brand_id">
            <label for="lkpp_brand_id"><?php _e( 'LKPP Product Brand', 'woocommerce' ); ?></label>
            <select id="lkpp_brand_id" name="lkpp_brand_id" data-placeholder="<?php _e( 'Search for LKPP Product Brand&hellip;', 'woocommerce' ); ?>" style="width:50%;max-width:15em;">
	            <?php
                    $lkpp_brand_id = get_post_meta( $post->ID, 'lkpp_brand_id', true );
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
				        echo '<option value="' . $lkpp_brand_id . '" selected="selected">' . $lkpp_brand_name . '</option>';        
		            }
	            ?>
            </select> 
        </p>
        <?php 
            woocommerce_wp_select( array( 
                'id'      => 'local_product', 
                'label'   => __( 'Produk Lokal', 'woocommerce' ), 
                'options' => array(
                    'yes'   => __( 'Yes', 'woocommerce' ),
                    'no'   => __( 'No', 'woocommerce' )
                    )
                )
            );

            woocommerce_wp_text_input( array( 
                'id'          => 'tkdn', 
                'label'       => __( 'Tingkat Komponen Dalam Negeri (%)', 'woocommerce' ), 
                'placeholder' => 'Input nilai persentase tingkat komponen dalam negeri'
                )
            );
        ?>
    </div>

    <div class='options_group'>
        <?php 
            woocommerce_wp_text_input( array( 
                'id'          => 'lkpp_price', 
                'label'       => __( 'Harga LKPP (Inc PPN)', 'woocommerce' ), 
                )
            );

            woocommerce_wp_text_input( array( 
                'id'          => 'lkpp_disc', 
                'label'       => __( 'persentase Diskon', 'woocommerce' ),
                'custom_attributes' => array( 'disabled' => true) 
                )
            );

            woocommerce_wp_text_input( array( 
                'id'          => 'lkpp_stock', 
                'label'       => __( 'Stock LKPP', 'woocommerce' ), 
                )
            );
        ?>
        <p class="form-field lkpp_expired_date">
            <label for="lkpp_expired_date"><?php _e( 'Harga Berlaku Hingga', 'woocommerce' ); ?></label>
            <input type="text" id="lkpp_expired_date" name="lkpp_expired_date" value="<?php echo esc_attr($lkpp_expired_date); ?>" class="lkpp_expired_date" />
        </p>
    </div>

</div>