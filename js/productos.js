var canvas;
var ctx;
var canvasOffset;
var offsetX;
var offsetY;
var isDown = false;
var pi2 = Math.PI * 2;
var resizerRadius = 8;
var rr = resizerRadius * resizerRadius;
var draggingResizer = {
    x: 0,
    y: 0
};
var imageX = 50;
var imageY = 50;
var imageWidth, imageHeight, imageRight, imageBottom;
var draggingImage = false;
var startX;
var startY;
var scaleX;
var scaleY;
var rect;
var img;

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
    jQuery("#tipo_lienzo").removeClass('active');
    jQuery("#tipo_poster").removeClass('active');
    jQuery("#tipo_camiseta").removeClass('active');

    if (Seleccion == 'poster') {
        jQuery("#posters").show();
        jQuery("#lienzos").hide();
        jQuery("#minimo").val(" ");
        jQuery("#lienzos").attr(" ");
        jQuery("#tipo_poster").addClass('active');
        jQuery('#canvas').html('');
    }

    if (Seleccion == 'lienzo') {
        jQuery("#lienzos").show();
        jQuery("#posters").hide();
        jQuery("#minimo").val('');
        jQuery("#posters").attr(" ");
        jQuery("#tipo_lienzo").addClass('active');
        jQuery('#canvas').html('');
    }

    if (Seleccion == 'camiseta') {
        jQuery("#tipo_camiseta").addClass('active');
        jQuery('#canvas').html('');
    }
});
jQuery(document).ready(function () {
    jQuery("input[name='posters']:radio").change(function () {
        var value = jQuery("input[name='posters']:checked").val();
        alert(value);
    });
});

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
            //Inicializar variables del canvas
            canvas = document.getElementById("myCanvas");
            ctx = canvas.getContext("2d");
            canvasOffset = jQuery("#myCanvas").offset();

            rect = canvas.getBoundingClientRect();
            scaleX = canvas.width / rect.width;   // relationship bitmap vs. element for X
            scaleY = canvas.height / rect.height;
            offsetX = rect.left;
            offsetY = rect.top;
            //console.log("offsetX", offsetX);
            //console.log("offsetY", offsetY);
            img = new Image();
            img.onload = function () {
                imageWidth = img.width / 4;
                imageHeight = img.height / 4;
                imageRight = imageX + imageWidth;
                imageBottom = imageY + imageHeight;
                draw(true, false);
            }
            img.onerror = failed;
            img.src = URL.createObjectURL(input.files[0]);
            registermouseEvent();
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function draw(withAnchors, withBorders) {
    // clear the canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // draw the image
    ctx.drawImage(img, 0, 0, img.width, img.height, imageX, imageY, imageWidth, imageHeight);

    // optionally draw the draggable anchors
    if (withAnchors) {
        drawDragAnchor(imageX, imageY);
        drawDragAnchor(imageRight, imageY);
        drawDragAnchor(imageRight, imageBottom);
        drawDragAnchor(imageX, imageBottom);
    }

    // optionally draw the connecting anchor lines
    if (withBorders) {
        ctx.beginPath();
        ctx.moveTo(imageX, imageY);
        ctx.lineTo(imageRight, imageY);
        ctx.lineTo(imageRight, imageBottom);
        ctx.lineTo(imageX, imageBottom);
        ctx.closePath();
        ctx.stroke();
    }

}

function drawDragAnchor(x, y) {
    ctx.beginPath();
    ctx.arc(x, y, resizerRadius, 0, pi2, false);
    ctx.closePath();
    ctx.fill();
}

function anchorHitTest(x, y) {
    var dx, dy;

    // top-left
    dx = x - imageX;
    dy = y - imageY;
    if (dx * dx + dy * dy <= rr) {
        return (0);
    }
    // top-right
    dx = x - imageRight;
    dy = y - imageY;
    if (dx * dx + dy * dy <= rr) {
        return (1);
    }
    // bottom-right
    dx = x - imageRight;
    dy = y - imageBottom;
    if (dx * dx + dy * dy <= rr) {
        return (2);
    }
    // bottom-left
    dx = x - imageX;
    dy = y - imageBottom;
    if (dx * dx + dy * dy <= rr) {
        return (3);
    }
    //console.log("dx", dx, "dy", dy);
    //console.log("operacion", (dx * dx + dy * dy));
    return (-1);

}

function hitImage(x, y) {
    return (x > imageX && x < imageX + imageWidth && y > imageY && y < imageY + imageHeight);
}

function handleMouseDown(e) {
    startX = parseInt(e.clientX - offsetX) * scaleX;
    startY = parseInt(e.clientY - offsetY) * scaleY;
    draggingResizer = anchorHitTest(startX, startY);
    draggingImage = draggingResizer < 0 && hitImage(startX, startY);
    //console.log("start X", startX, "start Y", startY);
    //console.log("draggingResizer", draggingResizer);
}

function handleMouseUp(e) {
    draggingResizer = -1;
    draggingImage = false;
    draw(true, false);
    /* console.log("new image width:", imageWidth);
    console.log("new image height:", imageHeight);
    console.log("new imageX:", imageX);
    console.log("new imageY:", imageY); */
}

function handleMouseOut(e) {
    handleMouseUp(e);
}

function handleMouseMove(e) {
    if (draggingResizer > -1) {
        mouseX = parseInt(e.clientX - offsetX);
        mouseY = parseInt(e.clientY - offsetY);

        // resize the image
        switch (draggingResizer) {
            case 0:
                //top-left
                imageX = mouseX;
                imageWidth = imageRight - mouseX;
                imageY = mouseY;
                imageHeight = imageBottom - mouseY;
                break;
            case 1:
                //top-right
                imageY = mouseY;
                imageWidth = mouseX - imageX;
                imageHeight = imageBottom - mouseY;
                break;
            case 2:
                //bottom-right
                imageWidth = mouseX - imageX;
                imageHeight = mouseY - imageY;
                break;
            case 3:
                //bottom-left
                imageX = mouseX;
                imageWidth = imageRight - mouseX;
                imageHeight = mouseY - imageY;
                break;
        }

        if (imageWidth < 25) { imageWidth = 25; }
        if (imageHeight < 25) { imageHeight = 25; }

        // set the image right and bottom
        imageRight = imageX + imageWidth;
        imageBottom = imageY + imageHeight;

        // redraw the image with resizing anchors
        draw(true, true);

    } else if (draggingImage) {

        imageClick = false;

        mouseX = parseInt(e.clientX - offsetX);
        mouseY = parseInt(e.clientY - offsetY);

        // move the image by the amount of the latest drag
        var dx = mouseX - startX;
        var dy = mouseY - startY;
        imageX += dx;
        imageY += dy;
        imageRight += dx;
        imageBottom += dy;
        // reset the startXY for next time
        startX = mouseX;
        startY = mouseY;

        // redraw the image with border
        draw(false, true);

    }
}

function failed() {
    console.error("The provided file couldn't be loaded as an Image media");
}

//Enviar los id a PHP 
function borrarPost(param1, param2) {
    //alert(param1);
    //alert(param2);
    jQuery.ajax({
        type: "GET",
        url: myScript.pluginsUrl + "borrarProducto.php",
        data: { woocommerce: param1, printful: param2 },
        success: function (html) {
            alert(html);
        }
    });
}

jQuery(".posters").click(function () {
    var x = jQuery(this).attr('value');
    console.log(x);
    jQuery('.posters').removeClass('active');
    jQuery(this).addClass('active');

    d = document.createElement("script");
    d.src = myScript.pluginsUrl + "minCost.php?info=" + x;
    d.type = "text/javascript";
    document.body.appendChild(d);
    jQuery("#posterSelected").val(x);

    //Convertir el tamaño del poster a px
    var producto_split = x.split(":");
    var producto_size = producto_split[0].split("x");
    var producto_width = parseInt(producto_size[0]);
    var producto_height = parseInt(producto_size[1]);
    var width_pixels = (producto_width * 37.79) / 4;
    var height_pixels = (producto_height * 37.79) / 4;

    //Crear canvas con el tamaño del lienzo al seleccionarlo
    jQuery('#canvas').html('');
    var newCanvas = jQuery('<canvas/>', {
        'class': 'canvasProducto',
        id: 'myCanvas'
    }).prop({
        width: width_pixels,
        height: height_pixels
    });
    jQuery('#canvas').append(newCanvas);
    jQuery('#posicion_canvas').show();
    jQuery('#label_orientacion').show();
});

jQuery(".lienzos").click(function () {
    var x = jQuery(this).attr('value');
    console.log(x);
    jQuery('.lienzos').removeClass('active');
    jQuery(this).addClass('active');

    d = document.createElement("script");
    d.src = myScript.pluginsUrl + "minCost.php?info=" + x;
    d.type = "text/javascript";
    document.body.appendChild(d);
    jQuery("#lienzoSelected").val(x);

    //Convertir el tamaño del lienzo a px
    var producto_split = x.split(":");
    var producto_size = producto_split[0].split("x");
    var producto_width = parseInt(producto_size[0]);
    var producto_height = parseInt(producto_size[1]);
    var width_pixels = (producto_width * 300) / 12;
    var height_pixels = (producto_height * 300) / 12;

    //Crear canvas con el tamaño del lienzo al seleccionarlo
    jQuery('#canvas').html('');
    var newCanvas = jQuery('<canvas/>', {
        'class': 'canvasProducto',
        id: 'myCanvas'
    }).prop({
        width: width_pixels,
        height: height_pixels
    });
    jQuery('#canvas').append(newCanvas);
    jQuery('#posicion_canvas').show();
    jQuery('#label_orientacion').show();
});

//Registrar los eventos del ratón para utilizarlos dentro del canvas
function registermouseEvent() {
    jQuery("#myCanvas").mousedown(function (e) {
        handleMouseDown(e);
    });
    jQuery("#myCanvas").mousemove(function (e) {
        handleMouseMove(e);
    });
    jQuery("#myCanvas").mouseup(function (e) {
        handleMouseUp(e);
    });
    jQuery("#myCanvas").mouseout(function (e) {
        handleMouseOut(e);
    });
}

function checkOrientacion() {
    var selected = jQuery('#posicion_canvas :selected').text();
    var poster = jQuery('.posters.active').attr('value');
    var lienzo = jQuery('.lienzos.active').attr('value');
    var tipo_producto = jQuery("input[name='productos']:checked").val();

    if (tipo_producto == "poster") {
        //Convertir el tamaño del lienzo a px
        var producto_split = poster.split(":");
        var producto_size = producto_split[0].split("x");
        var producto_width = parseInt(producto_size[0]);
        var producto_height = parseInt(producto_size[1]);
        var width_pixels = (producto_width * 37.79) / 4;
        var height_pixels = (producto_height * 37.79) / 4;
        console.log("width", width_pixels);
        console.log("height", height_pixels);

        if (selected == 'Horizontal') {
            //Crear canvas con el tamaño del lienzo al seleccionarlo
            jQuery('#canvas').html('');
            var newCanvas = jQuery('<canvas/>', {
                'class': 'canvasProducto',
                id: 'myCanvas'
            }).prop({
                width: height_pixels,
                height: width_pixels
            });
            jQuery('#canvas').append(newCanvas);
        }

        if (selected == 'Vertical') {
            //Crear canvas con el tamaño del lienzo al seleccionarlo
            jQuery('#canvas').html('');
            var newCanvas = jQuery('<canvas/>', {
                'class': 'canvasProducto',
                id: 'myCanvas'
            }).prop({
                width: width_pixels,
                height: height_pixels
            });
            jQuery('#canvas').append(newCanvas);
        }
    }

    if (tipo_producto == "lienzo") {
        //Convertir el tamaño del lienzo a px
        var producto_split = lienzo.split(":");
        var producto_size = producto_split[0].split("x");
        var producto_width = parseInt(producto_size[0]);
        var producto_height = parseInt(producto_size[1]);
        var width_pixels = (producto_width * 300) / 12;
        var height_pixels = (producto_height * 300) / 12;
        console.log("width", width_pixels);
        console.log("height", height_pixels);

        if (selected == 'Horizontal') {
            //Crear canvas con el tamaño del lienzo al seleccionarlo
            jQuery('#canvas').html('');
            var newCanvas = jQuery('<canvas/>', {
                'class': 'canvasProducto',
                id: 'myCanvas'
            }).prop({
                width: height_pixels,
                height: width_pixels
            });
            jQuery('#canvas').append(newCanvas);
        }

        if (selected == 'Vertical') {
            //Crear canvas con el tamaño del lienzo al seleccionarlo
            jQuery('#canvas').html('');
            var newCanvas = jQuery('<canvas/>', {
                'class': 'canvasProducto',
                id: 'myCanvas'
            }).prop({
                width: width_pixels,
                height: height_pixels
            });
            jQuery('#canvas').append(newCanvas);
        }
    }
}

function guardarNuevaImagen() {
    console.log("left", imageX, "top", imageY);
    d = document.createElement("script");
    d.src = myScript.pluginsUrl + "guardarImagenData.php?width=" + imageWidth + "&height=" + imageHeight + "&left=" + imageX + "&top=" + imageY;
    d.type = "text/javascript";
    document.body.appendChild(d);
}

function borrarSeleccionado(param1) {
    /* d = document.createElement("script");
    d.src = myScript.pluginsUrl + "borrarSeleccion.php?compare=" + param1;
    d.type = "text/javascript";
    document.body.appendChild(d); */

    jQuery.ajax({
        method: 'POST',
        url: my_ajax_url,
        data: {
            action: 'my_action', // "wp_ajax_*" action hook
            compare: param1,
        },
    })
    .done( function( response ) {
        //alert( response );
        location.reload();
    })
    .fail( function() {
        alert( "error" );
    })
}