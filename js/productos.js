var canvas;
var ctx;
var canvasOffset;
var offsetX;
var offsetY;
var canvasWidth;
var canvasHeight;
var isDragging = false;
var imageCanvas;

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


jQuery("input[name='productos']").click(function(){
    var Seleccion= jQuery("input[name='productos']:checked").val();
    if (Seleccion == 'poster') {
        jQuery("#posters").show();
        jQuery("#lienzos").hide();
        jQuery("#minimo").val(" ");
        jQuery("#lienzos").attr(" ");
    }

    if (Seleccion == 'lienzo') {
        jQuery("#lienzos").show();
        jQuery("#posters").hide();
        jQuery("#minimo").val('');
        jQuery("#posters").attr(" ");
    }
});
jQuery(document).ready(function(){
jQuery("input[name='posters']:radio").change(function(){
    alert("Hola");
    var value = jQuery("input[name='posters']:checked").val();
    alert(value);    
});
});

jQuery("#file").change(function () {
    filePreview(this);
});
jQuery("#crear").click(function(){
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

            canvas = document.getElementById('myCanvas');
            const context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
            var img = new Image();
            img.onload = draw;
            img.onerror = failed;
            img.src = URL.createObjectURL(input.files[0]);
            ctx = canvas.getContext("2d");
            canvasOffset = jQuery("#myCanvas").offset();
            offsetX = canvasOffset.left;
            offsetY = canvasOffset.top;
            canvasWidth = canvas.width;
            canvasHeight = canvas.height;
            storeImageCanvas(img);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function storeImageCanvas(image) {
    imageCanvas = image;
}

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
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(imageCanvas, canMouseX - 128 / 2, canMouseY - 120 / 2, 128, 120);
    }
}

//Dibujar la imagen sobre el Canvas
function draw() {
    var canvas = document.getElementById('myCanvas');
    var ctx = canvas.getContext('2d');
    ctx.drawImage(this, 0, 0);
}

function failed() {
    console.error("The provided file couldn't be loaded as an Image media");
}

//Enviar los id a PHP 
function borrarPost(param1, param2) {
    //alert(param1);
    //alert(param2);
    jQuery.ajax({
        type: "POST",
        url: myScript.pluginsUrl + "borrarProducto.php",
        data: { woocommerce: param1, printful: param2 },
        success: function (html) {
            alert(html);
        }
    });
}

 jQuery(".posters").click(function(){
     var x =  jQuery(this).attr('value');
     console.log(x);

     d = document.createElement("script");
    d.src = myScript.pluginsUrl + "minCost.php?info=" + x;
    d.type = "text/javascript";
    document.body.appendChild(d);
    jQuery("#posterSelected").val(x);

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
 jQuery(".lienzos").click(function(){
    var x =  jQuery(this).attr('value');
    console.log(x);

    d = document.createElement("script");
   d.src = myScript.pluginsUrl + "minCost.php?info=" + x;
   d.type = "text/javascript";
   document.body.appendChild(d);
   jQuery("#lienzoSelected").val(x);

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


