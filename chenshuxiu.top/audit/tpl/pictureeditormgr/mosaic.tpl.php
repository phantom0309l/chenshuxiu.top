<?php
$pagetitle = "图片马赛克";
$cssFiles = [
    'https://apps.bdimg.com/libs/jqueryui/1.8.7/themes/base/jquery-ui.css',
    $img_uri . '/v5/plugin/jrac/style.jrac.css',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.resizable.js',
    $img_uri . '/v5/plugin/jrac/jquery.jrac.js?v=2018051916',
]; //填写完整地址
$pageStyle = <<<STYLE
.ml20{ margin-left: 20px;}
#canvasb {
    max-width:100%;
    max-height:100%;
    width:auto;
    height:auto;
}
#containerb {
    margin-top:0.5em;
    border:2px dashed #999;
}
.coords tr td:first-child {
    padding-right:5px;
}

.coords input {
    color:#fff;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="block push-10-t">
            <div class="col-md-6 pane" id="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group" >
                            <input class="form-control" type="number" id="inp-mosaic-size"  placeholder="马赛克大小">
                            <span class="input-group-btn">
                                <button class="btn btn-default" id="set-mosaic-size" type="button" value="">设置</button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                    <button class="btn btn-info si si-grid" id="mosaic"> 马赛克</button>
                    <button class="btn btn-danger si si-action-undo push-10-l" id="undo"> 撤销</button>
                    </div>
                    <div class="col-md-3">
                    </div>
                </div>
                <img crossOrigin = "Anonymous" id="image" src="<?= Picture::getSrcImg($picture->picname, $picture->picext, 1500, 1500)?>">
                <button class="btn btn-danger" id="btn-reset-image">原始大小</button>
                <table class="coords push-10-t">
                    <tr><td>裁剪框坐标X</td><td><input class="form-control" type="text" /></td></tr>
                    <tr><td>裁剪框坐标Y</td><td><input class="form-control" type="text" /></td></tr>
                    <tr><td>裁剪框宽</td><td><input class="form-control" type="text" /></td></tr>
                    <tr><td>裁剪框高</td><td><input class="form-control" type="text" /></td></tr>
                    <tr><td>图片宽度</td><td><input class="form-control" type="text" /></td></tr>
                    <tr><td>图片高度</td><td><input class="form-control" type="text" /></td></tr>
                    <tr>
                        <td>锁定</td>
                        <td>
                        <label class="css-input css-checkbox css-checkbox-success">
                            <input type="checkbox" checked="checked"><span></span>
                        </label>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <button class="btn btn-success fa fa-save" id="save"> 保存</button>
                <div id="containerb">
                <canvas id="canvasb" width="<?=$picture->width?>" height="<?=$picture->height?>"><p>抱歉，您的浏览器不支持canvas，请使用chrome浏览器</p></canvas>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
var selection = {};
var originWidth = $('#image').width();
var originHeight = $('#image').height();
var imageDataStack = [];
var cropItemStack = [];
var mosaicSize = 30;
$(function () {
    $('#canvasb').width(originWidth);
    $('#canvasb').height(originHeight);
    initJrac();
    bindSave();
    bindMosaic();
    bindUndo();
    bindResizeImage();
    bindSetMosaicSize();
});

function initJrac() {
    $('.pane img').jrac({
      'crop_width': 100,
      'crop_height': 50,
      'crop_x': 100,
      'crop_y': 100,
      'image_width': originWidth,
      'viewport_onload': function() {
        var $viewport = this;
        var inputs = $viewport.$container.parent('.pane').find('.coords input:text');
        var events = ['jrac_crop_x','jrac_crop_y','jrac_crop_width','jrac_crop_height','jrac_image_width','jrac_image_height'];
        for (var i = 0; i < events.length; i++) {
          var event_name = events[i];
          // Register an event with an element.
          $viewport.observator.register(event_name, inputs.eq(i));
          // Attach a handler to that event for the element.
          inputs.eq(i).bind(event_name, function(event, $viewport, value) {
              var eventName = event.type;
              if (eventName == 'jrac_crop_x') {
                  selection.x1 = value;
              } else if (eventName == "jrac_crop_y") {
                  selection.y1 = value;
              } else if (eventName == "jrac_crop_width") {
                  selection.width = value
              } else if (eventName == "jrac_crop_height") {
                  selection.height = value
              }
              selection.x2 = selection.x1 + selection.width;
              selection.y2 = selection.y1 + selection.height;
              $(this).val(value);
          })
          // Attach a handler for the built-in jQuery change event, handler
          // which read user input and apply it to relevent viewport object.
          .change(event_name, function(event) {
            var event_name = event.data;
            $viewport.$image.scale_proportion_locked = $viewport.$container.parent('.pane').find('.coords input:checkbox').is(':checked');
            $viewport.observator.set_property(event_name, $(this).val());
          });
        }
        $viewport.$container.append('<div>图片原始大小: '
          +$viewport.$image.originalWidth+' x '
          +$viewport.$image.originalHeight+'</div>')
      }
    })
    // React on all viewport events.
    .bind('jrac_events', function(event, $viewport) {
      var inputs = $(this).parents('.pane').find('.coords input');
      inputs.css('background-color',($viewport.observator.crop_consistent())?'#46c37b':'#d26a5c');
    });
}

function mosaicEffect(selection) {
    console.log(selection);
    var img = document.getElementById('image');
    var ratio = originWidth/img.width;
    selection.x1 = Math.floor(selection.x1 *ratio);
    selection.y1 = Math.floor(selection.y1 *ratio);
    selection.x2 = Math.floor(selection.x2 *ratio);
    selection.y2 = Math.floor(selection.y2 *ratio);
    console.log(selection);
    //img.crossOrigin = "Anonymous";
    var canvasb = document.getElementById("canvasb");
    var ctxb = canvasb.getContext("2d");
    console.log("is blank canvas", isCanvasBlank(canvasb));
    if (isCanvasBlank(canvasb)) {
        ctxb.drawImage(img, 0, 0, originWidth, originHeight);
    }

    var tmpImageData = ctxb.getImageData(0, 0, canvasb.width, canvasb.height);
    var tmpPixelData = tmpImageData.data;
    imageDataStack.push(tmpImageData);

    var imageData = copyImageData(tmpImageData);
    var pixelData = imageData.data;
    //定义为一块的边长是多少(这个图像宽高的整数倍)
    var size = mosaicSize;
    console.log("...size", size);
    var totalnum = size * size;
    for (var i = selection.y1; i < selection.y2; i += size) {
        for (var j = selection.x1; j < selection.x2; j += size) {
            //这块是计算每一块全部的像素值--平均值
            var totalr = 0, totalg = 0, totalb = 0;
            for (var dx = 0; dx < size; dx++)
                for (var dy = 0; dy < size; dy++) {

                    var x = i + dx;
                    var y = j + dy;

                    var p = x * canvasb.width + y;
                    totalr += tmpPixelData[p * 4 + 0];
                    totalg += tmpPixelData[p * 4 + 1];
                    totalb += tmpPixelData[p * 4 + 2];
                }

            var p = i * canvasb.width + j;
            var resr = totalr / totalnum;
            var resg = totalg / totalnum;
            var resb = totalb / totalnum;

            //这个快像素的值=它的平均值
            for (var dx = 0; dx < size; dx++) {
                for (var dy = 0; dy < size; dy++) {
                    var x = i + dx;
                    var y = j + dy;

                    var p = x * canvasb.width + y;
                    pixelData[p * 4 + 0] = resr;
                    pixelData[p * 4 + 1] = resg;
                    pixelData[p * 4 + 2] = resb;
                }
            }
        }
    }
    ctxb.putImageData(imageData, 0, 0, 0, 0, canvasb.width, canvasb.height);
}

function isCanvasBlank(canvas) {
    var blank = document.createElement('canvas');
    blank.width = canvas.width;
    blank.height = canvas.height;

    return canvas.toDataURL() == blank.toDataURL();
}

function copyImageData(imagedata){
     return new ImageData(new Uint8ClampedArray(imagedata.data),imagedata.width,imagedata.height);
}

function bindUndo() {
    $('#undo').on('click', function() {
        var canvasb = document.getElementById("canvasb");
        var ctxb = canvasb.getContext("2d");
        var imageData = imageDataStack.pop();
        if (imageData) {
            ctxb.putImageData(imageData, 0, 0, 0, 0, canvasb.width, canvasb.height);
        }
        cropItem = cropItemStack.pop();
        if (cropItem) {
            cropItem.remove();
        }
    })
}

function bindMosaic() {
    $('#mosaic').on('click', function(){
        mosaicEffect(selection);
        $('.jrac_crop ').css({'z-index':2});
        var cropItem = $('.jrac_crop.ui-resizable').clone().removeClass('ui-resizable');
        cropItemStack.push(cropItem);
        cropItem.css({
            'position':'absolute',
            'border': '1px solid #333',
            'background': '#fff',
            'opacity': '0.5',
            'z-index': 1
        }).appendTo($('.jrac_viewport '))
    });
}

function bindSave() {
    $('#save').on('click', function (e) {
        var canvasb = document.getElementById("canvasb")
        console.log(canvasb);
        canvasb.toBlob(function(blob){
            var formData = new FormData();
            formData.append('imgurl', blob);
            $.ajax({
                "type": "post",
                "url": "/picture/uploadimagepost",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                "success": function (res) {
                    console.log(res);
                    alert('保存成功');
                },
                "error": function () {
                    alert('保存失败');
                }
            });
        })
    });
}

function bindResizeImage() {
    $("#btn-reset-image").on('click', function() {
        $('.pane').find('.coords input:text').eq(4).val(originWidth);
        $('.pane').find('.coords input:text').eq(5).val(originHeight).trigger('change');
    });
}

function bindSetMosaicSize() {
    $('#set-mosaic-size').on('click', function() {
        var v = $('#inp-mosaic-size').val();
        if (v < 1 || v > 100) {
            alert("必须在1-100范围内");
        }
        mosaicSize = v - '';
    });
}
</script>
<?php
$footerScript = <<<SCRIPT
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
