function checkOption() {
    var selected = jQuery('#tipo_producto :selected').text();
    if (selected == 'Poster') {
        jQuery("#posters").show();
        jQuery("#lienzos").hide();
    }

    if (selected == 'Lienzo') {
        jQuery("#lienzos").show();
        jQuery("#posters").hide();
    }
}


jQuery("input[name='productos']").click(function () {
    var Seleccion = jQuery("input[name='productos']:checked").val();
    if (Seleccion == 'poster') {
        jQuery("#posters").show();
        jQuery("#lienzos").hide();
        jQuery("#minimo").val(" ");
        jQuery("#lienzos").Attr("");
    }

    if (Seleccion == 'lienzo') {
        jQuery("#lienzos").show();
        jQuery("#posters").hide();
        jQuery("#minimo").val('');
    }
});

var selected = jQuery('#tipo_producto :selected').text();
if (selected == 'Poster') {
    jQuery("#posters").show();
    jQuery("#lienzos").hide();
}

if (selected == 'Lienzo') {
    jQuery("#lienzos").show();
    jQuery("#posters").hide();
}

//Onchange para obtener los precios minimos de cada producto
jQuery(document).on('change', '#posters', function () {
    var value = jQuery(this).val();
    console.log(value);

    d = document.createElement("script");
    d.src = myScript.pluginsUrl + "minCost.php?info=" + value;
    d.type = "text/javascript";
    document.body.appendChild(d);

    //Crear canvas con el tamaño del poster al seleccionarlo
    jQuery('#canvas').html('');
    var newCanvas = jQuery('<canvas/>', {
        'class': 'canvasProducto',
        id: 'myCanvas'
    }).prop({
        width: 200,
        height: 200
    });
    jQuery('#canvas').append(newCanvas);
});

jQuery(document).on('change', '#lienzos', function () {
    var value = jQuery(this).val();
    console.log(value);

    d = document.createElement("script");
    d.src = myScript.pluginsUrl + "minCost.php?info=" + value;
    d.type = "text/javascript";
    document.body.appendChild(d);

    //Crear canvas con el tamaño del lienzo al seleccionarlo
    jQuery('#canvas').html('');
    var newCanvas = jQuery('<canvas/>', {
        'class': 'canvasProducto',
        id: 'myCanvas'
    }).prop({
        width: 500,
        height: 500
    });
    jQuery('#canvas').append(newCanvas);
});

//Preview de las imagenes al subirlas
jQuery("#file").change(function () {
    filePreview(this);
});
jQuery("#crear").click(function () {
    jQuery("#TB_window").hide();
    jQuery("#TB_window").css("display", "none");

});

function filePreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            //jQuery('#myform + img').remove();
            //jQuery('#file').after('<img id="img-preview" src="' + e.target.result + '" width="300" height="300"/>');
            //jQuery('#img-preview').attr("src", e.target.result);
            //jQuery("#img-preview").css("display", "block");

            /*jQuery("#img-preview").draggable({
                containment: "#contenedor"
            });
            jQuery("#img-preview").resizable({
                containment: "#contenedor"
            });*/

            var canvas = document.getElementById('myCanvas');
            const context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
            var img = new Image();
            img.onload = draw;
            img.onerror = failed;
            img.src = URL.createObjectURL(input.files[0]);

            var canvasOffset = jQuery("#myCanvas").offset();
            var offsetX = canvasOffset.left;
            var offsetY = canvasOffset.top;
            var canvasWidth = canvas.width;
            var canvasHeight = canvas.height;
            var isDragging = false;

            function handleMouseDown(e) {
                canMouseX = parseInt(e.clientX - offsetX);
                canMouseY = parseInt(e.clientY - offsetY);
                // set the drag flag
                isDragging = true;
            }
            
            function handleMouseUp(e) {
                canMouseX = parseInt(e.clientX - offsetX);
                canMouseY = parseInt(e.clientY - offsetY);
                // clear the drag flag
                isDragging = false;
            }
            
            function handleMouseOut(e) {
                canMouseX = parseInt(e.clientX - offsetX);
                canMouseY = parseInt(e.clientY - offsetY);
                // user has left the canvas, so clear the drag flag
                //isDragging=false;
            }
            
            function handleMouseMove(e) {
                canMouseX = parseInt(e.clientX - offsetX);
                canMouseY = parseInt(e.clientY - offsetY);
                // if the drag flag is set, clear the canvas and draw the image
                if (isDragging) {
                    ctx.clearRect(0, 0, canvasWidth, canvasHeight);
                    ctx.drawImage(img, canMouseX - 128 / 2, canMouseY - 120 / 2, 128, 120);
                }
            }

            jQuery("#myCanvas").mousedown(function (e) { handleMouseDown(e); });
            jQuery("#myCanvas").mousemove(function (e) { handleMouseMove(e); });
            jQuery("#myCanvas").mouseup(function (e) { handleMouseUp(e); });
            jQuery("#myCanvas").mouseout(function (e) { handleMouseOut(e); });
        }
        reader.readAsDataURL(input.files[0]);
    }
}

//Dibujar la imagen sobre el Canvas
function draw() {
    var canvas = document.getElementById('myCanvas');
    //canvas.width = 200;
    //canvas.height = 200;
    var ctx = canvas.getContext('2d');
    ctx.drawImage(this, 0, 0);
}

function failed() {
    console.error("The provided file couldn't be loaded as an Image media");
}

//Enviar los id a PHP 
function borrarPost(param1, param2) {
    alert(param1);
    alert(param2);
    jQuery.ajax({
        type: "POST",
        url: "borrarProducto.php",
        data: { id1: param1, id2: param2 },
        success: function (html) {
            alert(html);
        }
    });
}
