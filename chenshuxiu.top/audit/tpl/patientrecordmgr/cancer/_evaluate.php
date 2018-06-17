<div class="">
    <form class="patientrecord-panel form form-horizontal">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>">
        <input type="hidden" name="code" value="cancer">
        <input type="hidden" name="type" value="evaluate">
        <div class="form-group">
            <label class="control-label col-md-2">日期</label>
            <div class="col-md-8">
            <input type="text" class="calendar form-control" name="thedate" value="<?= date('Y-m-d')?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">类别</label>
            <div class="col-md-8">
            <?php $evaluate_class = PatientRecordCancer::getOptionByCode('evaluate_class'); ?>
            <select class="patientrecord-evaluate-class form-control" name="evaluate[class]">
                <?php foreach ($evaluate_class as $k => $v) { ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                <?php }?>
            </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">评价</label>
            <div class="col-md-8">
                <select class="patientrecord-evaluate-assess form-control" name="evaluate[assess]"></select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">关联化疗</label>
            <div class="col-md-8">
            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getChemoOptionByPatientid($patient->id),"evaluate[relate_chemo]",'0','form-control'); ?>
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
            <a style="line-height:2.2" target='blank' href="/patientrecordmgr/list?patientid=<?= $patient->id ?>&code=cancer&type=evaluate"><i class="fa fa-history"></i> 历史记录</a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
$(function () {
    var init_assess = function(){
        var class_select = $(".patientrecord-evaluate-class");
        var assess_select = $(".patientrecord-evaluate-assess");
        assess_select.empty();
        var class_value = class_select.val();
        var assess = [];
        switch (class_value) {
            case '新辅助':
                assess = ['完全退缩','部分退缩','稳定','进展']
                break;

            case '辅助':
                assess = ['DFS','PD']
                break;

            case 'RECIST':
                assess = ['CR','PR','SD','PD']
                break;
            default:

        }

        for (var i = 0; i < assess.length; i++) {
            assess_select.append("<option value=\'" + assess[i] + "\'>" + assess[i] + "</option>")
        }
    }
    $(".patientrecord-evaluate-class").on("change", function(){
        init_assess();
    })

    init_assess();
});
</script>
