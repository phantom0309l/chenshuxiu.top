<?php
$pagetitle = "新增跟进记录";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
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
        <form class="J_form">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <?php
                    $type = $parent_patientrecord->type;
                    ?>
                    <input type="hidden" name="parent_patientrecordid" value="<?= $parent_patientrecord->id ?>"/>
                    <input type="hidden" name="patientid" value="<?= $parent_patientrecord->patientid ?>">
                    <input type="hidden" name="code" value="<?= $parent_patientrecord->code ?>">
                    <input type="hidden" name="type" value="<?= $type ?>">
                    <tr>
                        <th width=140>跟进</th>
                        <td>
                            <div class="col-md-4">
                                <?php
                                $relate_chemo = PatientRecordCancer::getChemoOptionByPatientid($parent_patientrecord->patientid);
                                ?>
                                <input class="form-control" type="text"
                                       value="<?= $parent_patientrecord->thedate . ' ' . $parent_patientrecord_data['name'] . ' ' . $parent_patientrecord_data['degree'] . ' ' . $relate_chemo[$parent_patientrecord_data['relate_chemo']] ?>"
                                       readonly>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>名称</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="<?= $type ?>[name]"
                                       value="<?= $parent_patientrecord_data["name"] ?>" readonly>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>日期</th>
                        <td>
                            <div class="col-md-4">
                                <input type="text" class="form-control calendar" name="thedate"
                                       value="<?= date('Y-m-d') ?>"
                                       placeholder="选择日期">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>程度</th>
                        <td>
                            <div class="col-md-4">
                                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('untoward_effect_degree'), "{$type}[degree]", 0, 'js-select2 form-control'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>关联化疗</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" type="hidden" name="<?= $type ?>[relate_chemo]"
                                       value="<?= $parent_patientrecord_data["relate_chemo"] ?>">
                                <input class="form-control" type="text"
                                       value="<?= $relate_chemo[$parent_patientrecord_data["relate_chemo"]] ?>" readonly>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <div class="col-md-4">
                                <textarea class="form-control" name="content" rows="4" cols="40"></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <div class="col-md-4">
                                <input class="btn btn-success J_submit" type="button" value="提交"/>
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

        $(function () {
            $(".J_submit").on("click", function () {
                var thedate = $('input[name="thedate"]').val();
                if (thedate === undefined || thedate === null || thedate === '') {
                    alert('日期不能为空');
                    return false;
                }

                var me = $(this);
                if (me.data('confirm') == true) {
                    if (!confirm('是否提交？')) {
                        return false;
                    }
                }
                var data = $('.J_form').serialize();
                $.ajax({
                    "type": "post",
                    "url": "/patientrecordmgr/addjson",
                    dataType: "json",
                    data: data,
                    "success": function (res) {
                        if (res.errno === '0') {
                            var patientid = $('input[name="patientid"]').val();
                            var code = $('input[name="code"]').val();
                            var type = $('input[name="type"]').val();
                            alert('保存成功');
                            window.location.href = '/patientrecordmgr/list?patientid=' + patientid + '&code=' + code + '&type=' + type;
                        } else {
                            alert(res.errmsg);
                        }
                    },
                    "error": function () {
                        alert('保存失败');
                    }
                });
            })
        });
    })
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
