<?php
$pagetitle = '运营系统首页';
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });

        $('#patient-listcond-word').autoComplete({
            type: 'patient',
            partner: '#patientid',
            change: function (event, ui) {
            },
            select: function (event, ui) {
                // $('#patientid').val(ui.item.id);
            },
            close: function (event, ui) {
            }
        });
    })
</script>

<div class="col-md-12" id="top">
    <section class="col-md-12 content-left">
                <div class=searchBar>
                    <form action="/patientpicturemgr/list" method="get" class="pr">
                        <div class="">
                            <label>按上传图片时间: </label>
                            从
                            <input type="text" class="calendar fromdate" style="width: 100px" name="fromdate" value="<?= $fromdate ?>" />
                            到
                            <input type="text" class="calendar todate" style="width: 100px" name="todate" value="<?= $todate ?>" />
                        </div>
                        <div class="">
                            <?php echo HtmlCtr::getRadioCtrImp(array(0 => "未归档",1 => "已归档",2 => "无意义",),'ppstatus',$ppstatus,''); ?>
                        </div>
                        <div class="mt10">
                            <label for="">疾病组：</label>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(), 'diseasegroupid', $diseasegroupid, 'f18'); ?>
                        </div>
                        <div class="mt10">
                            <label for="">标记类型：</label>
                            <?php echo HtmlCtr::getSelectCtrImp(PatientPicture::getTypes(), 'type', $type, 'f18'); ?>
                        </div>
                        <div class="mt10">
                            <label class="col-md-1" for="" style="margin-top: 8px;padding-right: 0px;width: 65px;">医生：</label>
                            <div class="col-md-3">
                                <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                            </div>
                        </div>
                        <div class="mt10">
                            <label class="col-md-1" for="" style="margin-top: 8px;padding-right: 0px;width: 65px;">患者：</label>
                            <div class="col-md-3">
                                <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                            </div>
                        </div>
                        <div class="mt10">
                            <input type="submit" class="btn btn-success" value="组合刷选" />
                        </div>
                    </form>
                </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                    <thead>
                    <tr>
                        <th>患者id</th>
                        <th>患者姓名</th>
                        <th>所属医生</th>
                        <th>上传时间</th>
                        <th>归档状态</th>
                        <th>类型</th>
                        <th>缩略图</th>
                        <th>图片标题</th>
                        <th>信息概要</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($patientpictures as $a ){
                        ?>
                        <tr>
                            <td><?= $a->patientid ?></td>
                            <td><?= $a->patient->name ?></td>
                            <td><?= $a->patient->doctor->name ?></td>
                            <td><?= $a->getCreateDay() ?></td>
                            <td><?= $a->getStatusDesc() ?></td>
                            <td id="<?=$a->id?>">
                                <?php echo HtmlCtr::getSelectCtrImp(PatientPicture::getTypes(false), 'pp_type', $a->type, 'f18 select_type'); ?>
                            </td>
                            <td>
                                <div class="pipeShell">
                                    <?php if( $a->obj->picture instanceof Picture ){?>
                                        <div class="col-md-6">
                                            <div class="overflow:hidden" style="max-width:200px;">
                                                <img class="img-responsive viewer-toggle"  data-url="<?= $a->obj->picture->getSrc(); ?>" src="<?=$a->obj->picture->getSrc(150, 150, false)?>" alt="">
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                            </td>
                            <td><?= $a->title ?></td>
                            <td><?= nl2br($a->getContent_brief()) ?></td>
                            <td>
                                <a target="_blank" href="/patientpicturemgr/one?patientpictureid=<?= $a->id ?>">修改</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function(){
        $('.pipeShell').viewer({
            inline: false,
            url: 'data-url',
            class: 'viewer-toggle',
            navbar: false,
            scalable: false,
            fullscreen: false,
            shown: function (e) {
            }
        })
        $('.pipeShell').viewer('update');

        $(".select_type").on('change', function () {
            console.log('=========');

            var type = $(this).val();
            console.log(type);

            var patientpictureid = $(this).parent('td').attr('id');
            console.log(patientpictureid);

            $.ajax({
                "type" : "get",
                "data" : {
                    patientpictureid : patientpictureid,
                    type : type
                },
                "dataType" : "text",
                "url" : "/patientpicturemgr/changetypejson",
                "success" : function (data) {
                    if (data == 'success') {
                        alert("标记成功!");
                    } else {
                        alert("标记失败!");
                    }
                }
            });
        });
    });
</script>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
