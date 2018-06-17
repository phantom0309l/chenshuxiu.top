<div class="">
    <form class="patientrecord-panel form form-horizontal">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>">
        <input type="hidden" name="code" value="cancer">
        <input type="hidden" name="type" value="chemo">
        <div class="form-group">
            <label class="control-label col-md-2">开始日期</label>
            <div class="col-md-8">
            <input type="text" class="calendar form-control" name="thedate" value="<?= date('Y-m-d')?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">医院</label>
            <div class="col-md-8">
            <input type="text" class="form-control" name="chemo[hospital_name]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">方案</label>
            <div class="col-md-8">
            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_protocol'),"chemo[protocol]",'未知','form-control'); ?>
            </div>
        </div>
                <div class="form-group">
            <label class="control-label col-md-2">化疗周期</label>
            <div class="col-md-8">
            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_cycle'),"chemo[cycle]",'三周方案','form-control'); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">性质</label>
            <div class="col-md-8">
            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_property'),"chemo[property]",'未知','form-control'); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">疗程</label>
            <div class="col-md-8">
            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_period'),"chemo[period]",'未知','form-control'); ?>
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
            <a style="line-height:2.2" target='blank' href="/patientrecordmgr/list?patientid=<?= $patient->id ?>&code=cancer&type=chemo"><i class="fa fa-history"></i> 历史记录</a>
            </div>
        </div>
    </form>
</div>
