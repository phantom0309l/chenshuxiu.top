<?php
$pagetitle = "图片涂鸦";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/lib/sketchpad.js?v=2018051814',
]; //填写完整地址
$pageStyle = <<<STYLE
.ml20{ margin-left: 20px;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <button class="btn btn-success" id="save">保存</button>
        <div class="block">
        <canvas class="sketchpad push-10-t" id="sketchpad" style="border:1px solid #ddd">
            <p>抱歉，您的浏览器不支持canvas，请使用chrome浏览器</p>
        </canvas>
        </div>
    </section>
</div>
<script>
    var sketchpad;
    $(function () {
        var imgurl = "<?= Picture::getSrcImg($picture->picname, $picture->picext, 1500, 1500)?>";
        sketchpad = new Sketchpad({
            element: '#sketchpad',
            width: <?=$picture->width?>,
            height: <?=$picture->height?>,
            penSize: 10,
            color: '#00ff00',
            imgurl: imgurl,
        });
        $('#color-picker').change(color);
        $('#color-picker').val('#000');
        $('#size-picker').change(size);
        $('#size-picker').val(1);
        bindSave();
    });

    function bindSave() {
        $('#save').on('click', function(e) {
            var formData = sketchpad.toFormData("imgurl");
            console.log(formData);
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
        });
    }

    function undo() {
        sketchpad.undo();
    }

    function redo() {
        sketchpad.redo();
    }

    function color(event) {
        sketchpad.color = $(event.target).val();
    }

    function size(event) {
        sketchpad.penSize = $(event.target).val();
    }

    function animateSketchpad() {
        sketchpad.animate(10);
    }

    function recover(event) {
        var settings = sketchpad.toObject();
        settings.element = '#other-sketchpad';
        var otherSketchpad = new Sketchpad(settings);
        $('#recover-button').hide();
    }
</script>
<?php
$footerScript = <<<SCRIPT
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
