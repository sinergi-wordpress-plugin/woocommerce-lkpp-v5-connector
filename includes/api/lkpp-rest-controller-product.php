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
 * Handle a custom 'lkpp_publish' query var to get products with the 'lkpp_publish' meta.
 * @param array $query - Args for WP_Query.
 * @param array $query_vars - Query vars from WC_Product_Query.
 * @return array modified $query
 */
function lkpp_publish_query_var( $query, $query_vars ) {
	if ( ! empty( $query_vars['lkpp_publish'] ) ) {
		$query['meta_query'][] = array(
			'key' => 'lkpp_publish',
			'value' => esc_attr( $query_vars['lkpp_publish'] ),
		);
	}

	return $query;
}
add_filter( 'woocommerce_product_data_store_cpt_get_products_query', 'lkpp_publish_query_var', 10, 2 );

/**
 * Rest api get all product callback function
 * @param Object $request
 */
function get_all_lkpp_product($request){

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
		$args = array(
			'lkpp_active'	=> 'active',
			'paginate'		=> true,
			'limit'			=> 500,
			'page'			=> 1,
			'orderby'		=> 'date',
			'order'			=> 'DESC'
		);
		if(isset($params['per_page']) && !empty($params['per_page'])){
			$args['limit'] = $params['per_page'];
			$json_response['per_page'] = $params['per_page'];
		}
		if(isset($params['page']) && !empty($params['page'])){
			$args['page'] = $params['page'];
			$json_response['current_page'] = $params['page'];
		}
		if(isset($params['order_by']) && !empty($params['order_by'])){
			if($params['order_by'] == 'updated_date'){
				$args['orderby'] = 'modified';
			}	
		}
		if(isset($params['sort']) && !empty($params['sort'])){
			if($params['sort'] == 'asc'){
				$args['order'] = 'ASC';
			}	
		}
		$data = wc_get_products($args);
		$product_list = map_product_data($data);
		$json_response['total'] = $data->total;
		$json_response['total_page'] = $data->max_num_pages;
		$json_response['produk'] = $product_list;
		$response = new WP_REST_Response($json_response);
    	$response->set_status(200);

    	return $response;	
	}
}

/**
 * Rest api get updated product callback function
 * @param Object $request
 */
function get_updated_lkpp_product($request){

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
		$args = array(
			'lkpp_active'	=> 'active',
			'paginate'		=> true,
			'limit'			=> 500,
			'page'			=> 1,
			'orderby'		=> 'date',
			'order'			=> 'DESC',
			'date_modified' => current_time('Y-m-d') . '...' . current_time('Y-m-d')
		);
		if(isset($params['per_page']) && !empty($params['per_page'])){
			$args['limit'] = $params['per_page'];
			$json_response['per_page'] = $params['per_page'];
		}
		if(isset($params['page']) && !empty($params['page'])){
			$args['page'] = $params['page'];
			$json_response['current_page'] = $params['page'];
		}
		if(isset($params['order_by']) && !empty($params['order_by'])){
			if($params['order_by'] == 'updated_date'){
				$args['orderby'] = 'modified';
			}	
		}
		if(isset($params['sort']) && !empty($params['sort'])){
			if($params['sort'] == 'asc'){
				$args['order'] = 'ASC';
			}	
		}
		if(isset($params['start_date']) && !empty($params['start_date']) && isset($params['end_date']) && !empty($params['end_date'])){
			$args['date_modified'] = $params['start_date'] . '...' . $params['end_date'];
		}
		$data = wc_get_products($args);
		$product_list = map_product_data($data);
		$json_response['total'] = $data->total;
		$json_response['total_page'] = $data->max_num_pages;
		$json_response['produk'] = $product_list;
		$response = new WP_REST_Response($json_response);
    	$response->set_status(200);

    	return $response;	
	}
}

/**
 * Rest api get updated product callback function
 * @param Object $request
 */
function get_lkpp_product($request){

	// Get LKPP Settings
	$settings = get_option('lkpp_settings');

	// JSON response format
	$json_response = array(
		'produk'		=> '',
	);

	$params = $request->get_params();
	$secret_key = sanitize_text_field($request->get_param( 'secretkey' ));
	$authentication = lkpp_authentication($secret_key);
	if($authentication['act'] == 'error'){
		return new WP_Error( 'invalid_secret', 'Invalid secret Key', array('status' => 403) );
	} else {
		if(isset($params['no_produk_penyedia']) && !empty($params['no_produk_penyedia'])){
			$product_id = wc_get_product_id_by_sku($params['no_produk_penyedia']);
			$product = wc_get_product($product_id);
			if(empty($product)){
				return new WP_Error( 'product_not_found', 'Product Not Found!', array('status' => 404) );
			}
			$product_data = array(
				'informasi'		=> array(
					'unspsc' => '',
					'id_kategori_produk_lkpp' => 0,
					'nama_produk' => '',
					'no_produk_penyedia' => '',
					'id_manufaktur' => 0,
					'berlaku_sampai' => '',
					'id_penawaran_lkpp' => (int)$settings['lkpp_id'],
					'id_unit_pengukuran_lkpp' => '',
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
					'item' => array(
						array(
							'file' => '',
							'deskripsi' => '',
						)
					),
					'tanggal_update' => '',
				)
			);
			$product_term = get_the_terms( $product_id, 'product_cat' );
			$lkpp_publish = get_post_meta($product_id, 'lkpp_publish', true);
			$local_product = get_post_meta($product_id, 'local_product', true);
			$product_data['informasi']['unspsc'] = (int)get_term_meta($product_term[0]->term_id,'unspsc_code',true);
			$product_data['informasi']['id_kategori_produk_lkpp'] = (int)get_post_meta($product_id, 'lkpp_categ_id', true);
			$product_data['informasi']['nama_produk'] = $product->get_name();
			$product_data['informasi']['no_produk_penyedia'] = $product->get_sku();
			$product_data['informasi']['id_manufaktur'] = (int)get_lkpp_brand_id($product_id);
			$product_data['informasi']['berlaku_sampai'] = get_post_meta($product_id, 'lkpp_expired_date', true);
			$product_data['informasi']['id_unit_pengukuran_lkpp'] = (int)get_post_meta($product_id, 'lkpp_unit_id', true);
			$product_data['informasi']['deskripsi_singkat'] = $product->get_short_description();
			$product_data['informasi']['deskripsi_lengkap'] = $product->get_description();
			$product_data['informasi']['kuantitas_stok'] = (int)get_post_meta($product_id, 'lkpp_stock', true);
			$product_data['informasi']['tanggal_update'] = get_the_modified_date( 'Y-m-d H:i:s', $product_id );
			$product_data['informasi']['tkdn_produk'] = get_post_meta($product_id, 'tkdn', true);
			if($lkpp_publish == 'publish'){
				$product_data['informasi']['produk_aktif'] = 1;
			}
			if($local_product == 'yes'){
				$product_data['informasi']['apakah_produk_lokal'] = 1;
			}
			$product_data['informasi']['url_produk'] = get_permalink( $product_id );
	
			// Add Product Specification
			$spec_list = array();	
			$attributes = $product->get_attributes();
			foreach($attributes as $attribute){
				$terms = get_terms($attribute['name']);
				$name = substr($attribute['name'], 3, strlen($attribute['name']));
				$spec['label'] = $name;
				$spec['deskripsi'] = $terms[0]->name;
				/*if(sizeof($terms) > 1){
					$spec['deskripsi'] = '';
					foreach($terms as $term){
						$spec['deskripsi'] += $term->name . " "; 
					}
				}*/
				array_push($spec_list, $spec);
			}
			$spec['label'] = 'Estimasi Berat Pengiriman';
			$spec['deskripsi'] = $product->get_weight() . ' Kg';
			array_unshift($spec_list, $spec);
			
			$product_data['spesifikasi']['item'] = $spec_list;
			$product_data['spesifikasi']['tanggal_update'] = get_the_modified_date( 'Y-m-d H:i:s', $product_id );
	
			// Add product featured image and image gallery URLs
			$image_urls = get_product_image_url($product);
	
			// Map image urls to product field
			$images = array();
			$product_data['informasi']['image_50x50'] = $image_urls['featured']['50'];
			$product_data['informasi']['image_100x100'] = $image_urls['featured']['100'];
			$product_data['informasi']['image_300x300'] = $image_urls['featured']['300'];
			$product_data['informasi']['image_800x800'] = $image_urls['featured']['800'];
			foreach($image_urls['gallery'] as $gallery){
				$image['image_50x50'] = $gallery['50'];
				$image['image_100x100'] = $gallery['100'];
				$image['image_300x300'] = $gallery['300'];
				array_push($images, $image);
			}
			$product_data['image']['item'] = $images;
			$product_data['image']['tanggal_update'] = get_the_modified_date( 'Y-m-d H:i:s', $product_id );
	
			// Add Product Price
			$price_exc = $product->get_regular_price();
			$price_inc = round($price_exc + ($price_exc * (10/100)));
			$lkpp_price = get_post_meta($product_id, 'lkpp_price', true);
			$product_data['harga']['harga_retail'] = $price_inc;
			$product_data['harga']['harga_pemerintah'] = (int)$lkpp_price;
			$product_data['harga']['tanggal_update'] = get_post_meta($product_id, 'price_update_date', true);

			$json_response['produk'] = $product_data;
			$response = new WP_REST_Response($json_response);
    		$response->set_status(200);

    		return $response;
		} else {
			return new WP_Error( 'invalid_request', 'Invalid Request', array('status' => 401) );
		}	
	}
}

/**
 * Product Data mapper function
 * @param Object $data
 */
function map_product_data($data){

	// Get LKPP Settings
	$settings = get_option('lkpp_settings');

	// Product query result list
	$products = array();

	// Product image object data
	$image = array(
		'deskripsi'		=> '',
		'image_50x50'	=> '',
		'image_100x100'	=> '',
		'image_300x300'	=> '',
	);

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
			'id_unit_pengukuran_lkpp' => '',
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
			'item' => array(
				array(
					'file' => '',
					'deskripsi' => '',
				)
			),
			'tanggal_update' => '',
		)
	);

	// Loop through data and map the field
	foreach($data->products as $item){
		$item_id = $item->get_id();
		$item_term = get_the_terms( $item_id, 'product_cat' );
		$lkpp_publish = get_post_meta($item_id, 'lkpp_publish', true);
		$local_product = get_post_meta($item_id, 'local_product', true);
		$product['informasi']['unspsc'] = (int)get_term_meta($item_term[0]->term_id,'unspsc_code',true);
		$product['informasi']['id_kategori_produk_lkpp'] = (int)get_post_meta($item_id, 'lkpp_categ_id', true);
		$product['informasi']['nama_produk'] = $item->get_name();
		$product['informasi']['no_produk_penyedia'] = $item->get_sku();
		$product['informasi']['id_manufaktur'] = (int)get_lkpp_brand_id($item_id);
		$product['informasi']['berlaku_sampai'] = get_post_meta($item_id, 'lkpp_expired_date', true);
		$product['informasi']['id_unit_pengukuran_lkpp'] = (int)get_post_meta($item_id, 'lkpp_unit_id', true);
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

		// Add Product Specification
		$spec_list = array();	
		$attributes = $item->get_attributes();
		foreach($attributes as $attribute){
			$terms = get_terms($attribute['name']);
			$name = substr($attribute['name'], 3, strlen($attribute['name']));
			$spec['label'] = $name;
			$spec['deskripsi'] = $terms[0]->name;
			/*if(sizeof($terms) > 1){
				$spec['deskripsi'] = '';
				foreach($terms as $term){
					$spec['deskripsi'] += $term->name . " "; 
				}
			}*/
			array_push($spec_list, $spec);
		}
		$spec['label'] = 'Estimasi Berat Pengiriman';
		$spec['deskripsi'] = $item->get_weight() . ' Kg';
		array_unshift($spec_list, $spec);
		
		$product['spesifikasi']['item'] = $spec_list;
		$product['spesifikasi']['tanggal_update'] = get_the_modified_date( 'Y-m-d H:i:s', $item_id );

		// Add product featured image and image gallery URLs
		$image_urls = get_product_image_url($item);

		// Map image urls to product field
		$images = array();
		$product['informasi']['image_50x50'] = $image_urls['featured']['50'];
		$product['informasi']['image_100x100'] = $image_urls['featured']['100'];
		$product['informasi']['image_300x300'] = $image_urls['featured']['300'];
		$product['informasi']['image_800x800'] = $image_urls['featured']['800'];
		foreach($image_urls['gallery'] as $gallery){
			$image['image_50x50'] = $gallery['50'];
			$image['image_100x100'] = $gallery['100'];
			$image['image_300x300'] = $gallery['300'];
			array_push($images, $image);
		}
		$product['image']['item'] = $images;
		$product['image']['tanggal_update'] = get_the_modified_date( 'Y-m-d H:i:s', $item_id );

		// Add Product Price
		$price_exc = $item->get_regular_price();
		$price_inc = round($price_exc + ($price_exc * (10/100)));
		$lkpp_price = get_post_meta($item_id, 'lkpp_price', true);
		$product['harga']['harga_retail'] = $price_inc;
		$product['harga']['harga_pemerintah'] = (int)$lkpp_price;
		$product['harga']['tanggal_update'] = get_post_meta($item_id, 'price_update_date', true);

		array_push($products, $product);
	}

	return $products;
}

/**
 * Get Post's LKPP Brand ID
 */
function get_lkpp_brand_id($item_id){
	$term_list = wp_get_post_terms($item_id, 'product_brand', array("fields" => "ids"));
	$term_id=$term_list[0];
	$lkpp_brand_id = get_term_meta($term_id,'lkpp_brand_id',true);
	return $lkpp_brand_id;
}

/**
 * Get Product Image URL function
 * @param Object $item
 */
function get_product_image_url($item){
	$featured_image_id = $item->get_image_id();
	$image_gallery_ids = $item->get_gallery_image_ids();
	$featured_image_urls = array(
		'50' 	=> wp_get_attachment_image_src( $featured_image_id, 'shop_thumbnail')[0],
		'100'	=> wp_get_attachment_image_src( $featured_image_id, 'shop_thumbnail')[0],
		'300'	=> wp_get_attachment_image_src( $featured_image_id, 'medium')[0],
		'800'	=> wp_get_attachment_image_src( $featured_image_id, 'large')[0] 
	);
	$gallery_image_url = array(
		'deskripsi'	=> '',
		'50'		=> '',
		'100'		=> '',
		'300'		=> '',
	);
	$gallery_image_urls = array();
	foreach($image_gallery_ids as $id){
		$gallery_image_url['50'] = wp_get_attachment_image_src( $id, 'shop_thumbnail')[0];
		$gallery_image_url['100'] = wp_get_attachment_image_src( $id, 'shop_thumbnail')[0];
		$gallery_image_url['300'] = wp_get_attachment_image_src( $featured_image_id, 'medium')[0];
		array_push($gallery_image_urls, $gallery_image_url);
	}

	$image_urls = array(
		'featured'	=> $featured_image_urls,
		'gallery'	=> $gallery_image_urls
	);

	return $image_urls;
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

/**
 * Register LKPP update product rest route
 */
add_action('rest_api_init', function () {
	register_rest_route( 'lkpp/v1', 'updated_produk',array(
		'methods'  => 'GET',
		'callback' => 'get_updated_lkpp_product',
		'permission_callback' => function() {
			return true;
		}
	));
});

/**
 * Register LKPP update product rest route
 */
add_action('rest_api_init', function () {
	register_rest_route( 'lkpp/v1', 'produk',array(
		'methods'  => 'GET',
		'callback' => 'get_lkpp_product',
		'permission_callback' => function() {
			return true;
		}
	));
});