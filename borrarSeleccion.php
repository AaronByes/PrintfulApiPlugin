<?php
require_once __DIR__ . '/vendor/printful/php-api-sdk/src/PrintfulApiClient.php';
require_once __DIR__ . '/vendor/autoload.php';
use Printful\PrintfulApiClient;

if (isset($_POST['compare'])) {
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $printful = $_POST['compare'];

    $path = dirname(__FILE__) . '/productos1.json';
    $url="http://plantilla.envidoo.es/wp-content/plugins/PrintfulApiPlugin/productos1.json";

    $headers = get_headers($url);

    if($headers[0] == 'HTTP/1.1 200 OK') //La URL existe
    {
        $json = file_get_contents($url);
        $obj = json_decode($json,TRUE);
        foreach($obj as $key=>$valor){
            echo 'alert(' . $valor . ')';
        }


    }
    $json = json_encode($obj);
    
    //$pf->delete('store/products/@' . $printful);  
} else {
    echo 'fail';
}