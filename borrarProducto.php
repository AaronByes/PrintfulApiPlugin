<?php
require_once __DIR__ . '/vendor/printful/php-api-sdk/src/PrintfulApiClient.php';
require_once __DIR__ . '/vendor/autoload.php';
use Printful\PrintfulApiClient;


    $Woocommerce = $_GET['param1'];
    $Printful = $_GET['param2'];

echo $Woocommerce;
echo $Printful;


$product = wc_get_product($Woocommerce);
echo $product;