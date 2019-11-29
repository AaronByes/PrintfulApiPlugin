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
                    <label for="poster"><img id="tipo_poster" src="../wp-content/plugins/PrintfulApiPlugin/img/poster.jpg" alt="poster" /></label>
                </td>
                <td>
                    <input type="radio" class="sr-only" name="productos" id="lienzo"value="lienzo" />
                    <label for="lienzo"><img id="tipo_lienzo" src="../wp-content/plugins/PrintfulApiPlugin/img/lienzo.jpg" alt="lienzo" /></label>
                </td>
                <td>
                    <input type="radio" class="sr-only" name="productos" id="camiseta" value="camiseta"/>
                    <label for="camiseta"><img id="tipo_camiseta" src="../wp-content/plugins/PrintfulApiPlugin/img/camisetas.jpg" alt="camisetas" /></label>
                </td>
        </td>
        </tr>
        </table>
    </tr>
    <tr>
        <td>
        <table  id="posters" style="display: none;">
            <tr>
            <th>21x30</th>
            <th>30x40</th>
            <th>50x70</th>
            <th>61x91</th>
            <th>70x100</th>
            </tr>
            <tr>
                <td>
                    <img class="posters" src="../wp-content/plugins/PrintfulApiPlugin/img/Posters/21x30.jpg" id="21x30:8947" value="21x30:8947" alt="poster21x30"/>
                    </td>
                    <td><img class="posters" src="../wp-content/plugins/PrintfulApiPlugin/img/Posters/30x40.jpg" value="61x91:8953" alt="poster30x40" />
                    </td>
                    <td><img class="posters" src="../wp-content/plugins/PrintfulApiPlugin/img/Posters/50x70.jpg"   value="30x40:8948" alt="poster50x70" />
                    </td>
                    <td><img class="posters" src="../wp-content/plugins/PrintfulApiPlugin/img/Posters/61x91.jpg" alt="poster61x91" value="61x91:8953" />
                    </td>
                    <td><img class="posters"  src="../wp-content/plugins/PrintfulApiPlugin/img/Posters/70x100.jpg" alt="poster70x100" value="70x100:8954" >
                    </td>
                </td>
                <input type="text" name="posters" id="posterSelected" style="display: none;">
        </table>
        <table  id="lienzos" style="display: none;">
            <tr>
            <th>12x12</th>
            <th>12x16</th>
            <th>16x16</th>
            <th>24x36</th>
            <th>18x24</th>
            <th>16x20</th>
            </tr>
            <tr>
                <td>
                    <img class="lienzos" src="../wp-content/plugins/PrintfulApiPlugin/img/Lienzos/12x12.jpg"  value="12x12:823" alt="lienzo12x12"/>
                    </td>
                    <td><img class="lienzos" src="../wp-content/plugins/PrintfulApiPlugin/img/Lienzos/12x16.jpg" value="12x16:5" alt="lienzo12x16" />
                    </td>
                    <td><img class="lienzos" src="../wp-content/plugins/PrintfulApiPlugin/img/Lienzos/16x16.jpg"   value="16x16:824" alt="lienzo16x16" />
                    </td>
                    <td><img class="lienzos" src="../wp-content/plugins/PrintfulApiPlugin/img/Lienzos/24x36.jpg" value="24x36:825" alt="lienzo24x36"  />
                    </td>
                    <td><img class="lienzos"  src="../wp-content/plugins/PrintfulApiPlugin/img/Lienzos/18x24.jpg" value="18x24:7" alt="lienzo18x24"  >
                    </td>
                    <td><img class="lienzos"  src="../wp-content/plugins/PrintfulApiPlugin/img/Lienzos/16x20.jpg"  value="16x20:6" alt="lienzo16x20" >
                    </td>
                </td>
                <input type="text" name="lienzos" id="lienzoSelected" style="display: none;">
        </table>
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
        <td><div id="canvas"></div></td>
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