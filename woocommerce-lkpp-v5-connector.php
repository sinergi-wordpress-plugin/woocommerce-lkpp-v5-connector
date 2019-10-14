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

    define( 'LKPP_CONNECTOR', dirname( __FILE__ ) );
    
    add_action( 'after_setup_theme', 'create_lkpp_category_taxonomy');
    add_action( 'after_setup_theme', 'create_lkpp_brand_taxonomy');
    add_action( 'init', 'render_panel');
    add_action( 'admin_enqueue_scripts', 'admin_scripts');
    add_action( 'wp_ajax_lkppgetcateg', 'lkpp_get_categ_callback');
    add_action( 'wp_ajax_lkppgetbrand', 'lkpp_get_brand_callback');
    add_action( 'save_post', 'lkpp_save_metaboxdata', 10, 2 );
    add_action( 'admin_menu','lkpp_admin_settings_menu');
    add_action('restrict_manage_posts', 'restrict_listings_by_categ_lkpp');
    add_filter('parse_query', 'convert_id_to_lkpp_categ_in_query');
    add_action('restrict_manage_posts', 'restrict_listings_by_brand_lkpp');
    add_filter('parse_query', 'convert_id_to_lkpp_brand_in_query');
    //add_action('updated_post_meta', 'update_lkpp_content_date');
    require_once (LKPP_CONNECTOR . '/includes/api/lkpp-rest-controller-product.php');

    /**
     * Add Content Updated date function
     * @param Int       $meta_id
     * @param Int       $post_id
     * @param String    $meta_key
     * @param String    $meta_value
     */
    function update_lkpp_content_date($meta_id, $post_id, $meta_key, $meta_value){
        if('lkpp_price' == $meta_key){
            //date_default_timezone_set('Asia/Jakarta');
            //$datetime = new DateTime();
            //$timezone = new DateTimeZone('Asia/Jakarta');
            //$datetime->setTimezone($timezone);
            $timestamp = time() + (0 * 7 * 0 * 0);
            $date = date('Y-m-d H:i:s', $timestamp);
            update_post_meta($post_id, 'price_update_date', $date);
        }
    }

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
        
        // Manage Taxonomy Capabilities
        $capability = array(
            'manage_terms' => 'edit_posts',
            'edit_terms' => '',
            'delete_terms' => '',
            'assign_terms' => 'edit_posts'
        );

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
            'capabilities'              => $capability,
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'meta_box_cb'                => false
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
        
        // Manage Taxonomy Capabilities
        $capability = array(
            'manage_terms' => 'edit_posts',
            'edit_terms' => '',
            'delete_terms' => '',
            'assign_terms' => 'edit_posts'
        );

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
            'capabilities'               => $capability,
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'meta_box_cb'                => false
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
	
	    // Note the 'id' attribute needs to match the 'target' parameter set above
        ?>
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
                    <select id="lkpp_brand_id" name="lkpp_brand_id" data-placeholder="<?php _e( 'Search for LKPP Product Brand&hellip;', 'woocommerce' ); ?>" style="width:50%;max-width:15em;">
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

                    woocommerce_wp_text_input( 
                        array( 
                            'id'          => 'tkdn', 
                            'label'       => __( 'Tingkat Komponen Dalam Negeri (%)', 'woocommerce' ), 
                            'placeholder' => 'Input nilai persentase tingkat komponen dalam negeri'
                        )
                    );
                ?>
            </div>

            <div class='options_group'>
                <?php 
                    woocommerce_wp_text_input( 
                        array( 
                            'id'          => 'lkpp_price', 
                            'label'       => __( 'Harga LKPP (Inc PPN)', 'woocommerce' ), 
                        )
                    );

                    woocommerce_wp_text_input( 
                        array( 
                            'id'          => 'lkpp_disc', 
                            'label'       => __( 'persentase Diskon', 'woocommerce' ),
                            'custom_attributes' => array( 'disabled' => true) 
                        )
                    );

                    woocommerce_wp_text_input( 
                        array( 
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
            <input type="hidden" id="price_update" name="price_update" value="" />            
        </div>
        <?php
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

            if( isset( $_POST['lkpp_product_category_id'] ) ) {
                update_post_meta( $post_id, 'lkpp_product_category_id', $_POST['lkpp_product_category_id'] );
                $lkpp_categ = get_terms(
                    array(
                        'hide_empty' => false, // also retrieve terms which are not used yet
                        'meta_query' => array(
                            array(
                               'key'       => 'lkpp_product_category_id',
                               'value'     => $_POST['lkpp_product_category_id'],
                               'compare'   => 'LIKE'
                            )
                        ),
                        'taxonomy'  => 'lkpp_product_category',
                        )
                );
                $lkpp_categ_id = $lkpp_categ[0]->term_id;
                wp_set_post_terms( $post_id, $lkpp_categ_id, 'lkpp_product_category' );
            }
            else
                delete_post_meta( $post_id, 'lkpp_product_category_id' );

            if( isset( $_POST['lkpp_brand_id'] ) ) {
                update_post_meta( $post_id, 'lkpp_brand_id', $_POST['lkpp_brand_id'] );
                $lkpp_brand = get_terms(
                    array(
                        'hide_empty' => false, // also retrieve terms which are not used yet
                        'meta_query' => array(
                            array(
                               'key'       => 'lkpp_brand_id',
                               'value'     => $_POST['lkpp_brand_id'],
                               'compare'   => 'LIKE'
                            )
                        ),
                        'taxonomy'  => 'lkpp_product_brand',
                        )
                );
                $lkpp_brand_id = $lkpp_brand[0]->term_id;
                wp_set_post_terms( $post_id, $lkpp_brand_id, 'lkpp_product_brand' );
            }    
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

            if(isset($_POST['price_update']) && ($_POST['price_update'] == 'updated')){
                $date = current_time('Y-m-d H:i:s');
                update_post_meta( $post_id, 'price_update_date', $date );
            }
                
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
        wp_enqueue_script('lkpp_admin', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', array( 'jquery', 'select2' ) ); 
     
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

    /**
     * Admin menu and page creation
     */
    function lkpp_admin_settings_menu() {
        add_menu_page(
            'LKPP E-Catalogue V5 Connector',
            'LKPP',
            'edit_posts',
            'lkpp-products',
            'lkpp_product_page_callback',
            'dashicons-screenoptions'
        );
        add_submenu_page(
            'lkpp-products',
            'LKPP Products',
            'All Products',
            'edit_posts',
            'lkpp-products',
            'lkpp_product_page_callback'
        );
        add_submenu_page ( 
            'lkpp-products',
            'LKPP Settings',
            'Settings',
            'manage_options',
            'lkpp-settings-page',
            'lkpp_settings_page_callback'
        );
    }
    /**
     * Admin menu page renderer callback
     */
    function lkpp_product_page_callback() {
        include(LKPP_CONNECTOR . '/templates/lkpp-product-page.php');
    }

    /**
     * Admin menu settings page renderer callback
     */
    function lkpp_settings_page_callback() {
        include(LKPP_CONNECTOR . '/templates/lkpp-settings-page.php');
    }

    /**
     * Add admin product filter by LKPP Category
     */
    function restrict_listings_by_categ_lkpp() {
        global $typenow;
        global $wp_query;
        if ($typenow=='product') {
            $taxonomy = 'lkpp_product_category';
            $business_taxonomy = get_taxonomy($taxonomy);
            wp_dropdown_categories(array(
                'show_option_all' =>  __("Select LKPP Category"),
                'taxonomy'        =>  $taxonomy,
                'name'            =>  'lkpp_product_category',
                'orderby'         =>  'name',
                'selected'        =>  (isset( $wp_query->query['lkpp_product_category']) ? $wp_query->query['lkpp_product_category'] : ''),
                'hierarchical'    =>  true,
                'depth'           =>  3,
                'show_count'      =>  false, // Show # listings in parens
                'hide_empty'      =>  false, // Don't show businesses w/o listings
            ));
        }
    }

    /**
     * Filter LKPP Category Query
     */
    function convert_id_to_lkpp_categ_in_query($query) {
		global $pagenow;
		$post_type = 'product'; // change HERE
		$taxonomy = 'lkpp_product_category'; // change HERE
		$q_vars = &$query->query_vars;
		if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
			$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
			$q_vars[$taxonomy] = $term->slug;
		}
    }
    
    /**
     * Add admin product filter by LKPP Brand
     */
    function restrict_listings_by_brand_lkpp() {
        global $typenow;
        global $wp_query;
        if ($typenow=='product') {
            $taxonomy = 'lkpp_product_brand';
            $business_taxonomy = get_taxonomy($taxonomy);
            wp_dropdown_categories(array(
                'show_option_all' =>  __("Select LKPP Brand"),
                'taxonomy'        =>  $taxonomy,
                'name'            =>  'lkpp_product_brand',
                'orderby'         =>  'name',
                'selected'        =>  (isset( $wp_query->query['lkpp_product_brand']) ? $wp_query->query['lkpp_product_brand'] : ''),
                'hierarchical'    =>  true,
                'depth'           =>  3,
                'show_count'      =>  false, // Show # listings in parens
                'hide_empty'      =>  false, // Don't show businesses w/o listings
            ));
        }
    }

    /**
     * Filter LKPP Brand Query
     */
    function convert_id_to_lkpp_brand_in_query($query) {
		global $pagenow;
		$post_type = 'product'; // change HERE
		$taxonomy = 'lkpp_product_brand'; // change HERE
		$q_vars = &$query->query_vars;
		if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
			$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
			$q_vars[$taxonomy] = $term->slug;
		}
    }
}   
