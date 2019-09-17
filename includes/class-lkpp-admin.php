<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Lkpp_Admin' ) ):
class Lkpp_Admin {

    public static $shop_order_columns = 1;

    /**
     * Register the hooks
     */
    public function __construct() {
        //add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        //add_action( 'admin_notices', array( $this, 'add_notices' ) );

        //add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts'), 11 );
        //add_action( 'admin_footer', array($this, 'variable_script') );

        // metaboxes
        //add_action( 'woocommerce_process_product_meta', array($this, 'save_product_warranty') );
        //add_action( 'woocommerce_process_product_meta', array($this, 'save_variation_warranty') );
        //add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'save_variation_warranty') );

        // order actions
        // add_filter( 'woocommerce_order_actions', array($this, 'add_order_action') );
        // add_action( 'woocommerce_order_action_generate_rma', array($this, 'redirect_order_to_rma_form') );

        // variable products support
        // add_action( 'woocommerce_product_after_variable_attributes', array($this, 'variables_panel'), 10, 3 );

        // Update request from the admin
        // add_action( 'admin_post_warranty_create', array($this, 'create_warranty') );
        // add_action( 'admin_post_warranty_delete', array($this, 'warranty_delete') );
        // add_action( 'admin_post_warranty_print', array($this, 'warranty_print') );

        // add_action( 'admin_post_warranty_upload_shipping_label', array($this, 'attach_shipping_label') );

        // return stock
        // add_action( 'admin_post_warranty_return_inventory', array($this, 'return_inventory') );

        // refund order item
        // add_action( 'admin_post_warranty_refund_item', array($this, 'refund_item') );

        // CSV Import
        // add_filter( 'woocommerce_csv_product_post_columns', array($this, 'csv_import_fields') );

        // bulk edit
        // add_action( 'admin_post_warranty_bulk_edit', array($this, 'bulk_edit') );

        // save settings
        //add_action( 'admin_post_wc_warranty_settings_update', array($this, 'update_settings') );

        //add_filter( 'manage_shop_order_posts_columns', array( $this, 'count_shop_order_columns' ), 1000 );
        //add_action( 'woocommerce_admin_order_actions_end', array($this, 'order_inline_edit_actions') );
        //add_action( 'admin_footer', array($this, 'order_inline_edit_template') );

        //add_action( 'woocommerce_ajax_add_order_item_meta', array( $this, 'add_line_item_warranty_meta' ), 10, 2 );
        //add_action( 'woocommerce_before_order_itemmeta', array( $this, 'maybe_render_addon_options' ), 10, 3 );
        //add_action( 'woocommerce_before_order_itemmeta', array( $this, 'render_order_item_warranty' ), 10, 3 );
        //add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_item_meta' ) );
        //add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_line_item_warranty_indices' ), 10, 2 );
        //add_action( 'woocommerce_saved_order_items', array( $this, 'save_line_item_warranty_indices' ), 9, 2 );
        //add_action( 'woocommerce_saved_order_items', array( $this, 'add_addon_price_to_line_item' ), 10, 2 );

        //add_action( 'woocommerce_order_item_meta_end', array( $this, 'render_order_item_warranty' ), 10, 3 );
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'panel_data_tab' ) );

        if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
            add_action( 'woocommerce_product_write_panels', array( $this, 'panel_add_custom_box' ) );
        } else {
            add_action( 'woocommerce_product_data_panels', array( $this, 'panel_add_custom_box' ) );
        }
    }

    /**
     * Adds a 'Warranty' tab to a product's data tabs
     */
    function panel_data_tab() {
        echo ' <li class="warranty_tab tax_options hide_if_external"><a href="#lkpp_product_data"><span>'. __('LKPP', 'woocommerce') .'</span></a></li>';
    }

    /**
     * Outputs the form for the Warranty data tab
     */
    function panel_add_custom_box() {
        global $post, $wpdb, $thepostid, $woocommerce;

        $lkpp_active = get_post_meta( $post->ID, 'lkpp_active', true );

        if (trim($lkpp_active) == '') {
            update_post_meta($post->ID, 'lkpp_active', 'not_active');
            $lkpp_active = 'not_active';
        }

        $lkpp_publish = get_post_meta( $post->ID, 'lkpp_publish', true );

        if (trim($lkpp_publish) == '') {
            update_post_meta($post->ID, 'lkpp_publish', 'unpublish');
            $warranty_type_value = 'unpublish';
        }

        $local_product = get_post_meta( $post->ID, 'local_product', true );

        if (trim($lkpp_active) == '') {
            update_post_meta($post->ID, 'local_product', 'no');
            $lkpp_active = 'no';
        }

        $tkdn = get_post_meta( $post->ID, 'tkdn', true );

        if (trim($tkdn) == '') {
            update_post_meta($post->ID, 'tkdn', '0');
            $tkdn = '0';
        }

        // $currency = get_woocommerce_currency_symbol();
        /* $inline = '
            var warranty_fields_toggled = false;
            $("#product_warranty_default").change(function() {

                if ($(this).is(":checked")) {
                    $(".warranty_field").attr("disabled", true);
                } else {
                    $(".warranty_field").attr("disabled", false);
                }

            }).change();

            $("#product_warranty_type").change(function() {
                $(".show_if_included_warranty, .show_if_addon_warranty").hide();

                if ($(this).val() == "included_warranty") {
                    $(".show_if_included_warranty").show();
                } else if ($(this).val() == "addon_warranty") {
                    $(".show_if_addon_warranty").show();
                }
            }).change();

            $("#included_warranty_length").change(function() {
                if ($(this).val() == "limited") {
                    $(".limited_warranty_length_field").show();
                } else {
                    $(".limited_warranty_length_field").hide();
                }
            }).change();

            var tmpl = "<tr>\
                            <td valign=\"middle\">\
                                <span class=\"input\"><b>+</b> '. $currency .'</span>\
                                <input type=\"text\" name=\"addon_warranty_amount[]\" class=\"input-text sized\" size=\"4\" value=\"\" />\
                            </td>\
                            <td valign=\"middle\">\
                                <input type=\"text\" class=\"input-text sized\" size=\"3\" name=\"addon_warranty_length_value[]\" value=\"\" />\
                                <select name=\"addon_warranty_length_duration[]\">\
                                    <option value=\"days\">'. __('Days', 'wc_warranty') .'</option>\
                                    <option value=\"weeks\">'. __('Weeks', 'wc_warranty') .'</option>\
                                    <option value=\"months\">'. __('Months', 'wc_warranty') .'</option>\
                                    <option value=\"years\">'. __('Years', 'wc_warranty') .'</option>\
                                </select>\
                            </td>\
                            <td><a class=\"button warranty_addon_remove\" href=\"#\">&times;</a></td>\
                        </tr>";

            $(".btn-add-warranty").click(function(e) {
                e.preventDefault();

                $("#warranty_addons").append(tmpl);
            });

            $(".warranty_addon_remove").live("click", function(e) {
                e.preventDefault();

                $(this).parents("tr").remove();
            });

            $("#variable_warranty_control").change(function() {
                if ($(this).val() == "variations") {
                    $(".hide_if_control_variations").hide();
                    $(".show_if_control_variations").show();
                } else {
                    $(".hide_if_control_variations").show();
                    $(".show_if_control_variations").hide();
                    $("#warranty_product_data :input[id!=variable_warranty_control]").change();
                }
            }).change();

            $("#variable_product_options").on("woocommerce_variations_added", function() {
                $("#variable_warranty_control").change();
            });

            $("#woocommerce-product-data").on("woocommerce_variations_loaded", function() {
                $("#variable_warranty_control").change();
            });
            '; */

        /* if ( function_exists('wc_enqueue_js') ) {
            wc_enqueue_js( $inline );
        } else {
            $woocommerce->add_inline_js( $inline );
        } */

        //$warranty_label = $warranty['label'];
        //$default_warranty = false;
        //$control_type   = 'parent';

        //$product = wc_get_product( $post->ID );

        /*if ( $product->is_type( 'variable' ) ) {
            $control_type = get_post_meta( $post->ID, '_warranty_control', true );
            if (! $control_type ) {
                $control_type = 'variations';
            }
        }*/

        //$default_warranty = isset( $warranty['default'] ) ? $warranty['default'] : false;

        /*if ( empty($warranty_label) ) {
            $warranty_label = __('Warranty', 'wc_warranty');
        }*/

        include WooCommerce_Lkpp_Connector::$base_path .'/templates/admin/product-panel.php';
    }
}
endif;

return new Lkpp_Admin();    