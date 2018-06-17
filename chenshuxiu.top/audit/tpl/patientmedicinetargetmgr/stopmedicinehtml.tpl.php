<div class="modal-dialog modal-dialog-popin">
    <div class="modal-content">
        <div class="block block-themed block-transparent remove-margin-b">
            <div class="block-header bg-primary">
                <ul class="block-options">
                    <li>
                        <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                    </li>
                </ul>
                <h3 class="block-title">停药：<span class="text-warning"><?= $medicine->name ?></span></h3>
            </div>
            <div class="block-content">
                <form class="drugBox drugBox-stop">
                    <input type="hidden" value="<?= $patient->id ?>" id="patientid" name="patientid" />
                    <input type="hidden" value="<?= $medicine->id ?>" id="medicineid" name="medicineid" />
                    <input type="hidden" value="<?= $pmtarget->id ?>" id="pmtargetid" name="pmtargetid" />
                    <div class="form-group">
                        <label>停药日期<span class="red">(必填)</span></label>
                        <input type="text" class="form-control calendar" name="record_date" value="<?= date("Y-m-d") ?>"/>
                    </div>
                    <div class="form-group">
                        <label>备注</label>
                        <textarea class="form-control" rows="7" name="auditremark"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
            <button class="btn btn-sm btn-danger stopMedicineSubmitBtn" type="button" data-type="stopAndRemove"> 停药并删除应用药</button>
            <button class="btn btn-sm btn-primary stopMedicineSubmitBtn" type="button" data-type="stop"> 仅停药</button>
        </div>
    </div>
</div>
<script>
$(function() {
    $('.modal-dialog').draggable({ cursor: "move"});
})
</script>
