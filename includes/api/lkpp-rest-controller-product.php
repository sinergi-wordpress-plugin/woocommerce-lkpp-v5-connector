<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to validate request's secret key
 */
function lkpp_authentication($secret){

	$secret = esc_attr($secret);
	if(empty($secret)){
		return array('act' => 'error' , 'msg' => 'invalid Request, Secret Key Required','secret' => $secret );
	}
	$lkpp_settings = get_option('lkpp_settings');
	$lkpp_secret = $lkpp_settings['lkpp_secret'];
	if($secret == $lkpp_secret){
			return array('act' => 'success' , 'msg' => 'Secret Key matched!','secret' => $secret );
	} else {
		return array('act' => 'error' , 'msg' => 'Invalid Secret Key','secret' => $secret );
	}
}

/**
 * Handle a custom 'lkpp_active' query var to get products with the 'lkpp_active' meta.
 * @param array $query - Args for WP_Query.
 * @param array $query_vars - Query vars from WC_Product_Query.
 * @return array modified $query
 */
function lkpp_active_query_var( $query, $query_vars ) {
	if ( ! empty( $query_vars['lkpp_active'] ) ) {
		$query['meta_query'][] = array(
			'key' => 'lkpp_active',
			'value' => esc_attr( $query_vars['lkpp_active'] ),
		);
	}

	return $query;
}
add_filter( 'woocommerce_product_data_store_cpt_get_products_query', 'lkpp_active_query_var', 10, 2 );

/**
 * Rest api callback function
 * @param $request
 */
function get_all_lkpp_product($request){
	
	// Product query result list
	$product_list = array();

	// Get LKPP Settings
	$settings = get_option('lkpp_settings');

	// Product object data
	$product = array(
		'informasi'		=> array(
			'unspsc' => '',
			'id_kategori_produk_lkpp' => 0,
			'nama_produk' => '',
			'no_produk_penyedia' => '',
			'id_manufaktur' => 0,
			'berlaku_sampai' => '',
			'id_penawaran_lkpp' => (int)$settings['lkpp_id'],
			'id_unit_pengukuran_lkpp' => 7,
			'deskripsi_singkat' => '',
			'deskripsi_lengkap' => '',
			'kuantitas_stok' => 0,
			'tanggal_update' => '',
			'tkdn_produk' => '',
			'produk_aktif' => 0,
			'apakah_produk_lokal' => 0,
			'url_produk' => '',
			'image_50x50' => '',
			'image_100x100' => '',
			'image_300x300' => '',
			'image_800x800' => ''	
		),
		'spesifikasi'	=> array(
			'item' => array(),
			'tanggal_update' => '',
		),
		'image'			=> array(
			'item' => array(),
			'tanggal_update' => '',
		),
		'harga'			=> array(
			'harga_retail' => 0,
			'harga_pemerintah' => 0,
			'ongkos_kirim' => 0,
			'kurs_id' => 1,
			'tanggal_update' => '',
		),
		'lampiran'			=> array(
			'item' => array(),
			'tanggal_update' => '',
		)
	);

	// JSON response format
	$json_response = array(
		'total'			=> 0,
		'current_page'	=> 1,
		'per_page'		=> 500,
		'total_page'	=> 0,
		'produk'		=> array()
	);

	$params = $request->get_params();
	$secret_key = sanitize_text_field($request->get_param( 'secretkey' ));
	$authentication = lkpp_authentication($secret_key);
	if($authentication['act'] == 'error'){
		return new WP_Error( 'invalid_secret', 'Invalid secret Key', array('status' => 403) );
	} else {
		$settings = get_option('lkpp_settings');
		$args = array(
			'lkpp_active'	=> 'active',
			'paginate'		=> true,
			'limit'			=> 500,
			'page'			=> 1,
			'orderby'		=> 'date',
			'order'			=> 'DESC'
		);
		$data = wc_get_products($args);
		foreach($data->products as $item){
			$item_id = $item->get_id();
			$item_term = get_the_terms( $item_id, 'product_cat' );
			$lkpp_publish = get_post_meta($item_id, 'lkpp_publish', true);
			$local_product = get_post_meta($item_id, 'local_product', true);
			$product['informasi']['unspsc'] = (int)get_term_meta($item_term[0]->term_id,'unspsc_code',true);
			$product['informasi']['id_kategori_produk_lkpp'] = (int)get_post_meta($item_id, 'lkpp_product_category_id', true);
			$product['informasi']['nama_produk'] = $item->get_name();
			$product['informasi']['no_produk_penyedia'] = $item->get_sku();
			$product['informasi']['id_manufaktur'] = (int)get_post_meta($item_id, 'lkpp_brand_id', true);
			$product['informasi']['berlaku_sampai'] = get_post_meta($item_id, 'lkpp_expired_date', true);
			$product['informasi']['deskripsi_singkat'] = $item->get_short_description();
			$product['informasi']['deskripsi_lengkap'] = $item->get_description();
			$product['informasi']['kuantitas_stok'] = (int)get_post_meta($item_id, 'lkpp_stock', true);
			$product['informasi']['tanggal_update'] = get_the_modified_date( 'Y-m-d H:i:s', $item_id );
			$product['informasi']['tkdn_produk'] = get_post_meta($item_id, 'tkdn', true);
			if($lkpp_publish == 'publish'){
				$product['informasi']['produk_aktif'] = 1;
			}
			if($local_product == 'yes'){
				$product['informasi']['apakah_produk_lokal'] = 1;
			}
			$product['informasi']['url_produk'] = get_permalink( $item_id );
			array_push($product_list, $product);
		}
		$json_response['total'] = $data->total;
		$json_response['total_page'] = $data->max_num_pages;
		$json_response['produk'] = $product_list;
		//$success = 'Connected';
		$response = new WP_REST_Response($json_response);
    	$response->set_status(200);

    	return $response;	
	}
}

/**
 * Register LKPP all product rest route
 */
add_action('rest_api_init', function () {
	register_rest_route( 'lkpp/v1', 'all_produk',array(
		'methods'  => 'GET',
		'callback' => 'get_all_lkpp_product',
		'permission_callback' => function() {
			return true;
		}
	));
});