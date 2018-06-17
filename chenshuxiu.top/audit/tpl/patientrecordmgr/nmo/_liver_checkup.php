<div class="">
    <form class="patientrecord-panel form form-horizontal">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>">
        <input type="hidden" name="code" value="nmo">
        <input type="hidden" name="type" value="liver_checkup">
        <div class="form-group">
            <label class="control-label col-md-2">日期</label>
            <div class="col-md-8">
            <input type="text" class="calendar form-control" name="thedate" value="<?= date('Y-m-d')?>">
            </div>
        </div>
		<div class="form-group">
            <label class="control-label col-md-2">ALT</label>
            <div class="col-md-8">
            <input type="text" class="form-control" name="liver_checkup[ALT]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">AST</label>
            <div class="col-md-8">
            <input type="text" class="form-control" name="liver_checkup[AST]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">DBIL</label>
            <div class="col-md-8">
            <input type="text" class="form-control" name="liver_checkup[DBIL]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">TBIL</label>
            <div class="col-md-8">
            <input type="text" class="form-control" name="liver_checkup[TBIL]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">GGT</label>
            <div class="col-md-8">
            <input type="text" class="form-control" name="liver_checkup[GGT]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">ALP</label>
            <div class="col-md-8">
            <input type="text" class="form-control" name="liver_checkup[ALP]" value="">
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
            <a style="line-height:2.2" target='blank' href="/patientrecordmgr/list?patientid=<?= $patient->id ?>&code=nmo&type=liver_checkup"><i class="fa fa-history"></i> 历史记录</a>
            </div>
        </div>
    </form>
</div>
