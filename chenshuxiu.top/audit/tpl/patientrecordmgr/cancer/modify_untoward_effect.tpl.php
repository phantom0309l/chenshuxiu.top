<?php
$pagetitle = "备注修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/patientrecordmgr/modifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <input type="hidden" name="patientrecordid" value="<?= $patientrecord->id ?>"/>
                    <?php
                    $type = $patientrecord->type;
                    ?>
                    <tr>
                        <th width=140>日期</th>
                        <td>
                            <div class="col-md-4">
                                <input type="text" class="calendar form-control" name="thedate"
                                       value="<?= $patientrecord->thedate ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>名称</th>
                        <td>
                            <div class="col-md-4">
                                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('untoward_effect_name'), "{$type}[name]", $patientrecord_data["name"], 'js-select2 form-control', 'width: 100%'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>程度</th>
                        <td>
                            <div class="col-md-4">
                                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('untoward_effect_degree'), "{$type}[degree]", $patientrecord_data["degree"], 'js-select2 form-control', 'width: 100%'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>关联化疗</th>
                        <td>
                            <div class="col-md-4">
                                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getChemoOptionByPatientid($patientrecord->patientid), "{$type}[relate_chemo]", $patientrecord_data["relate_chemo"], 'js-select2 form-control', 'width: 100%'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <div class="col-md-4">
                            <textarea class="form-control" name="content" rows="4"
                                      cols="40"><?= $patientrecord->content ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <div class="col-md-4">
                                <input class="btn btn-success J_submit" type="submit" value="提交"/>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<script>
    $(function () {
        App.initHelper('select2');
    });
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
