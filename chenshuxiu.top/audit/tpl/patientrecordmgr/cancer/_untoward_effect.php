<?php
$relate_chemo = PatientRecordCancer::getChemoOptionByPatientid($patient->id);
?>
<div class="J_untoward_effect">
    <form class="patientrecord-panel form form-horizontal">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>">
        <input type="hidden" name="code" value="cancer">
        <input type="hidden" name="type" value="untoward_effect">
        <div class="form-group">
            <label class="control-label col-md-2">跟进/新建</label>
            <div class="col-md-8">
                <?php
                $patientrecords = PatientRecordDao::getParentListByPatientidCodeType($patient->id, 'cancer', 'untoward_effect');
                ?>
                <select autocomplete="off" name="parent_patientrecordid"
                        class="js-select2 form-control J_parent_patientrecordid" style="width: 100%">
                    <option value="0">新建</option>
                    <?php foreach ($patientrecords as $patientrecord) {
                        $data = json_decode($patientrecord->json_content, true);
                        ?>
                        <option value="<?= $patientrecord->id ?>" data-relatechemo="<?= $data['relate_chemo'] ?>"
                                data-untowardeffectname="<?= $data['name'] ?>">
                            <?= $patientrecord->thedate ?>&nbsp;
                            <?= $data['name'] ?>&nbsp;
                            <?= $data['degree'] ?>&nbsp;
                            <?= $relate_chemo[$data['relate_chemo']] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">日期</label>
            <div class="col-md-8">
                <input type="text" class="calendar form-control" name="thedate" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">名称</label>
            <div class="col-md-8">
                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('untoward_effect_name'), "untoward_effect[name]", 'WBC', 'js-select2 form-control J_untoward_effect_name', 'width: 100%'); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">程度</label>
            <div class="col-md-8">
                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('untoward_effect_degree'), "untoward_effect[degree]", '-1', 'js-select2 form-control J_untoward_effect_degree', 'width: 100%'); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">关联化疗</label>
            <div class="col-md-8">
                <?php echo HtmlCtr::getSelectCtrImp($relate_chemo, "untoward_effect[relate_chemo]", '0', 'js-select2 form-control J_untoward_effect_relate_chemo', 'width: 100%'); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">备注</label>
            <div class="col-md-8">
                <textarea class="form-control" name="content" rows="4" cols="40"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <a href="javascript:" class="patientrecord-addBtn btn btn-success btn-sm btn-minw">提交</a>
            </div>
            <div class="col-md-4 col-md-offset-4 text-right">
                <a style="line-height:2.2" target='_blank'
                   href="/patientrecordmgr/list?patientid=<?= $patient->id ?>&code=cancer&type=untoward_effect"><i
                            class="fa fa-history">

                    </i> 历史记录</a>
            </div>
        </div>
    </form>
</div>
<script>
    $(function () {
        var el_box = $('.J_untoward_effect');

        el_box.find('.js-select2').select2();

        el_box.find('.J_parent_patientrecordid').on('select2:select', function(e) {
            var el_name = el_box.find('.J_untoward_effect_name');
            var el_relate_chemo = el_box.find('.J_untoward_effect_relate_chemo');

            var me = $(this);
            if (me.val() === "0") {
                el_name.attr("readonly", false);
                el_relate_chemo.attr("readonly", false);
                return false;
            }

            var option = e.params.data.element;
            var name = option.dataset.untowardeffectname;
            var relate_chemo = option.dataset.relatechemo;

            el_name.val(name).trigger("change");
            el_name.attr("readonly", true);

            el_relate_chemo.val(relate_chemo).trigger("change");
            el_relate_chemo.attr("readonly", true);
        });
    })
</script>