<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
            </button>

            <h4 class="modal-title" id="drugStopLabel">
                停药：<span class="text-primary"><?= $medicine->name ?></span>
            </h4>
        </div>

        <div class="modal-body">
            <form class="drugBox">
                <input type="hidden" value="<?= $patient->id ?>" id="patientid" name="patientid" />
                <input type="hidden" value="<?= $medicine->id ?>" id="medicineid" name="medicineid" />
                <div class="form-group">
                    <label>停药日期<span class="red">(必填)</span></label>
                    <input type="text" class="form-control calendar" name="record_date" value="<?= date("Y-m-d") ?>"/>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label">停药原因</label>
                    <div>
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getStop_drug_typeCtrArray(),"stop_drug_type",$patientmedicineref->stop_drug_type, 'form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>备注</label>
                    <textarea class="form-control" rows="7" name="content"><?= $patientmedicineref->remark ?></textarea>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                关闭
            </button>
            <button type="button" class="btn btn-primary stopDrugBtn">
                提交
            </button>
        </div>

    </div>
</div>
<script>
$(function() {
    $('.modal-dialog').draggable({ cursor: "move"});
})
</script>
