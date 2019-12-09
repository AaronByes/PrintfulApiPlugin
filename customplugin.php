<?php
/*
Plugin Name: API Printful
Plugin URI: https://envidea.es
description: Plugin para subir productos a Printful
Version: 1.0.0
Author: Envidea Multimedia
Author URI: https://envidea.es
 */

require_once __DIR__ . '/vendor/printful/php-api-sdk/src/PrintfulApiClient.php';
require_once __DIR__ . '/vendor/autoload.php';
use Printful\PrintfulApiClient;

// Add menu
function customplugin_menu()
{
    add_menu_page("Crear Producto", "Crear Producto", "manage_options", "api_printful", "uploadfile", 'dashicons-arrow-up-alt');
    wp_enqueue_style('carga_css', plugin_dir_url(__FILE__) . 'css/style.css', false, '1.0', 'all');
    wp_enqueue_script('my_js', plugin_dir_url(__FILE__) . 'js/productos.js', array('jquery'), 10, '1.0', true);
    wp_localize_script('my_js', 'myScript', array('pluginsUrl' => plugin_dir_url(__FILE__)));
    wp_localize_script('my_js', 'my_ajax_url', admin_url('admin-ajax.php'));
}

add_action("admin_menu", "customplugin_menu");

function uploadfile()
{
    include "uploadfile.php";
}

//Métodos para eliminar productos en la tabla y a previsualizar
add_action('wp_ajax_my_action', 'my_action');

//Eliminar producto en la tabla
function my_action()
{
    $printful = isset($_POST['compare']) ? $_POST['compare'] : 'N/A';
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $path = dirname(__FILE__) . '/productos1.json';
    $url = "http://plantilla.envidoo.es/wp-content/plugins/PrintfulApiPlugin/productos1.json";
    $headers = get_headers($url);

    if ($headers[0] == 'HTTP/1.1 200 OK') //La URL existe
    {
        $json = file_get_contents($url);
        $obj = json_decode($json, true);
        foreach ($obj as $key => $valor) {
            foreach ($valor as $key_valor => $valor_valor) {
                if ($siguiente == true) {
                    //echo $valor_valor;
                    wp_delete_post($valor_valor, true);
                }
                $siguiente = false;

                if ($valor_valor == $printful) {
                    $siguiente = true;
                    //echo $valor_valor;
                }
            }
        }
    }
    $json = json_encode($obj);
    $pf->delete('store/products/@' . $printful);
    wp_die();
}

add_action('wp_ajax_my_action_preview', 'my_action_preview');

//Eliminar producto del preview
function my_action_preview()
{
  $woocommerce = isset($_POST['woocommerce']) ? $_POST['woocommerce'] : 'N/A';
  $printful = isset($_POST['printful']) ? $_POST['printful'] : 'N/A';
  $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
  $pf = new PrintfulApiClient($apiKey);
  $woocommerce = $_POST['woocommerce'];
  $printful = $_POST['printful'];
  $pf->delete('store/products/@' . $printful);
  wp_delete_post($woocommerce, true);
  wp_die();
}
