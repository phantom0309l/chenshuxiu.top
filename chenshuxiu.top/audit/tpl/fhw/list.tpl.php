<?php
$pagetitle = "上传图片";
$cssFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170820',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170820',
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="margin: 10px auto 20px auto;">
            <span>上传单张图片</span>
            <form>
                <div>
                    <div id="showimg" class="monitor_picbox">
                    </div>
                    <div style="clear: both;"></div>
                    <div>
                        <input class="file-input" onchange="uploadimg(this)" type="file" id="input-uploadimg" name="imgurl"/>
                    </div>
                </div>
                <div style="clear: both"></div>
                <p id="urlBox"></p>
            </form>
        </div>
    </section>
</div>
<div class="clear"></div>

<script text="javascript" src="<?= $img_uri ?>/jquery/jquery-file-upload.js"></script>
<script text="javascript">
    function uploadimg(obj) {
        if (obj.value.length > 0) {
            $.ajaxFileUpload({
                url: '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                secureuri: false,
                fileElementId: 'input-uploadimg', //文件选择框的id属性
                dataType: 'json', //服务器返回的格式，可以是json
                success: function (data, status) {            //相当于java中try语句块的用法
                    console.log(data);
                    var reg = /\d+_\d+\./;
                    var image_url = data.thumb.replace(reg, "");
                    var newimgDiv = "<div class=\"img-container push-10-r push-10 fl\" style=\"border:#ccc 1px solid;width:154px;height:158px;float:left;margin:0 2px 5px 0;padding:1px;text-align:center;line-height:150px;\">\n" +
                        "                <input type=\"hidden\" class=\"pictureid\" name=\"pictureid[]\" value=\"" + data.pictureid + "\">\n" +
                        "                <img class=\"img-responsive viewer-toggle\" data-url=\"" + image_url + "\" src=\"" + data.thumb + "\" alt=\"\">\n" +
                        "                </div>\n" +
                        "            </div>";
                    $("#showimg").append(newimgDiv);
                },
                error: function (data, status, e) {            //相当于java中catch语句块的用法
                    $('#upload_status').html('上传失败');
                    alert(data);
                    alert(e);
                }
            });
        }
    }
</script>

<?php
$footerScript = <<<STYLE
    $(function(){
        $(document).off("click", ".delete-pic").on("click", ".delete-pic", function(){
            var imgDiv = $(this).parents('.img-container');
            if (!confirm("确定删除吗?")) {
                return false;
            }
            imgDiv.remove();
        });

        $('.monitor_picbox').viewer({
            inline: false,
            url: 'data-url',
            class: 'viewer-toggle',
            navbar: false,
            scalable: false,
            fullscreen: false,
            shown: function (e) {
            }
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
