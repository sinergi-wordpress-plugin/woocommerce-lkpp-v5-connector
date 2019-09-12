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
 * @since             0.0.1
 * @package           woocommerce-lkpp-v5-connector
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce LKPP E-Catalogue V5 Connector
 * Plugin URI:        https://github.com/sinergi-wordpress-plugin/woocommerce-lkpp-v5-connector
 * Description:       Woocommerce connector for LKPP E-Catalogue V5
 * Version:           0.0.1
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

/**
 * Add LKPP product category taxonomies
 *
 * @since 0.0.1
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
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );

    // register taxonomy to woocommerce product object
    register_taxonomy( 'lkpp_product_category', 'product', $args );
    register_taxonomy_for_object_type( 'lkpp_product_category', 'product' );
}
add_action( 'init', 'create_lkpp_category_taxonomy', 0 );