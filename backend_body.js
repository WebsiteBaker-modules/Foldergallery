//domReady.ready(function() {
    function addEvent(elem, event, fn) {
        if(!elem) { return false; }
console.info (elem);
        if (elem.addEventListener) {
            elem.addEventListener(event, fn, false);
        } else {
            elem.attachEvent("on" + event, function() {
                // set the this pointer same as addEventListener when fn is called
                return(fn.call(elem, window.event));
            });
        }
    }

$(document).ready(function() {
    console.info('FOLDERGALLERY ==='+typeof FOLDERGALLERY);

    if (typeof FOLDERGALLERY === 'object') {

        $(function() {
            $("#dragableCategorie ul").sortable({
                opacity: 0.6,
                cursor: 'move',
                update: function() {
                    var order = $(this).sortable("serialize") + '&action=updateRecordsListings&parent_id='+the_parent_id;
                    $.post(FOLDERGALLERY.AddonUrl+"admin/scripts/reorderCNC.php", order, function(theResponse){
                        $("#dragableResult").html(theResponse);
                    });
                }
            });
        });
/*---------------------------------------------------------------------------------------------------*/
        //    var ThemeCss = FOLDERGALLERY.ThemeUrl+'/default.css';
        //    var TemplateCss = FOLDERGALLERY.TemplateUrl+'/default.css';
        var JcropCss = FOLDERGALLERY.AddonThemeUrl+'js/jcrob/css/jquery.Jcrop.css';
        if (typeof LazyOnLoad==='undefined'){
            $.insert(JcropCss);
        } else {
            LazyOnLoad ('', JcropCss);
        }
        $.insert(WB_URL+'/include/jquery/jquery-ui-min.js');
        $.insert(FOLDERGALLERY.AddonThemeUrl+'js/jcrob/js/jquery.Jcrop.min.js');
        $(function() {
            $("#dragableTable ul").sortable({
                opacity: 0.6,
                cursor: 'move',
                update: function() {
                    var order = $(this).sortable("serialize") + '&action=updateRecordsListings&parent_id='+the_parent_id;
                    $.post(FOLDERGALLERY.AddonUrl+"admin/scripts/reorderDND.php", order, function(theResponse){
                        $("#dragableResult").html(theResponse);
                    });
                }
            });
        });
    //});
    }

    if (document.querySelectorAll(".remodal").length > 0) {
        var remodalCss = FOLDERGALLERY.AddonThemeUrl+'css/sweetalert/sweetalert.css';
//        var remodalThemeCss = FOLDERGALLERY.AddonThemeUrl+'css/remodal/remodal-default-theme.css';
        if (typeof LoadOnFly==='undefined'){
            $.insert(remodalCss);
//            $.insert(remodalThemeCss);
        } else {
            LoadOnFly('head', remodalCss);
//            LoadOnFly('head', remodalThemeCss);
        }
        $.insert(FOLDERGALLERY.AddonThemeUrl+'js/sweetalert/sweetalert.min.js');
    }// end of remodal

    // Intialize jCrop only if needed (means settingsRatio is defined
    if(!(typeof(settingsRatio) == 'undefined')) {
        // Remember to invoke within jQuery(window).load(...)
        // If you don't, Jcrop may not initialize properly
        $(window).load(function(){
            $('#cropbox').Jcrop({
                onChange: showPreview,
                onSelect: updateCoords,
                aspectRatio: settingsRatio
            });
        });

        function showPreview(coords)
        {
            var imgWidth = $("#cropbox").width();
            var scale = relWidth / imgWidth;
            if  (settingsRatio > 1) {
                var rx = thumbSize / coords.w;
                var ry = thumbSize / settingsRatio / coords.h;
            } else {
                var rx = thumbSize * settingsRatio / coords.w;
                var ry = thumbSize / coords.h;
            }
            $('#preview').css({
                width: Math.round(rx * relWidth / scale) + 'px',
                height: Math.round(ry * relHeight / scale) + 'px',
                marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                marginTop: '-' + Math.round(ry * coords.y) + 'px'
            });
        };

        function updateCoords(c)
        {
            var imgWidth = $("#cropbox").width();
            var scale = relWidth / imgWidth;
            $('#x1').val(c.x * scale);
            $('#y1').val(c.y * scale);
            $('#x2').val(c.x2 * scale);
            $('#y2').val(c.y2 * scale);
        };

        function checkCoords()
        {
            if (parseInt($('#y2').val())) return true;
            alert('Please select a crop region then press submit.');
            return false;
        };

    }
    // End of jCrop

/*---------------------------------------------------------------------------------------------------*/
(function ($) {
    var originalVal = $.fn.val;
    $.fn.val = function (value) {
        var res = originalVal.apply(this, arguments);

        if (this.is('input:text') && arguments.length >= 1) {
            // this is input type=text setter
            this.trigger("input");
        }

        return res;
    };
})(jQuery);




    $("#loadPreset").change(function () {
        var value = $(this).val(),
            thumb_x = $("#thumb_x").val(),
            thumb_y = $("#thumb_y").val(),
            ThumbPreset = FOLDERGALLERY.AddonUrl
                        +'admin/scripts/getThumbPreset.php?preset='+value
                        +'&thumb_x='+thumb_x
                        +'&thumb_y='+thumb_y;
console.info(thumb_x);
console.info(thumb_y);
        $.getJSON(ThumbPreset, {
            format: "json"
        })
          .done(function(data) {
                var preset = data.preset,
                    ratio  = preset.thumb_ratio;
console.log(preset);
//console.log(ratio);
                setRatio(preset);
                if(typeof(preset.image_background_color) == 'undefined') {
                    $('#background_color').attr("value", "#FFFFFF");
                } else {
                    $('#background_color').attr("value", data.image_background_color);
                }
            })
        });
});

/*---------------------------------------------------------------------------------------------------*/
    function setRatio(preset) {
        var iRatio = preset.thumb_ratio,
            ratio  = iRatio;
//    console.log('ratio = '+ratio);
        switch (ratio) {
            case 1:
                $("input[name='thumb_ratio'][value='1']").attr('checked', true);
                break;
            case 1.34:
                $("input[name='thumb_ratio'][value='1.34']").attr('checked', true);
                break;
            case 0.75:
                $("input[name='thumb_ratio'][value='0.75']").attr('checked', true);
                break;
            case 1.79:
                $("input[name='thumb_ratio'][value='1.79']").attr('checked', true);
                break;
            case 0.56:
                $("input[name='thumb_ratio'][value='0.56']").attr('checked', true);
                break;
            default:
               $("input[name='thumb_ratio'][value='free']").attr('checked', true);
               var iRatio = 1;
        }
        $("#thumb_x").attr("value", preset.image_x);
        $("#thumb_y").attr("value", preset.image_y);

        if (ratio || preset.image_ratio){
            if((ratio ==='free') || (preset.image_ratio === 'false')) {
                $('#thumb_keep').attr('checked', true);
            } else {
                $('#thumb_cut').attr('checked', true);
            }
        }
   }

/*---------------------------------------------------------------------------------------------------*/
    function getRatio(el){
      var ratio = $(el).val();
      return ((ratio === 'undefined' || ratio === 'free')?null:ratio);
    }

/*---------------------------------------------------------------------------------------------------*/
    if( document.querySelectorAll("#thumb_x").length > 0 ) {

        var $thumb_x = $("#thumb_x");
    console.log($thumb_x);
        $thumb_x.on('input', function() {
          // Do this when value changes
        //console.log($(this).val());
            var ratio = getRatio("input[name='thumb_ratio']:checked"),
                value = $(this).val();
    console.log('ratio = '+ratio+' value ='+value);
            if (!ratio){
             // Do nothing
            } else {
                $("#thumb_y").attr("value", Math.round(value*ratio));
            }
        });

/*---------------------------------------------------------------------------------------------------*/
        var $thumb_y = $("#thumb_y");
        console.log($thumb_y);
        $thumb_y.on('input', function() {
          // Do this when value changes
        //console.log($(this).val());
            var value = $(this).val(),
                ratio = getRatio("input[name='thumb_ratio']:checked");
            if (!ratio){
             // Do nothing
            } else {
                var xValue = Math.round(value/ratio);
    console.log('ratio = '+ratio+' value ='+value+ ' Ergebnis = '+xValue);
                $("#thumb_x").attr("value", xValue);
            }
        });
    }
/*---------------------------------------------------------------------------------------------------*/

//console.info('FOLDERGALLERY ==='+typeof FOLDERGALLERY);
    // Function to toggle active/inavtive of a categorie in the overview
    function toggle_active_inactive(id) {
        var img = $("#i" + id);
    console.log(img);
        if( img.attr("src") == FOLDERGALLERY.AddonThemeUrl+"img/active1.gif") {
            var action = "disable";
            var src = FOLDERGALLERY.AddonThemeUrl+"img/active0.gif";
        } else {
            var action = "enable";
            var src = FOLDERGALLERY.AddonThemeUrl+"img/active1.gif";
        }
        $.ajax({
            url: FOLDERGALLERY.AddonUrl+"admin/scripts/cat_switch_active_inactive.php",
            type: "POST",
            data: 'cat_id='+id+'&action='+action,
            dataType: 'json',
            success: function(data) {
                if(data.success == "true") {
                    img.attr("src", src);
                    img.attr("title", data.message);
                } else {
                    alert(data.message);
                }
            },
            complete: function() {}
        });
    }
    // End of toggle_active_inactive
/*-----------------------------------------------------------------------------------*/

    var pJpegQuality = document.getElementById( "defaultQuality" ),
        resJpegQuality = document.getElementById( "jpegQualityResult" );
        if (pJpegQuality){
        pJpegQuality.addEventListener("input", function() {
            resJpegQuality.innerHTML = "" + pJpegQuality.value+" %";
        }, false);
    }


    var pBgOpacity = document.getElementById( "opacity" ),
        resBgOpacity = document.getElementById( "OpacityResult" );
        if (pBgOpacity){
        pBgOpacity.addEventListener("input", function() {
            resBgOpacity.innerHTML = "" + pBgOpacity.value+" ";
        }, false);
    }

/*
*/

