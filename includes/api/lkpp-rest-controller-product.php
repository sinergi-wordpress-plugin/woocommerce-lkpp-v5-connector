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
 * Rest api callback function
 * @param $request
 */
function get_all_lkpp_product($request){
	
	// Product query result list
	$product_list = array();

	// Product object data
	$product = array(
		'informasi'		=> array(
			'unspsc' => '',
			'id_kategori_produk_lkpp' => 0,
			'nama_produk' => '',
			'no_produk_penyedia' => '',
			'id_manufaktur' => 0,
			'berlaku_sampai' => '',
			'id_penawaran_lkpp' => 0,
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
	$json_template = array(
		'total'			=> '',
		'current_page'	=> '',
		'per_page'		=> '',
		'total_page'	=> '',
		'produk'		=> array()
	);

	$params = $request->get_params();
	$secret_key = sanitize_text_field($request->get_param( 'secretkey' ));
	$authentication = lkpp_authentication($secret_key);
	if($authentication['msg'] == 'error'){
		return new WP_Error( 'invalid_secret', 'Invalid secret Key', array('status' => 403) );
	}
}

/**
 * Register LKPP all product rest route
 */
add_action('rest_api_init', function () {
	register_rest_route( 'lkpp/v1', 'all_produk',array(
				  'methods'  => 'GET',
				  'callback' => 'get_all_lkpp_product'
		));
  });