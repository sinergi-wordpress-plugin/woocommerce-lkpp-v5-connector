<style type="text/css">
    span.input {float: left; margin-top: 4px;}
    p.addon-row {margin-left: 25px;}
</style>
<div id="lkpp_product_data" class="panel woocommerce_options_panel">
<div class="options_group grouping">

    <?php

        woocommerce_wp_checkbox( array(
            'id' 		=> '_allow_personal_message',
            'label' 	=> __( 'Allow the customer to add a personal message', 'woocommerce' ),
            ) 
        );

        woocommerce_wp_text_input( array(
            'id'				=> '_valid_for_days',
            'label'				=> __( 'Gift card validity (in days)', 'woocommerce' ),
            'desc_tip'			=> 'true',
            'description'		=> __( 'Enter the number of days the gift card is valid for.', 'woocommerce' ),
            'type' 				=> 'number',
            'custom_attributes'	=> array(
                'min'	=> '1',
                'step'	=> '1',
                ),
            ) 
        );

    ?>
        
    <p class="form-field">
        <label for="lkpp_active">
            <?php _e('LKPP Active', 'woocommerce'); ?>
        </label>
        <select id="lkpp_active" name="lkpp_active" class="select">
            <option value="active" <?php if ($lkpp_active == 'active') echo 'selected'; ?>><?php _e('Active', 'woocommerce'); ?></option>
            <option value="inactive" <?php if ($lkpp_active == 'inactive') echo 'selected'; ?>><?php _e('Inactive', 'woocommerce'); ?></option>
        </select>
    </p>

    <p class="form-field">
        <label for="lkpp_publish">
            <?php _e('LKPP Publish', 'woocommerce'); ?>
        </label>
        <select id="lkpp_publish" name="lkpp_publish" class="select">
            <option value="publish" <?php if ($lkpp_active == 'publish') echo 'selected'; ?>><?php _e('Publish', 'woocommerce'); ?></option>
            <option value="unpublish" <?php if ($lkpp_active == 'unpublish') echo 'selected'; ?>><?php _e('Unpublish', 'woocommerce'); ?></option>
        </select>
    </p>

    <p class="form-field">
        <label for="lkpp_product_category_id">
                <?php _e('LKPP Product Category', 'woocommerce'); ?>
            </label>
            <select id="lkpp_product_category_id" name="lkpp_product_category_id" class="select">
                <?php
                    global $post; 
                    $lkpp_categ_id = get_post_meta($post->ID, 'lkpp_product_category_id', true);
                    if($lkpp_categ_id){
                        $lkpp_categ = get_terms(
                            array(
                                'hide_empty' => false, // also retrieve terms which are not used yet
                                'meta_query' => array(
                                    array(
                                       'key'       => 'lkpp_product_category_id',
                                       'value'     => $lkpp_categ_id,
                                       'compare'   => 'LIKE'
                                    )
                                ),
                                'taxonomy'  => 'lkpp_product_category',
                                )
                        );
                        $lkpp_categ_name = $lkpp_categ->name;
                        $html = '<option value="' . $lkpp_categ_id . '" selected="selected">' . $lkpp_categ_name . '</option>';
                    }
                    echo $html;    
                ?>
            </select>
        </p>

        <p class="form-field">
            <label for="lkpp_brand_id">
                <?php _e('LKPP Brand', 'woocommerce'); ?>
            </label>
            <select id="lkpp_brand_id" name="lkpp_brand_id" class="select">
                <?php
                    global $post; 
                    $lkpp_brand_id = get_post_meta($post->ID, 'lkpp_brand_id', true);
                    if($lkpp_brand_id){
                        $lkpp_brand = get_terms(
                            array(
                                'hide_empty' => false, // also retrieve terms which are not used yet
                                'meta_query' => array(
                                    array(
                                       'key'       => 'lkpp_brand_id',
                                       'value'     => $lkpp_brand_id,
                                       'compare'   => 'LIKE'
                                    )
                                ),
                                'taxonomy'  => 'lkpp_product_brand',
                                )
                        );
                        $lkpp_brand_name = $lkpp_brand->name;
                        $html = '<option value="' . $lkpp_brand_id . '" selected="selected">' . $lkpp_brand_name . '</option>';
                    }
                    echo $html;    
                ?>
            </select>
        </p>

        <p class="form-field local_product">
            <label for="local_product">
                <?php _e('Produk Lokal', 'woocommerce'); ?>
            </label>
            <select id="local_product" name="local_product" class="select">
                <option value="yes" <?php if ($local_product == 'yes') echo 'selected'; ?>><?php _e('Yes', 'woocommerce'); ?></option>
                <option value="no" <?php if ($local_product == 'no') echo 'selected'; ?>><?php _e('No', 'woocommerce'); ?></option>
            </select>
        </p>

        <p class="form-field show_if_local_product"> 
            <label for="tkdn"><?php _e('Komposisi Komponen Dalam Negeri', 'woocommerce'); ?></label>

            <input type="text" name="tkdn" value="<?php echo esc_attr($tkdn); ?>" class="input-text sized tkdn" />
        </p>
        
</div>

<div class="options_group grouping">
        
    <p class="form-field"> 
        <label for="lkpp_price"><?php _e('Harga LKPP ( Include PPN )', 'woocommerce'); ?></label>

        <input type="text" name="lkpp_price" value="<?php echo esc_attr($lkpp_price); ?>" class="input-text sized lkpp_price" />
    </p>
        
    <p class="form-field"> 
        <label for="lkpp_discount"><?php _e('Persentase Diskon', 'woocommerce'); ?></label>

        <input type="text" name="lkpp_discount" value="<?php echo esc_attr($lkpp_discount); ?>" class="input-text sized lkpp_discount" />
    </p>

    <p class="form-field"> 
        <label for="lkpp_stock"><?php _e('Stock', 'woocommerce'); ?></label>

        <input type="text" name="lkpp_stock" value="<?php echo esc_attr($lkpp_stock); ?>" class="input-text sized lkpp_stock" />
    </p>

    <p class="form-field"> 
        <label for="lkpp_price_valid"><?php _e('Harga berlaku hingga', 'woocommerce'); ?></label>

        <input type="text" name="lkpp_price_valid" value="<?php echo esc_attr($lkpp_price_valid); ?>" class="input-text sized lkpp_price_valid" />
    </p>
        
</div>

</div>