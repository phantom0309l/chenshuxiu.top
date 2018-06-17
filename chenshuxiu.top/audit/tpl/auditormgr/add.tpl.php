<?php
$pagetitle = "员工新建";
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
            <form action="/auditormgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=90>类型</th>
                        <td>
                            <div class="col-md-6">
                                <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getAuditorTypeCtrArray(false),'type', $type, 'css-radio-success')?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=90>头像</th>
                        <td>
                            <div>
                                <div id="auditor_img">
                                    <div class="img-container fx-opt-zoom-out imgDiv img">
                                        <img class="img-responsive" src="" alt="">
                                    </div>
                                </div>
                                <div style="clear: both;"></div>
                                <div>
                                    <input class="file-input" onchange="uploadimg(this)" data-pic_type="bedtktpictureids" type="file" id="input-uploadimg" name="imgurl"/>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>用户名</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="username" value="" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;padding-left: 0px">
                                用户用于登录的名字
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="name" value="" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;padding-left: 0px">
                                用户的姓名
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>电话</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="mobile" value="" />
                            </div>
                            <div class="col-md-3 red" style="margin-top: 6px;padding-left: 0px">
                                * 用于联系与登录
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>密码</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="password" value="" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;padding-left: 0px">
                                为空则自动取手机号后6位
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>IP电话座席号</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="cdr_no1" value="<?= $myauditor->id ?>" placeholder="IP电话座席号" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>个人电话座席号</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="cdr_no2" value="<?= $myauditor->id - 9000 ?>" placeholder="个人电话座席号" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>疾病组</th>
                        <td>
                            <div class="col-md-2">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(),'diseasegroupid',$diseasegroupid,'js-select2 form-control') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>管辖省</th>
                        <td>
                            <div class="col-md-2">
                                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllXprovinceCtrArray(),"xprovinceid_control",$xprovinceid_control,'js-select2 form-control');?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>推荐人</th>
                        <td>
                            <div class="col-md-2">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),'auditorid_prev',$auditorid_prev,'js-select2 form-control') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>基础员工组</th>
                        <td>
                            <div class="col-md-3">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorGroupCtrArray(true, 'base'),'auditorgroupid', 0,'js-select2 form-control') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <div class="col-md-4">
                                <textarea class="form-control" name="auditremark" rows="4"></textarea>
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;padding-left: 0px">
                                可以记录成员其他信息
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <div class="col-md-2">
                                <input class="btn btn-success" type="submit" value="提交" />
                            </div>
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
