<?php
/**
 * Functions used by plugins
 */
if ( ! class_exists( 'SC_WC_Dependencies' ) )
	require_once 'class-wc-dependencies.php';

/**
 * WC Detection
 */
if ( ! function_exists( 'sc_is_woocommerce_active' ) ) {
	function sc_is_woocommerce_active() {
		return SC_WC_Dependencies::woocommerce_active_check();
	}
}
