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
                    //Eliminar el producto de woocommerce y eliminarlo del json
                    wp_delete_post($valor_valor, true);
                    unset($obj[$key]);
                    $json = json_encode($obj);
                    file_put_contents($path, $json);
                }
                $siguiente = false;

                if ($valor_valor == $printful) {
                    $siguiente = true;
                }
            }
        }

        //Eliminar el producto de printful
        $pf->delete('store/products/@' . $printful);
    }
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

//Recoger la información del pedido y pasarla a printful
function wc_register_guests($order_id)
{
    $order = new WC_Order($order_id);
    $order_data = $order->get_data();
    $order_items = $order->get_items();
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $path = dirname(__FILE__) . '/productos1.json';
    $url = "http://plantilla.envidoo.es/wp-content/plugins/PrintfulApiPlugin/productos1.json";
    $headers = get_headers($url);

    //Obtener todos los datos de facturación del pedido
    $order_shipping_first_name = $order_data['shipping']['first_name'];
    $order_shipping_last_name = $order_data['shipping']['last_name'];
    $order_shipping_company = $order_data['shipping']['company'];
    $order_shipping_address_1 = $order_data['shipping']['address_1'];
    $order_shipping_address_2 = $order_data['shipping']['address_2'];
    $order_shipping_city = $order_data['shipping']['city'];
    $order_shipping_state = $order_data['shipping']['state'];
    $order_shipping_postcode = $order_data['shipping']['postcode'];
    $order_shipping_country = $order_data['shipping']['country'];

    //Crear el pedido de printful
    $new_order;
    $order_printful = array(
        'recipient' => array(
            'name' => $order_shipping_first_name . ' ' . $order_shipping_last_name,
            'address1' => $order_shipping_address_1,
            'city' => $order_shipping_city,
            'state_code' => $order_shipping_state,
            'country_code' => $order_shipping_country,
            'zip' => $order_shipping_postcode,
        ),
        'items' => [],
    );

    //Obtener id del producto de woocommerce
    foreach ($order_items as $item) {
        $product_id = $item->get_product_id();
        $product_quantity = $item->get_quantity();
        $product_woo = wc_get_product($product_id);
        $product_price = $product_woo->get_price();
        //echo "ID:   " . $product_id;
        //echo WC()->countries->countries[$order_shipping_country];

        //Obtener el id de printful
        if ($headers[0] == 'HTTP/1.1 200 OK') //La URL existe
        {
            $json = file_get_contents($url);
            $obj = json_decode($json, true);

            foreach ($obj as $key => $valor) {
                foreach ($valor as $key_valor => $valor_valor) {
                    if ($valor_valor == $product_id) {
                        $siguiente = true;
                        $variant_id_array = $pf->get('store/products/@' . $id);

                        //Recorrer el array para obtener el id de la variante y crear el pedido en printful
                        foreach ($variant_id_array as $variant_id_array_key => $variant_id_array_valor) {
                            foreach ($variant_id_array_valor as $variant_id_array_key_2 => $variant_id_array_valor_2) {
                                foreach ($variant_id_array_valor_2 as $variant_id_array_key_3 => $variant_id_array_valor_3) {
                                    if ($variant_id_array_key_3 == "external_id") {
                                        $variant_id = $variant_id_array_valor_3;

                                        //Añadir los productos de printful al pedido
                                        $new_item = array(
                                            'external_variant_id' => $variant_id,
                                            'quantity' => $product_quantity,
                                            'retail_price' => $product_price,
                                        );

                                        //Añadir los productos al array del pedido
                                        array_push($order_printful['items'], $new_item);
                                        $new_order = $order_printful;
                                    }
                                }
                            }
                        }
                    }
                    //Asignar el id de printful obtenido del json
                    $id = $valor_valor;
                }
            }
        }
    }

    //Crear el pedido en printful
    try {
        $pf->post('orders', $new_order);
    } catch (PrintfulApiException $e) { //API response status code was not successful
        echo 'Printful API Exception: ' . $e->getCode() . ' ' . $e->getMessage();
    } catch (PrintfulException $e) { //API call failed
        echo 'Printful Exception: ' . $e->getMessage();
        var_export($pf->getLastResponseRaw());
    }
}

//call our wc_register_guests() function on the thank you page
add_action('woocommerce_thankyou', 'wc_register_guests', 10, 1);
