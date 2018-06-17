<?php
$pagetitle = "员工修改";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageScript = <<<SCRIPT
$(function(){
    $('.js-select2').select2();
})
SCRIPT;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<style>
    .img {
        width: 150px;
        border: 1px solid #EEE5DE;
        margin-bottom: 10px
    }
</style>

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
                    var newimgDiv = "<div class=\"img-container fx-opt-zoom-out imgDiv img\">\n" +
                        "                <input type=\"hidden\" class=\"pictureid\" name=\"pictureid\" value=\"" + data.pictureid + "\">\n" +
                        "                <img class=\"img-responsive\" src=\"" + data.thumb + "\" alt=\"\">\n" +
                        "            </div>";
                    $("#auditor_img").html(newimgDiv);
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

<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <form action="/my/modifypicturepost" method="post">
                <input type="hidden" name="auditorid" value="<?= $auditor->id ?>" />
                <table class="table table-bordered">
                    <tr>
                        <th width=90>userid</th>
                        <td><?= $myuser->id ?></td>
                    </tr>
                    <tr>
                        <th>auditorid</th>
                        <td><?= $myuser->getAuditor()->id ?></td>
                    </tr>
                    <tr>
                        <th width=90>头像</th>
                        <td>
                            <div>
                                <div id="auditor_img">
                                    <?php
                                    if ($auditor->picture instanceof Picture) {
                                        ?>
                                        <div class="img-container fx-opt-zoom-out imgDiv img">
                                            <input type="hidden" class="pictureid" name="pictureid" value="<?=$auditor->pictureid?>">
                                            <img class="img-responsive" src="<?= $auditor->picture->getSrc(150, 150) ?>" alt="">
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div style="clear: both;"></div>
                                <div>
                                    <?php if ($myauditor->id == 10007 || $auditor->id == $myauditor->id) { ?>
                                        <input class="file-input" onchange="uploadimg(this)" data-pic_type="bedtktpictureids" type="file" id="input-uploadimg" name="imgurl"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $myuser->createtime ?></td>
                    </tr>
                    <tr>
                        <th>username</th>
                        <td><?= $myuser->username ?></td>
                    </tr>
                    <tr>
                        <th>mobile</th>
                        <td><?= $myuser->mobile ?></td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td><?= $myuser->name ?></td>
                    </tr>
                    <tr>
                        <th>二维码</th>
                        <td>
                            <a href="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=<?= $qr_ticket ?>"
                               target="_blank">
                                <img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=<?= $qr_ticket ?>"/>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </section>
</div>
<div class="clear"></div>

<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
</body>
</html>
