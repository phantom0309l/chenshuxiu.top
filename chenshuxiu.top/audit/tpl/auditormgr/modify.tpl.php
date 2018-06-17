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
            <?php
            $auditroles = AuditRole::getDescArr();
            ?>
            <form action="/auditormgr/modifypost" method="post">
                <input type="hidden" name="auditorid" value="<?= $auditor->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=90>员工id</th>
                        <td><?= $auditor->id ?></td>
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
                        <th>类型</th>
                        <td>
                            <div class="col-md-6">
                                <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getAuditorTypeCtrArray(false),'type', $auditor->type, 'css-radio-success')?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>
                            <div class="col-md-2">
                                <input class="form-control" type="text" name="name" value="<?= $auditor->name?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>角色</th>
                        <td>

                        <?php
                        foreach ( $auditroles as $k => $v ) {
                            $chk = '';
                            if (in_array( $k, $auditor->getAuditRoleIdArr() ))
                                $chk = 'checked';
                            echo "<input type='checkbox' name='auditroleids[]' id='auditrole$k' value='$k' $chk /><label for='auditrole$k'>$v</label> ";
                        }
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <th>状态</th>
                        <td>
                            <div class="col-md-6">
                                <?php
                                    $arr = [
                                        '1' => '在职',
                                        '0' => '离职'
                                    ];
                                    echo HtmlCtr::getRadioCtrImp4OneUi($arr,'status', $auditor->status, 'css-radio-success')?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>疾病组</th>
                        <td>
                            <div class="col-md-2">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(),'diseasegroupid',$auditor->diseasegroupid,'js-select2 form-control') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>管辖省</th>
                        <td>
                            <div class="col-md-2">
                                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllXprovinceCtrArray(),"xprovinceid_control",$auditor->xprovinceid_control,'js-select2 form-control');?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>推荐人</th>
                        <td>
                            <div class="col-md-2">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),'auditorid_prev',$auditor->auditorid_prev,'js-select2 form-control') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>基础员工组</th>
                        <td>
                            <div class="col-md-3">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorGroupCtrArray(true, 'base'),'auditorgroupid', $auditorGroupId,'js-select2 form-control') ?>
                            </div>离职员工配置此项无效
                        </td>
                    </tr>
                    <tr>
                        <th>达标日期</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="standard_date" value="<?= $auditor->standard_date ?>" placeholder="达标日期" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>IP电话座席号</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="cdr_no1" value="<?= $auditor->cdr_no1 ?>" placeholder="IP电话座席号" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>个人电话座席号</th>
                        <td>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="cdr_no2" value="<?= $auditor->cdr_no2 ?>" placeholder="个人电话座席号" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <div class="col-md-4">
                                <textarea class="form-control" rows="4" name="remark"><?php echo $auditor->remark; ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
