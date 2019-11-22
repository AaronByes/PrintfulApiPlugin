<?php
include 'printful.php';
include 'minCost.php';
/*if(isset($_POST['value'])) {  
    $aid = $_POST['value'];
    echo $aid;
} else {
    echo 'fail';    
}*/

// Upload file
if (isset($_POST['but_submit'])) {
    if ($_FILES['file']['name'] != '') {
        $uploadedfile = $_FILES['file'];
        $nombre_producto = $_POST['nombre'];
        $precio_venta = $_POST['precio_venta'];
        $tipo_producto = $_POST['productos'];
        $size_poster = $_POST['posters'];
        $arr_poster = explode(':', $size_poster);
        $size_lienzo = $_POST['lienzos'];
        $arr_lienzo = explode(':', $size_lienzo);
        $upload_overrides = array('test_form' => false);
        
        
        
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        

        $imageurl = "";
        if ($movefile && !isset($movefile['error'])) {
            $imageurl = $movefile['url'];
            //echo "<img width='40px' style='text-align:left;' src=$imageurl></img>";
            
            if($tipo_producto == "poster") {
                $id = (int)$arr_poster[1];
                $size = $arr_poster[0];
                $size_image = getimagesize($imageurl);
                echo crearProducto($imageurl, $nombre_producto, $precio_venta, $id, $size);
                echo findPrintfileId('268', $id, $size_image, $imageurl, $nombre_producto, $size, $precio_venta);
            }

            if($tipo_producto == "lienzo") {
                $id = (int)$arr_lienzo[1];
                $size = $arr_lienzo[0];
                $size_image = getimagesize($imageurl);
                echo crearProducto($imageurl, $nombre_producto, $precio_venta, $id, $size);
                echo findPrintfileId('3', $id, $size_image, $imageurl, $nombre_producto, $size, $precio_venta);
            }
        } else {
            echo $movefile['error'];
        }
    }
}
?>
<!-- Form -->
<form method='post' action='' name='myform' id='myform' enctype='multipart/form-data'>
  <table class="table-productos">
    <tr>
        <td>
            <label for="nombre">Nombre del Producto: </label> <input type="text" id="nombre" name="nombre"></td>
    </tr>
    <tr>
    <td>
        <table>
            <tr>
            <th>Poster</th>
            <th>Lienzo</th>
            <th>Camisetas</th>
            </tr>
            <tr>
                <td>
        <input type="radio" class="sr-only" name="productos" id="poster" value="poster"/>
                    <label for="poster"><img src="../wp-content/plugins/PrintfulApiPlugin/img/poster.jpg" alt="poster" /></label>
                    </td>
                    <td>
        <input type="radio" class="sr-only" name="productos" id="lienzo"value="lienzo" />
                    <label for="lienzo"><img src="../wp-content/plugins/PrintfulApiPlugin/img/lienzo.jpg" alt="lienzo" /></label>
                    </td>
                    <td>
        <input type="radio" class="sr-only" name="productos" id="camiseta" value="camiseta"/>
                    <label for="camiseta"><img src="../wp-content/plugins/PrintfulApiPlugin/img/camisetas.jpg" alt="camisetas" /></label>
                    </td>
        </td>
        </table>
    </tr>
    <tr>
        <!-- <td>
            <label for="tipo_producto">Tipo de producto: </label>
            <select name="tipo_producto" id="tipo_producto" onchange="checkOption()" style="width: 50%">
                <option name="radio" value="elige_producto">Elige tu producto</option>
                <option name="radio" value="poster">Poster</option>
                <option name="radio" value="lienzo">Lienzo</option>
                </select>           
        </td>
         -->
        <td>
            <select name="posters" id="posters" style="display: none;" onchange="">
                <option name="radio_poster" value="elige_tamano">Elige tu tamaño</option>
                <option name="radio_poster" value="21x30:8947" id="8947">21x30</option>
                <option name="radio_poster" value="30x40:8948" id="8948">30x40</option>
                <option name="radio_poster" value="50x70:8952" id="8952">50x70</option>
                <option name="radio_poster" value="61x91:8953" id="8953">61x91</option>
                <option name="radio_poster" value="70x100:8954" id="8954">70x100</option>
            </select>
            <select name="lienzos" id="lienzos" style="display: none;">
                <option name="radio_lienzo" value="elige_tamano">Elige tu tamaño</option>
                <option name="radio_lienzo" value="16x20:6" id="6">16x20</option>
                <option name="radio_lienzo" value="18x24:7" id="7">18x24</option>
                <option name="radio_lienzo" value="24x36:825" id="825">24x36</option>
                <option name="radio_lienzo" value="16x16:824" id="824">16x16</option>
                <option name="radio_lienzo" value="12x16:5" id="5">12x16</option>
                <option name="radio_lienzo" value="12x12:823" id="823">12x12</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="minimo">Precio de Coste Art Hackers: </label>
            <input type="text" value="" id="minimo" name="minimo" readonly>
            <!---<label name="porcentaje_producto">10%</label>--->
        </td> 
    </tr>
    <tr>
        <td>
            <label for="precio_venta">Precio de Venta: </label>
            <input type="decimal" value="" id="precio_venta" name="precio_venta">
        </td> 
    </tr>
    <tr>
        <td><input type='file' name='file' id='file'></td>
        <td><div id="contenedor"><img  class="ui-widget-content" id="img-preview" width="300" height="300" style="display: none;"/></div></td>
    </tr>
    <tr>
      <td><input type='submit' name='but_submit' class="button button-primary" value='Previsualizar'></td>
    </tr>
  </table>

  <div><?php echo mostrarProducto(); ?></div>
  <!-- The Modal -->
<div id="myModal" class="modal" style="display: none;">

<!-- Modal content -->
<div class="modal-content">
  <span class="close">&times;</span>
  <p>Some text in the Modal..</p>
</div>

</div>
</form>