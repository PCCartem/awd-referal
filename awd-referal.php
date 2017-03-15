<?php
/*
Plugin Name: AWD Referal System
Description:
Version: 1.0
Author: Vedernikov Artem (freeflight.pro@gmail.com)
Author URI: http://variarti.ru
*/
require_once "vendor/autoload.php";



define('AWD_REFERAL_DIR', plugin_dir_path(__FILE__));
define('AWD_REFERAL_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'awd_referal_activation');
register_deactivation_hook(__FILE__, 'awd_referal_deactivation');

function awd_referal_activation() {
    new App\Install();
//    register_uninstall_hook(__FILE__, 'awd_referal_uninstall');
}

function awd_referal_deactivation() {
    new App\Uninstall();
}


add_action('init', function () {
    $app = new App\App();
    $app->run();
});

add_action('admin_menu', function () {
    $app = new App\Admin();
    $app->run();
});

add_action('after_setup_theme', function(){
    if ( ! is_admin() && ! current_user_can('manage_options') )
        show_admin_bar( false );
});