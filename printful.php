
<?php
require_once __DIR__ . '/vendor/printful/php-api-sdk/src/PrintfulApiClient.php';
require_once __DIR__ . '/vendor/autoload.php';

use Printful\PrintfulApiClient;

function crearProducto($url, $nombre_producto, $precio, $id, $size)
{
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);

    // Create new product in Printful
    $rates = array(
        "sync_product" => array(
            'name' => $nombre_producto,
            'thumbnail' => $url,
        ),
        'sync_variants' => [array(
            'retail_price' => $precio,
            'variant_id' => $id,
            'files' => [array(
                'url' => $url,
            )],
        )],
    );
    //var_export(json_encode($rates, JSON_UNESCAPED_SLASHES));

    try {
        $pf->post('store/products', $rates);
        echo "<div class='notice inline notice-success  is-dismissible'>
                <p>Producto creado correctamente.</p>
                <button type='button' class='notice-dismiss'><span class='screen-reader-text'>Descartar</span></button>
            </div>";
    } catch (PrintfulApiException $e) { //API response status code was not successful
        //echo 'Printful API Exception: ' . $e->getCode() . ' ' . $e->getMessage();
        echo "Ha ocurrido un error al crear el producto";
    } catch (PrintfulException $e) { //API call failed
        //echo 'Printful Exception: ' . $e->getMessage();
        //var_export($pf->getLastResponseRaw());
        echo "Ha ocurrido un error al crear el producto";
    }
}

function findPrintfileId($idProducto, $variantId, $image_size, $image_url, $nombre, $product_size, $precio, $imagewidth, $imageheight, $imageleft, $imagetop) {
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $printfiles = $pf->get('mockup-generator/printfiles/' . $idProducto);
    $printfile_id = '';
    $isVariant = false;
    foreach($printfiles as $key => $value) {
        if($key == "variant_printfiles") {
            foreach($value as $key_printfiles => $value_printfiles) {
                foreach($value_printfiles as $key_printfiles_2 => $value_printfiles_2) {
                        if($value_printfiles_2 == $variantId) {
                            $isVariant = true;
                        }

                        if($key_printfiles_2 == "placements" && $isVariant) {
                            foreach($value_printfiles_2 as $key_printfiles_3 => $value_printfiles_3) {
                                $printfile_id = $value_printfiles_3;
                                //Obtener el tamaño de la imagen y llamar al método para obtener el tamaño del lienzo
                                $width  = $image_size[0];
                                $height = $image_size[1];
                                findAreaWidthHeight($printfile_id, $idProducto, $variantId, $image_url, $nombre, $product_size, $precio, $imagewidth, $imageheight, $imageleft, $imagetop);
                            }
                            $isVariant = false;
                        }
                }
            }
        }
    }
}

function findAreaWidthHeight($printfileid, $id, $variant_id, $imageUrl, $nombreProd , $sizeProd, $precioProd, $imageWidth, $imageHeight, $imageLeft, $imageTop) {
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $printfiles = $pf->get('mockup-generator/printfiles/' . $id);
    $isWidth = false;
    $isHeight = false;
    $area_width = '';
    $area_height = '';
    foreach($printfiles as $key => $value) {
        if($key == "printfiles") {
            foreach($value as $key_printfiles => $value_printfiles) {
                foreach($value_printfiles as $key_printfiles_2 => $value_printfiles_2) {
                        if($key_printfiles_2 == 'printfile_id' && $value_printfiles_2 == $printfileid) {
                            $isWidth = true;
                            $isHeight = true;
                        }

                        if($key_printfiles_2 == "width" && $isWidth) {
                            $isWidth = false;
                            $area_width = $value_printfiles_2;
                            //echo "El width es: ". $value_printfiles_2 . ". " ;
                        }

                        if($key_printfiles_2 == "height" && $isHeight) {
                            $isHeight = false;
                            $area_height = $value_printfiles_2;
                            //echo "El height es: " . $value_printfiles_2 . ". ";
                        }
                }
            }

            generateMockup($id, $variant_id, $area_width, $area_height, $imageUrl, $nombreProd , $sizeProd, $precioProd, $imageWidth, $imageHeight, $imageLeft, $imageTop);
        }
    }
}

function generateMockup($id_product, $variant_id, $area_width, $area_height, $image_url, $nombreProducto , $sizeProducto , $precioProducto, $image_width, $image_height, $image_left, $image_top) {
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    //$left = ((int)$area_width/2) - ((int)$image_width/2);
    //$top = ((int)$area_height/2) - ((int)$image_height/2);
    $images = [];
     // Create new product in Printful
     $mockup = array(
        'variant_ids' => [$variant_id],
        'format' => 'jpg',
        'files' => [array(
            'placement'=> 'default',
            'image_url' => $image_url,
            'position' => array(
                'area_width' => $area_width,
                'area_height' => $area_height,
                'width' => $image_width,
                'height' => $image_height,
                'top' => $image_top,
                'left' => $image_left
            )
        )],
    );

    try {
        $task_response = $pf->post('mockup-generator/create-task/' . $id_product, $mockup);
        sleep(5);
        //Recorrer la respuesta para obtener la task key y así obtener las imagenes con la imagen en vista previa
        foreach($task_response as $task_key => $task_value) {
            if($task_key == 'task_key') {
                $mockup_images = $pf->get('mockup-generator/task?task_key=' . $task_value);

                foreach($mockup_images as $mockup_key => $mockup_value) {
                    //echo $mockup_key . " => " . $mockup_value . "<br>";
                    if($mockup_key == "mockups") {
                        foreach($mockup_value as $mockup_key_2 => $mockup_value_2) {
                            foreach($mockup_value_2 as $mockup_key_3 => $mockup_value_3) {
                                if($mockup_key_3 == "mockup_url") {
                                    array_push($images, $mockup_value_3);
                                }
                                if($mockup_key_3 == 'extra') {
                                    foreach($mockup_value_3 as $mockup_key_4 => $mockup_value_4) {
                                        foreach($mockup_value_4 as $mockup_key_5 => $mockup_value_5) {
                                            if($mockup_key_5 == "url") {
                                                //echo "Url:  " . $mockup_value_5;
                                                array_push($images, $mockup_value_5);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //print_r($images);
        //Crear producto en Woocommerce
        $arr = explode('x', $sizeProducto);
        $post_id = wp_insert_post(array(
            'post_title' => $nombreProducto,
            'post_type' => 'product',
            'post_status' => 'publish',
            'post_content' => '',
            'post_excerpt' => ''));

        generate_featured_image($images[0], $post_id);
        foreach($images as $image) {
            attach_product_thumbnail($post_id, $image, 1);
        }
        wp_set_object_terms($post_id, 'simple', 'product_type');
        update_post_meta($post_id, '_visibility', 'visible');
        update_post_meta($post_id, '_stock_status', 'instock');
        update_post_meta($post_id, '_downloadable', 'no');
        update_post_meta($post_id, '_virtual', 'no');
        update_post_meta($post_id, '_featured', 'no');
        update_post_meta( $post_id, '_width', $arr[0]);
        update_post_meta( $post_id, '_height', $arr[1]);
        update_post_meta($post_id, '_price', $precioProducto);
        update_post_meta( $post_id, '_product_attributes', array() );
        update_post_meta($post_id, '_manage_stock', 'yes');
        update_post_meta($post_id, '_backorders', 'no');

        writeToLog($post_id);

        //Modal para previsualizar el producto 
        add_thickbox(); 
        echo '<div id="my-content-id" style="display:none;">';
        print_r ($borrar);
                    foreach($images as $image){                        
                     echo '<div><img src=' . $image . ' id="img-preview"></div>';}
                     echo '
                    <div style="text-align: center; margin-bottom: 1em;">
                        <input type="submit" id="crear" onclick="self.parent.tb_remove();" name="but_modificar" class="button button-primary btn-aceptar" style="margin-bottom: 1em;" value="CREAR PRODUCTO">
                        <form method="post" action="" name="borrarform" id="borrarform">
                            <input type="submit" onclick="borrarPost('. $post_id .','. ultimoProducto() .')" id="BorrarProducto" name="but_aceptar" class="button button-primary btn-modificar" value="BORRAR PRODUCTO">
                        </form>
                        </div>
                    
            </div>
            <a href="#TB_inline?&width=600&height=550&inlineId=my-content-id" class="thickbox button button-primary" style="margin-top: 1em;">Previsualizar</a>';
        //echo "Mockup creado correctamente";
    } catch (PrintfulApiException $e) { //API response status code was not successful
        //echo 'Printful API Exception: ' . $e->getCode() . ' ' . $e->getMessage();
        echo "Ha ocurrido un error al crear el producto";
    } catch (PrintfulException $e) { //API call failed
        //echo 'Printful Exception: ' . $e->getMessage();
        //var_export($pf->getLastResponseRaw());
        echo "Ha ocurrido un error al crear el producto";
    }
}

function mostrarProducto()
{
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);

    // Get product list
    try {
        $products = $pf->get('store/products');
        echo "<table class='tabla-productos-final' width='100%' border='1'><tr>
            <th>Precio Coste Art Hackers</th>
            <th>Precio de Venta</th>
            <th>Nombre</th>
            <th>Imagen</th>
            <th>Eliminar</th>
        </tr>";
        foreach ($products as $key => $value) {
            echo "<tr>";
            foreach ($value as $llave => $valor) {
                if ($llave == "external_id") {
                    $variantes = $pf->get("store/products/@" . $valor);

                    //Obtener el precio del producto
                    foreach ($variantes as $variant_key => $variant_value) {
                        if ($variant_key == "sync_variants") {
                            foreach ($variant_value as $variant_key_2 => $variant_value_2) {
                                foreach ($variant_value_2 as $variant_key_3 => $variant_value_3) {
                                    if ($variant_key_3 == "variant_id") {
                                        $preciosPrintful = $pf->get('products/variant/@' . $variant_value_3);

                                        //Obtener el precio mínimo del producto
                                        foreach ($preciosPrintful as $precio) {
                                            foreach ($precio as $precio_key => $precio_value) {
                                                if ($precio_key == 'price') {
                                                    $precio = $precio_value;
                                                    echo "<td><span>$precio" . "€</span></td>";
                                                }
                                            }
                                        }
                                    }

                                    if ($variant_key_3 == "retail_price") {
                                        echo "<td><span>$variant_value_3" . "€</span></td>";
                                    }
                                }
                            }
                        }
                    }
                }

                if ($llave == "name") {
                    echo "<td><span>$valor</span></td>";
                }

                if ($llave == "thumbnail_url") {
                    echo "<td><img src=$valor width='100' /></td>";
                    echo "<td><button id='Borrar' value='...' class='dashicons dashicons-trash'></td> ";
                }
            }
            echo "</tr>";
        }

        echo "</table>";

    } catch (PrintfulApiException $e) { //API response status code was not successful
        echo 'Printful API Exception: ' . $e->getCode() . ' ' . $e->getMessage();
    } catch (PrintfulException $e) { //API call failed
        echo 'Printful Exception: ' . $e->getMessage();
        var_export($pf->getLastResponseRaw());
    }
}

//Create json file to store products
function writeToLog($idWoocommerce)
{
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $products = $pf->get('store/products');  
    $reverse_products = array_reverse($products);

    foreach ($reverse_products as $key => $value) {
        foreach ($value as $llave => $valor) {
            if ($llave == "external_id") {
                $idPrintful = $valor;
            } 
        }
    }
    //echo "conexion: [ { idWoocommerce: " . $idWoocommerce ." /n " . "idPrintful: " . $idPrintful . " },]";
    $path = dirname(__FILE__) . '/productos1.json';
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $url="plantilla.envidoo.es/wp-content/plugins/PrintfulApiPlugin/productos1.json";
    $urlexists = url_exists($url);
    echo $urlexists;

    if (($h = fopen($path, "a")) !== false) {
        fwrite($h,$urlexists . "{'conexion': [ { 'idWoocommerce': '" . $idWoocommerce ." " . "'idPrintful': '" . $idPrintful . "' },]}");
        fclose($h);
    } else {
        die('WHAT IS GOING ON?');
    }

}
//Mirar si existe la ruta del archivo json para crearlo o modificarlo
function url_exists( $url = NULL ){

    if( empty( $url ) ){
        return false;
    }

    $response = wp_remote_head($url);

    // Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
    $accepted_response = array( 200, 301, 302 );
    if( ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), $accepted_response ) ) { 
        return true;
    } else {
         return false;
    }

}

//Recoger el ultimo producto creado para obtener su id
function ultimoProducto()
{
    $apiKey = 'qw9ttqt6-z72u-qf80:ejz1-52lb33te3obg';
    $pf = new PrintfulApiClient($apiKey);
    $products = $pf->get('store/products');
    $reverse_products = array_reverse($products);

    foreach ($reverse_products as $key => $value) {
        foreach ($value as $llave => $valor) {
            if ($llave == "external_id") {
                $idPrintful = $valor;
            }
        }
    }
    return "'" . $idPrintful . "'";
}

//Establecer la imagen del producto como imagen principal
function generate_featured_image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))
      $file = $upload_dir['path'] . '/' . $filename;
    else
      $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}

function attach_product_thumbnail($post_id, $url, $flag){
    /*
     * If allow_url_fopen is enable in php.ini then use this
     */
    $image_url = $url;
    $url_array = explode('/',$url);
    $image_name = $url_array[count($url_array)-1];
    $image_data = file_get_contents($image_url); // Get image data
  /*
   * If allow_url_fopen is not enable in php.ini then use this
   */
  // $image_url = $url;
  // $url_array = explode('/',$url);
  // $image_name = $url_array[count($url_array)-1];
  // $ch = curl_init();
  // curl_setopt ($ch, CURLOPT_URL, $image_url);
  // // Getting binary data
  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  // curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
  // $image_data = curl_exec($ch);
  // curl_close($ch);
  $upload_dir = wp_upload_dir(); // Set upload folder
    $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); //    Generate unique name
    $filename = basename( $unique_file_name ); // Create image file name
    // Check folder permission and define file location
    if( wp_mkdir_p( $upload_dir['path'] ) ) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }
    // Create the image file on the server
    file_put_contents( $file, $image_data );
    // Check image file type
    $wp_filetype = wp_check_filetype( $filename, null );
    // Set attachment data
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name( $filename ),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    // Create the attachment
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    // Include image.php
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    // Assign metadata to attachment
    wp_update_attachment_metadata( $attach_id, $attach_data );
    // asign to feature image
    if( $flag == 0){
        // And finally assign featured image to post
        set_post_thumbnail( $post_id, $attach_id );
    }
    // assign to the product gallery
    if( $flag == 1 ){
        // Add gallery image to product
        $attach_id_array = get_post_meta($post_id,'_product_image_gallery', true);
        $attach_id_array .= ','.$attach_id;
        update_post_meta($post_id,'_product_image_gallery',$attach_id_array);
    }
}

