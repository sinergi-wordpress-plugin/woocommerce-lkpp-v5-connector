<style type="text/css">
    span.input {float: left; margin-top: 4px;}
    p.addon-row {margin-left: 25px;}
</style>
<div id="lkpp_product_data" class="panel woocommerce_options_panel">

    <!-- <div class="options_group show_if_variable">
        <p class="form-field">
            <label for="variable_warranty_control">
                <?php _e('Warranty Control', 'wc_warranty'); ?>
            </label>
            <select id="variable_warranty_control" name="variable_warranty_control">
                <option value="parent" <?php selected( $control_type, 'parent' ); ?>><?php _e('Define warranty for all variations', 'wc_warranty'); ?></option>
                <option value="variations" <?php selected( $control_type, 'variations' ); ?>><?php _e('Define warranty per variation', 'wc_warranty'); ?></option>
            </select>
        </p>
    </div> -->

    <div class="options_group grouping hide_if_control_variations">
        <p class="form-field lkpp_active_field">
            <label for="lkpp_active">
                <?php _e('LKPP Active', 'woocommerce'); ?>
            </label>
            <select id="lkpp_active" name="lkpp_active" class="select warranty_field">
                <option value="active" <?php if ($lkpp_active == 'active') echo 'selected'; ?>><?php _e('Active', 'woocommerce'); ?></option>
                <option value="inactive" <?php if ($lkpp_active == 'inactive') echo 'selected'; ?>><?php _e('Inactive', 'woocommerce'); ?></option>
            </select>
        </p>

        <p class="form-field lkpp_publish_field">
            <label for="lkpp_publish">
                <?php _e('LKPP Publish', 'woocommerce'); ?>
            </label>
            <select id="lkpp_publish" name="lkpp_publish" class="select warranty_field">
                <option value="publish" <?php if ($lkpp_active == 'publish') echo 'selected'; ?>><?php _e('Publish', 'woocommerce'); ?></option>
                <option value="unpublish" <?php if ($lkpp_active == 'unpublish') echo 'selected'; ?>><?php _e('Unpublish', 'woocommerce'); ?></option>
            </select>
        </p>

        <p class="form-field">
            <label for="lkpp_product_category_id">
                <?php _e('LKPP Product Category', 'woocommerce'); ?>
            </label>
            <select id="lkpp_product_category_id" name="lkpp_product_category_id" class="select warranty_field">
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
                                );
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
            <select id="lkpp_brand_id" name="lkpp_brand_id" class="select warranty_field">
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
                                );
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
            <select id="local_product" name="local_product" class="select warranty_field">
                <option value="yes" <?php if ($local_product == 'yes') echo 'selected'; ?>><?php _e('Yes', 'woocommerce'); ?></option>
                <option value="no" <?php if ($local_product == 'no') echo 'selected'; ?>><?php _e('No', 'woocommerce'); ?></option>
            </select>
        </p>

        <p class="form-field show_if_local_product show_if_addon_warranty"> 
            <label for="tkdn"><?php _e('Komposisi Komponen Dalam Negeri', 'woocommerce'); ?></label>

            <input type="text" name="tkdn" value="<?php echo esc_attr($tkdn); ?>" class="input-text sized tkdn" />
        </p>
        
    </div>

    <div class="options_group grouping hide_if_control_variations">
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

    <!-- <div class="options_group grouping show_if_addon_warranty hide_if_control_variations">
        <p class="form-field">
            <label for="addon_no_warranty">
                <?php _e( '"No Warranty" option', 'wc_warranty'); ?>
            </label>
            <input type="checkbox" name="addon_no_warranty" id="addon_no_warranty" value="yes" <?php if (isset($warranty['no_warranty_option']) && $warranty['no_warranty_option'] == 'yes') echo 'checked'; ?> class="checkbox warranty_field" />
        </p>

        <table class="widefat">
            <thead>
            <tr>
                <th><?php _e('Cost', 'wc_warranty'); ?></th>
                <th><?php _e('Duration', 'wc_warranty'); ?></th>
                <th width="50">&nbsp;</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th colspan="3">
                    <a href="#" class="button btn-add-warranty"><?php _e('Add Row', 'wc_warranty'); ?></a>
                </th>
            </tr>
            </tfoot>
            <tbody id="warranty_addons">
            <?php
            if ( isset($warranty['addons']) ) foreach ( $warranty['addons'] as $addon ):
                ?>
                <tr>
                    <td valign="middle">
                        <span class="input"><b>+</b> <?php echo $currency; ?></span>
                        <input type="text" name="addon_warranty_amount[]" class="input-text sized warranty_field" size="4" value="<?php echo $addon['amount']; ?>" />
                    </td>
                    <td valign="middle">
                        <input type="text" class="input-text sized warranty_field" size="3" name="addon_warranty_length_value[]" value="<?php echo $addon['value']; ?>" />
                        <select name="addon_warranty_length_duration[]" class=" warranty_field">
                            <option value="days" <?php if ($addon['duration'] == 'days') echo 'selected'; ?>><?php _e('Days', 'wc_warranty'); ?></option>
                            <option value="weeks" <?php if ($addon['duration'] == 'weeks') echo 'selected'; ?>><?php _e('Weeks', 'wc_warranty'); ?></option>
                            <option value="months" <?php if ($addon['duration'] == 'months') echo 'selected'; ?>><?php _e('Months', 'wc_warranty'); ?></option>
                            <option value="years" <?php if ($addon['duration'] == 'years') echo 'selected'; ?>><?php _e('Years', 'wc_warranty'); ?></option>
                        </select>
                    </td>
                    <td><a class="button warranty_addon_remove" href="#">&times;</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    </div> -->
</div>