<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/sinergi-wordpress-plugin
 * @since             0.0.2
 * @package           woocommerce-lkpp-v5-connector
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce LKPP E-Catalogue V5 Connector
 * Plugin URI:        https://github.com/sinergi-wordpress-plugin/woocommerce-lkpp-v5-connector
 * Description:       Woocommerce connector for LKPP E-Catalogue V5
 * Version:           0.0.2
 * Author:            Sinergi Creative
 * Author URI:        https://github.com/sinergi-wordpress-plugin
 * License:           GPL-3.0
 * License URI:       https://github.com/sinergi-wordpress-plugin/woocommerce-lkpp-v5-connector/blob/master/LICENSE
 * Text Domain:       woocommerce-lkpp-v5-connector
 * Domain Path:       /languages
 *
 * WC requires at least: 3.5.0
 * WC tested up to: 3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    define( 'LKPP_CONNECTOR', plugin_dir_path( __FILE__ ) );
    
    add_action( 'after_setup_theme', 'create_lkpp_category_taxonomy');
    add_action( 'after_setup_theme', 'create_lkpp_brand_taxonomy');
    add_action( 'init', 'render_panel');
    add_action( 'admin_enqueue_scripts', 'admin_scripts');
    add_action( 'wp_ajax_lkppgetcateg', 'lkpp_get_categ_callback');
    add_action( 'wp_ajax_lkppgetbrand', 'lkpp_get_brand_callback');

    function render_panel() {
        add_filter( 'woocommerce_product_data_tabs', 'lkpp_product_tabs');

        if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
            add_filter( 'woocommerce_product_data_tabs', 'lkpp_options_product_tab_content');
        } else {
            add_filter( 'woocommerce_product_data_panels', 'lkpp_options_product_tab_content');
        }
    }

    /**
    * Add LKPP product category taxonomies
    *
    * @since 0.0.2
    */
 
    function create_lkpp_category_taxonomy() {
 
        // Labels part for the GUI
        $labels = array(
            'name' => _x( 'LKPP Product Categories', 'taxonomy general name' ),
            'singular_name' => _x( 'LKPP Product Category', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search LKPP Product Categories' ),
            'popular_items' => null,
            'all_items' => __( 'All LKPP Product Categories' ),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => null, 
            'update_item' => null,
            'add_new_item' => null,
            'new_item_name' => null,
            'separate_items_with_commas' => __( 'Separate topics with commas' ),
            'add_or_remove_items' => __( 'Add or remove topics' ),
            'choose_from_most_used' => null,
            'menu_name' => __( 'LKPP Product Categories' ),
        ); 
 
        // set taxonomy option data
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => true,
        );

        // register taxonomy to woocommerce product object
        register_taxonomy( 'lkpp_product_category', 'product', $args );
        register_taxonomy_for_object_type( 'lkpp_product_category', 'product' );
    }

    /**
    * Add LKPP product brand taxonomies
    *
    * @since 0.0.2
    */
 
    function create_lkpp_brand_taxonomy() {
 
        // Labels part for the GUI
        $labels = array(
            'name' => _x( 'LKPP Product Brand', 'taxonomy general name' ),
            'singular_name' => _x( 'LKPP Product Brand', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search LKPP Product Brands' ),
            'popular_items' => null,
            'all_items' => __( 'All LKPP Product Brands' ),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => null, 
            'update_item' => null,
            'add_new_item' => null,
            'new_item_name' => null,
            'separate_items_with_commas' => __( 'Separate topics with commas' ),
            'add_or_remove_items' => __( 'Add or remove topics' ),
            'choose_from_most_used' => null,
            'menu_name' => __( 'LKPP Product Brands' ),
        ); 
 
        // set taxonomy option data
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => true,
        );

        // register taxonomy to woocommerce product object
        register_taxonomy( 'lkpp_product_brand', 'product', $args );
        register_taxonomy_for_object_type( 'lkpp_product_brand', 'product' );
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

        global $post, $wpdb, $thepostid, $woocommerce;
        
        $lkpp_active = get_post_meta( $post->ID, 'lkpp_active', true );

        if (trim($lkpp_active) == '') {
            update_post_meta($post->ID, 'lkpp_active', 'inactive');
            $lkpp_active = 'inactive';
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
	
	    // Note the 'id' attribute needs to match the 'target' parameter set above
        ?>
        <div id='lkpp_data_options' class='panel woocommerce_options_panel'>
            <?php ?>
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
                    <select id="lkpp_product_category_id" name="lkpp_product_category_id[]" data-placeholder="<?php _e( 'Search for LKPP Product Category&hellip;', 'woocommerce' ); ?>" style="width:50%;max-width:15em;">
	                    <?php
        
                            $lkpp_product_category_id = get_post_meta( $post->ID, 'lkpp_product_category_id', true );
		                    if ( $lkpp_product_category_id ) {

				                $lkpp_categ = get_terms(
                                    array(
                                        'hide_empty' => false, // also retrieve terms which are not used yet
                                        'meta_query' => array(
                                            array(
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
                    <select id="lkpp_brand_id" name="lkpp_brand_id[]" data-placeholder="<?php _e( 'Search for LKPP Product Brand&hellip;', 'woocommerce' ); ?>" style="width:50%;max-width:15em;">
	                    <?php
        
                            $lkpp_brand_id = get_post_meta( $post->ID, 'lkpp_brand_id', true );
		                    if ( $lkpp_brand_id ) {

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
				                echo '<option value="' . $lkpp_product_brand . '" selected="selected">' . $lkpp_brand_name . '</option>';
			                    
		                    }
	                    ?>
                    </select> 
                </p>
                <?php 
                
                ?>
            </div>

        </div>
        <?php

    }

    /**
     * Load Select 2 and JS Script.
     */
    function admin_scripts(){
 
        //wp_enqueue_style('select2-lkpp', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
        //wp_enqueue_script('select2-lkpp', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
     
        // please create also an empty JS file in your theme directory and include it too
        wp_enqueue_script('lkpp_admin', 'https://stage.spakat.id/wp-content/plugins/woocommerce-lkpp-v5-connector-0.0.2_1/assets/js/admin.js', array( 'jquery', 'select2' ) ); 
     
    }

    /**
     * LKPP Get Product Categ Ajax Handler.
     */
    function lkpp_get_categ_callback(){
 
        // we will pass post IDs and titles to this array
        $return = array();

        $s = wp_unslash( $_GET['q'] );
        $comma = _x( ',', 'tag delimiter' );
        
        if ( ',' !== $comma ) {
            $s = str_replace( $comma, ',', $s );
        }

        if ( false !== strpos( $s, ',' ) ) {
            $s = explode( ',', $s );
            $s = $s[ count( $s ) - 1 ];
        }

        $s = trim( $s );
     
        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
        $search_results = new WP_Term_Query( array( 
            'taxonomy' => 'lkpp_product_category',
            'name__like' => $s,
            'hide_empty' => false 
            ) 
        );

        if( ! empty( $search_results->terms ) ) {
            foreach ( $search_results->terms as $term ) {
                $lkpp_categ_name = $term->name;
                $lkpp_categ_id = $term->term_id; // get_term_meta($term->term_id, 'lkpp_product_category_id', true);
                $return[] = array( $lkpp_categ_id, $lkpp_categ_name );
            }
        }
        echo json_encode( $return );
        die;
    }

    /**
     * LKPP Get Product Brand Ajax Handler.
     */
    function lkpp_get_brand_callback(){
 
        // we will pass post IDs and titles to this array
        $return = array();

        $s = wp_unslash( $_GET['q'] );
        $comma = _x( ',', 'tag delimiter' );
        
        if ( ',' !== $comma ) {
            $s = str_replace( $comma, ',', $s );
        }

        if ( false !== strpos( $s, ',' ) ) {
            $s = explode( ',', $s );
            $s = $s[ count( $s ) - 1 ];
        }

        $s = trim( $s );
     
        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
        $search_results = new WP_Term_Query( array( 
            'taxonomy' => 'lkpp_product_brand',
            'name__like' => $s,
            'hide_empty' => false 
            ) 
        );

        if( ! empty( $search_results->terms ) ) {
            foreach ( $search_results->terms as $term ) {
                $lkpp_categ_name = $term->name;
                $lkpp_categ_id = $term->term_id; // get_term_meta($term->term_id, 'lkpp_product_category_id', true);
                $return[] = array( $lkpp_categ_id, $lkpp_categ_name );
            }
        }
        echo json_encode( $return );
        die;
    }
}   
