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
jQuery(document).on('change', '#posters', function () {
    var value = jQuery(this).val();
    console.log(value);

    d = document.createElement("script");
    d.src = myScript.pluginsUrl + "minCost.php?info=" + value;
    d.type = "text/javascript";
    document.body.appendChild(d);
});

jQuery(document).on('change', '#lienzos', function () {
    var value = jQuery(this).val();
    console.log(value);

    d = document.createElement("script");
    d.src = myScript.pluginsUrl + "minCost.php?info=" + value;
    d.type = "text/javascript";
    document.body.appendChild(d);
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
            jQuery('#img-preview').attr("src", e.target.result);
            jQuery("#img-preview").css("display", "block");

            jQuery("#img-preview").draggable({
                containment: "#contenedor"
             });               
               jQuery("#img-preview").resizable({
               containment: "#contenedor"
             });
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function borrarPost(param1,param2){
    alert(param1);
    alert(param2);
    jQuery.ajax({
        type:"POST",
        url:"borrarProducto.php",
        data:{id1:param1, id2:param2},
        success:function(html){
            alert(html);
        }
    });
}
