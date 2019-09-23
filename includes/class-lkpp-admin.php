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
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'lkpp_product_tabs' ) );

        if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
            add_filter( 'woocommerce_product_data_tabs', array( $this, 'lkpp_options_product_tab_content' ) );
        } else {
            add_filter( 'woocommerce_product_data_panels', array( $this, 'lkpp_options_product_tab_content' ) );
        }
    }

    /**
     * Add a custom product tab.
     */
    function lkpp_product_tabs( $tabs) {

	    $tabs['lkpp'] = array(
		    'label'		=> __( 'LKPP', 'woocommerce' ),
		    'target'	=> 'lkpp_data_options',
		    'class'		=> array( 'show_if_simple', 'show_if_variable'  ),
	    );

	    return $tabs;

    }

    /**
     * Contents of the gift card options product tab.
     */
    function lkpp_options_product_tab_content() {

	    global $post;
	
	    // Note the 'id' attribute needs to match the 'target' parameter set above
        ?>
        <div id='lkpp_data_options' class='panel woocommerce_options_panel'>
            <?php ?>
            <div class='options_group'>
                <?php

                    woocommerce_wp_select( array( 
                        'id'      => '_lkpp_active', 
                        'label'   => __( 'LKPP Active', 'woocommerce' ), 
                        'options' => array(
                            'active'   => __( 'Active', 'woocommerce' ),
                            'inactive'   => __( 'Inactive', 'woocommerce' )
                            )
                        )
                    );

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
            </div>

        </div>
        <?php

    }
}
endif;

return new Lkpp_Admin();    