<?php
require_once __DIR__ . '/vendor/printful/php-api-sdk/src/PrintfulApiClient.php';
require_once __DIR__ . '/vendor/autoload.php';
use Printful\PrintfulApiClient;

if(isset($_GET['info'])) {  
    $info = $_GET['info'];
    $arr = explode(':', $info);
    $id = $arr[1];
    //echo "alert(\"" . @$id . "\");";
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);

    $preciosMin = $pf->get('products/variant/@' . $id);

//Obtener el precio mínimo del producto
foreach ($preciosMin as $precio) {
    foreach ($precio as $precio_key => $precio_value) {
        if ($precio_key == 'price') {
            $precio = $precio_value;
            //echo "alert(\"" . @$precio . "\");";
            echo "jQuery('#minimo').val(\"" . @$precio . "€\");";
        }
    }
}
} else {
    //echo 'fail';    
}