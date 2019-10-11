<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lkpp_settings_tabs( $current = 'lkpp_info' ) {
    
    $tabs = array( 'lkpp_info' => 'LKPP Info', 'lkpp_secret' => 'Rest API Secret' );
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : ’;
        echo "<a class='nav-tab $class' href='?page=lkpp-settings-page&tab=$tab'>$name</a>";

    }
    echo '</h2>';
}

function save_lkpp_settings($action) {

    $settings = get_option('lkpp_settings');
    if($action == 'save_lkpp_info') {
        $settings['lkpp_id'] = $_POST['lkpp_id'];
        $settings['lkpp_secret_access'] = $_POST['lkpp_secret_access'];
        $settings['lkpp_categ_url'] = $_POST['lkpp_categ_url'];
        $settings['lkpp_brand_url'] = $_POST['lkpp_brand_url'];
        $success = add_option( 'lkpp_settings', $settings, '', 'no' );
        if ( ! $success ) {
            $success = update_option( 'lkpp_settings', $settings );
        }
    } elseif($action == 'save_lkpp_secret') {
        $settings['lkpp_secret'] = $_POST['lkpp_secret'];
        $success = add_option( 'lkpp_settings', $settings, '', 'no' );
        if ( ! $success ) {
            $success = update_option( 'lkpp_settings', $settings );
        }
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'save_lkpp_info'){
    check_admin_referer( "lkpp-settings-info" );
    save_lkpp_settings($_POST['action']);
      
    //$url_parameters = isset($_POST['tab'])? 'updated=true&tab='.$_POST['tab'] : 'updated=true';
    //wp_redirect(admin_url('admin.php?page=lkpp-settings-page&'.$url_parameters));
    //exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'save_lkpp_secret'){
    check_admin_referer( "lkpp-settings-secret" );
    save_lkpp_settings($_POST['action']);
      
    //$url_parameters = isset($_POST['tab'])? 'updated=true&tab='.$_POST['tab'] : 'updated=true';
    //wp_redirect(admin_url('admin.php?page=lkpp-settings-page&'.$url_parameters));
    //exit;
}
//add_action('admin_post_save_lkpp_settings','process_lkpp_settings');

if ( isset ( $_GET['tab'] ) ) {
    lkpp_settings_tabs($_GET['tab']);
    if($_GET['tab'] == 'lkpp_info'){
        ?>
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <?php
    
            $settings = get_option( "lkpp_settings" );

            wp_nonce_field( "lkpp-settings-info" ); 
            ?>
            <div class="options-group">
                <p class="form-field">
                    <div style="width:150px; display:inline-block;">
                        <label for="lkpp_id"><?php _e( 'ID Penyedia LKPP', 'woocommerce' ); ?></label>
                    </div>
                    <input type="text" name="lkpp_id" value="<?php echo esc_attr($settings['lkpp_id']); ?>" style="width:5%; margin-left:10px;"/>
                </p>
                <p class="form-field">
                    <div style="width:150px; display:inline-block;">
                        <label for="lkpp_secret_access" style="width:150px !important;"><?php _e( 'Akses Secret Key', 'woocommerce' ); ?></label>
                    </div>
                    <input type="text" name="lkpp_secret_access" value="<?php echo esc_attr($settings['lkpp_secret_access']); ?>" style="width:20%; margin-left:10px;"/>
                </p>
                <p class="form-field">
                    <div style="width:150px; display:inline-block;">
                        <label for="lkpp_categ_url" style="width:150px !important;"><?php _e( 'URL Kategori LKPP', 'woocommerce' ); ?></label>
                    </div>
                    <input type="text" name="lkpp_categ_url" value="<?php echo esc_attr($settings['lkpp_categ_url']); ?>" style="width:25%; margin-left:10px;"/>
                </p>
                <p class="form-field">
                    <div style="width:150px; display:inline-block;">
                        <label for="lkpp_brand_url" style="width:150px !important;"><?php _e( 'URL Brand LKPP', 'woocommerce' ); ?></label>
                    </div>
                    <input type="text" name="lkpp_brand_url" value="<?php echo esc_attr($settings['lkpp_brand_url']); ?>" style="width:25%; margin-left:10px;"/>
                </p>
            </div>
            <p class="submit" style="clear: both;">
                <input type="submit" name="Submit"  class="button-primary" value="Save Settings" />
                <input type="hidden" name="action" value="save_lkpp_info" />
            </p>
        </form>
        <?php
    } else {
        ?>
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <?php
            $settings = get_option( "lkpp_settings" );
            wp_nonce_field( "lkpp-settings-secret" ); 
            ?>
            <div class="options-group">
                <p class="form-field">
                    <div style="width:150px; display:inline-block;">
                        <label for="lkpp_secret"><?php _e( 'Datafeed Secret Key', 'woocommerce' ); ?></label>
                    </div>
                    <input type="text" name="lkpp_secret" value="<?php echo esc_attr($settings['lkpp_secret']); ?>" style="width:20%; margin-left:10px; margin-right:10px;"/>
                    <button class="button-primary" id="lkpp_secret_button"> Generate Secret Key</button>
                </p>
            </div>       
            <p class="submit" style="clear: both;">
                <input type="submit" name="Submit"  class="button-primary" value="Save Settings" />
                <input type="hidden" name="action" value="save_lkpp_secret" />
            </p>
        </form>
        <?php
    }     
} else {
    lkpp_settings_tabs('lkpp_info');
    ?>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <?php
        $settings = get_option( "lkpp_settings" );

        wp_nonce_field( "lkpp-settings-info" ); 
        ?>
        <div class="options-group">
            <p class="form-field">
                <div style="width:150px; display:inline-block;">
                    <label for="lkpp_id"><?php _e( 'ID Penyedia LKPP', 'woocommerce' ); ?></label>
                </div>
                <input type="text" name="lkpp_id" value="<?php echo esc_attr($settings['lkpp_id']); ?>" style="width:5%; margin-left:10px;"/>
            </p>
            <p class="form-field">
                <div style="width:150px; display:inline-block;">
                    <label for="lkpp_secret_access" style="width:150px !important;"><?php _e( 'Akses Secret Key', 'woocommerce' ); ?></label>
                </div>
                <input type="text" name="lkpp_secret_access" value="<?php echo esc_attr($settings['lkpp_secret_access']); ?>" style="width:20%; margin-left:10px;"/>
            </p>
            <p class="form-field">
                <div style="width:150px; display:inline-block;">
                    <label for="lkpp_categ_url" style="width:150px !important;"><?php _e( 'URL Kategori LKPP', 'woocommerce' ); ?></label>
                </div>
                <input type="text" name="lkpp_categ_url" value="<?php echo esc_attr($settings['lkpp_categ_url']); ?>" style="width:25%; margin-left:10px;"/>
            </p>
            <p class="form-field">
                <div style="width:150px; display:inline-block;">
                    <label for="lkpp_brand_url" style="width:150px !important;"><?php _e( 'URL Brand LKPP', 'woocommerce' ); ?></label>
                </div>
                <input type="text" name="lkpp_brand_url" value="<?php echo esc_attr($settings['lkpp_brand_url']); ?>" style="width:25%; margin-left:10px;"/>
            </p>
        </div>
        <p class="submit" style="clear: both;">
            <input type="submit" name="Submit"  class="button-primary" value="Save Settings" />
            <input type="hidden" name="action" value="save_lkpp_settings" />
        </p>
    </form>
    <?php
}
