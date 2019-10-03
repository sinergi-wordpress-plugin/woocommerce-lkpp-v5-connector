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
    add_action( 'save_post', 'lkpp_save_metaboxdata', 10, 2 );

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
            'show_ui'                    => false,
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
            'show_ui'                    => false,
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
            $lkpp_publish = 'unpublish';
        }

        $local_product = get_post_meta( $post->ID, 'local_product', true );

        if (trim($local_product) == '') {
            update_post_meta($post->ID, 'local_product', 'no');
            $local_product = 'no';
        }

        $tkdn = get_post_meta( $post->ID, 'tkdn', true );

        if (trim($tkdn) == '') {
            update_post_meta($post->ID, 'tkdn', '0');
            $tkdn = '0';
        }

        $lkpp_price = get_post_meta( $post->ID, 'lkpp_price', true );

        if (trim($lkpp_price) == '') {
            update_post_meta($post->ID, 'lkpp_price', '0');
            $lkpp_price = '0';
        }

        $lkpp_disc = get_post_meta( $post->ID, 'lkpp_disc', true );

        if (trim($lkpp_disc) == '') {
            update_post_meta($post->ID, 'lkpp_disc', '0');
            $lkpp_disc = '0';
        }

        $lkpp_stock = get_post_meta( $post->ID, 'lkpp_stock', true );

        if (trim($lkpp_stock) == '') {
            update_post_meta($post->ID, 'lkpp_stock', '10');
            $lkpp_stock = '10';
        }

        $lkpp_expired = get_post_meta( $post->ID, 'lkpp_expired_date', true );

        if (trim($lkpp_expired_date) == '') {

            date_default_timezone_set('Asia/Jakarta');
            $default_date = date('Y-m-d', strtotime('+1 month'));
            update_post_meta($post->ID, 'lkpp_expired_date', $default_date);
            $lkpp_expired_date = $default_date;
        }

        include LKPP_CONNECTOR . 'templates/product-panel.php';

    }

    /**
     * Saving custom field to database
     */
    function lkpp_save_metaboxdata( $post_id, $post ) {
 
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
     
        // if post type is different from our selected one, do nothing
        if ( $post->post_type == 'product' ) {
            if( isset( $_POST['lkpp_active'] ) )
                update_post_meta( $post_id, 'lkpp_active', $_POST['lkpp_active'] );
            else
                delete_post_meta( $post_id, 'lkpp_active' );
     
            if( isset( $_POST['lkpp_publish'] ) )
                update_post_meta( $post_id, 'lkpp_publish', $_POST['lkpp_publish'] );
            else
                delete_post_meta( $post_id, 'lkpp_publish' );

            if( isset( $_POST['lkpp_product_category_id'] ) )
                update_post_meta( $post_id, 'lkpp_product_category_id', $_POST['lkpp_product_category_id'] );
            else
                delete_post_meta( $post_id, 'lkpp_product_category_id' );

            if( isset( $_POST['lkpp_brand_id'] ) )
                update_post_meta( $post_id, 'lkpp_brand_id', $_POST['lkpp_brand_id'] );
            else
                delete_post_meta( $post_id, 'lkpp_brand_id' );
                
            if( isset( $_POST['local_product'] ) )
                update_post_meta( $post_id, 'local_product', $_POST['local_product'] );
            else
                delete_post_meta( $post_id, 'local_product' );
                
            if( isset( $_POST['tkdn'] ) ) {
                if(isset( $_POST['local_product'] ) && ($_POST['local_product'] == 'yes') ) {
                    update_post_meta( $post_id, 'tkdn', $_POST['tkdn'] );
                }
                else {
                    update_post_meta( $post_id, 'tkdn', '0' );
                }
            }
            else
                delete_post_meta( $post_id, 'tkdn' ); 
                
            if( isset( $_POST['lkpp_price'] ) )
                update_post_meta( $post_id, 'lkpp_price', $_POST['lkpp_price'] );
            else
                delete_post_meta( $post_id, 'lkpp_price' );
                
            if( isset( $_POST['lkpp_disc'] ) )
                update_post_meta( $post_id, 'lkpp_disc', $_POST['lkpp_disc'] );
            else
                delete_post_meta( $post_id, 'lkpp_disc' );  
                
            if( isset( $_POST['lkpp_stock'] ) )
                update_post_meta( $post_id, 'lkpp_stock', $_POST['lkpp_stock'] );
            else
                delete_post_meta( $post_id, 'lkpp_stock' );
                
            if( isset( $_POST['lkpp_expired_date'] ) )
                update_post_meta( $post_id, 'lkpp_expired_date', $_POST['lkpp_expired_date'] );
            else
                delete_post_meta( $post_id, 'lkpp_expired_date' );
        }
        return $post_id;
    }

    /**
     * Load JS Script.
     */
    function admin_scripts(){
     
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
                $lkpp_categ_id = get_term_meta($term->term_id, 'lkpp_product_category_id', true);
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
                $lkpp_brand_name = $term->name;
                $lkpp_brand_id = get_term_meta($term->term_id, 'lkpp_brand_id', true);
                $return[] = array( $lkpp_brand_id, $lkpp_brand_name );
            }
        }
        echo json_encode( $return );
        die;
    }
}   
