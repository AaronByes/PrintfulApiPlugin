<?php
/*
  Plugin Name: API Printful
  Plugin URI: https://envidea.es
  description: Plugin para subir productos a Printful
  Version: 1.0.0
  Author: Envidea Multimedia
  Author URI: https://envidea.es
*/

// Add menu
function customplugin_menu() {
  add_menu_page("Crear Producto", "Crear Producto","manage_options", "api_printful", "uploadfile", 'dashicons-arrow-up-alt'); 
  wp_enqueue_style('carga_css', plugin_dir_url( __FILE__ ) . 'css/style.css', false, '1.0', 'all');
  wp_enqueue_script( 'my_js', plugin_dir_url( __FILE__ ) . 'js/productos.js', array('jquery'), 10, '1.0', true);
  wp_localize_script('my_js', 'myScript', array('pluginsUrl' => plugin_dir_url( __FILE__ )));
}

add_action("admin_menu", "customplugin_menu");

function uploadfile(){
  include "uploadfile.php";
}