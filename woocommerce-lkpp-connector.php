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

 // If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if plugin is active
 *
 * @param string $plugin_file Plugin file name.
 */
function lkpp_is_plugin_active( $plugin_file ) {

	$active_plugins = (array) apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );

	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, (array) get_site_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $plugin_file, $active_plugins, true ) || array_key_exists( $plugin_file, $active_plugins );
}

/**
 * Check if WooCommerce plugin is active
 */
if ( ! lkpp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

class WooCommerce_Lkpp_Connector {
    /**
    * Setup the WC_Warranty extension
    */
    function __construct() {

        self::$plugin_file      = __FILE__;
        self::$base_path        = plugin_dir_path( __FILE__ );
        self::$includes_path    = trailingslashit( self::$base_path ) . 'includes';

        //self::$default_statuses = $this->get_default_statuses();
        //self::$providers        = self::get_providers();

        // form builder tips
        /*self::$tips   = apply_filters( 'wc_warranty_form_builder_tips', array(
            'name'      => __('The name of the field that gets displayed on the Warranty Requests Table (Admin Panel)', 'wc_warranty'),
            'label'     => __('The label of the field displayed to the user when requesting for an RMA (Frontend)', 'wc_warranty'),
            'default'   => __('The initial value of the field', 'wc_warranty'),
            'required'  => __('Check this to make this field required', 'wc_warranty'),
            'multiple'  => __('Check this to allow users to select one or more options', 'wc_warranty'),
            'options'   => __('One option per line', 'wc_warranty')
        ) );*/

        $this->include_files();

        /*if ( !is_admin() ) {
            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        }*/

        //add_action( 'init', array( $this, 'init' ) );
        add_action( 'init', 'create_lkpp_category_taxonomy', 0 );
        add_action( 'init', 'create_lkpp_brand_taxonomy', 0 );
    }

    /**
    * Initialization logic
    */
    public function init() {
        require_once self::$includes_path . '/class-warranty-privacy.php';
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'wc_warranty', plugins_url( 'assets/css/front.css', self::$plugin_file ) );
    }
    /**
    * Include core files
    */
    public function include_files() {
        //require_once self::$includes_path . '/class.warranty_compat.php';
        //require_once self::$includes_path .'/functions.php';
        //require_once self::$includes_path .'/class-warranty-install.php';
        //require_once self::$includes_path .'/class-warranty-shortcodes.php';
        //require_once self::$includes_path .'/class-warranty-query.php';
        //require_once self::$includes_path .'/class-warranty-order.php';
        //require_once self::$includes_path .'/class-warranty-item.php';

        if ( is_admin() ) {
            //require_once self::$includes_path .'/class-warranty-coupons.php';
            //require_once self::$includes_path .'/class-warranty-settings.php';
            self::$admin = include self::$includes_path .'/class-lkpp-admin.php';
        } /* else {
            include_once self::$includes_path .'/class-warranty-frontend.php';
            include_once self::$includes_path .'/class-warranty-cart.php';
        } */

        /* if ( defined( 'DOING_AJAX' ) ) {
            require_once self::$includes_path .'/class-warranty-ajax.php';
        }*/

    }


    /**
    * Add LKPP product category taxonomies
    *
    * @since 0.0.2
    */
 
    public function create_lkpp_category_taxonomy() {
 
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
 
    public function create_lkpp_brand_taxonomy() {
 
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
}
$GLOBALS['wc_lkpp'] = new WooCommerce_Lkpp_Connector();