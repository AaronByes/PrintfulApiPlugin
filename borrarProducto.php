<?php
require_once __DIR__ . '/vendor/printful/php-api-sdk/src/PrintfulApiClient.php';
require_once __DIR__ . '/vendor/autoload.php';
use Printful\PrintfulApiClient;

if (isset($_GET['woocommerce']) && isset($_GET['printful'])) {
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $woocommerce = $_GET['woocommerce'];
    $printful = $_GET['printful'];
    $pf->delete('store/products/@' . $printful);
    //wh_deleteProduct($woocommerce, TRUE);
    echo wp_delete_post( $woocommerce, false);
} else {
    //echo 'fail';
}

function wh_deleteProduct($id, $force = FALSE)
{
    $product = wc_get_product($id);

    if(empty($product))
        return new WP_Error(999, sprintf(__('No %s is associated with #%d', 'woocommerce'), 'product', $id));

    // If we're forcing, then delete permanently.
    if ($force)
    {
        if ($product->is_type('variable'))
        {
            foreach ($product->get_children() as $child_id)
            {
                $child = wc_get_product($child_id);
                $child->delete(true);
            }
        }
        elseif ($product->is_type('grouped'))
        {
            foreach ($product->get_children() as $child_id)
            {
                $child = wc_get_product($child_id);
                $child->set_parent_id(0);
                $child->save();
            }
        }

        $product->delete(true);
        $result = $product->get_id() > 0 ? false : true;
    }
    else
    {
        $product->delete();
        $result = 'trash' === $product->get_status();
    }

    if (!$result)
    {
        return new WP_Error(999, sprintf(__('This %s cannot be deleted', 'woocommerce'), 'product'));
    }

    // Delete parent product transients.
    if ($parent_id = wp_get_post_parent_id($id))
    {
        wc_delete_product_transients($parent_id);
    }
    return true;
}
