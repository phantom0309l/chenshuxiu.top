<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
            </button>

            <h4 class="modal-title" id="drugItemAddLabel">
                添加<span class="text-primary"><?= $medicine->name ?></span>用药记录
            </h4>
        </div>

        <div class="modal-body">
            <form class="drugBox">
                <input type="hidden" value="<?= $patient->id ?>" id="patientid" name="patientid" />
                <input type="hidden" value="<?= $medicine->id ?>" id="medicineid" name="medicineid" />
                    <div class="form-group">
                        <label>首次用药日期<span class="red">(填写后不可修改)</span></label>
                        <?php if($patientmedicineref->isNotFillFirstStartDate()){ ?>
                        <input type="text" class="form-control calendar" name="first_start_date" value=""/>
                        <?php } else { ?>
                        <p><?= $patientmedicineref->first_start_date?></p>
                        <?php } ?>
                    </div>
                <div class="form-group">
                    <label>用药日期<span class="red">(必填)</span></label>
                    <input type="text" class="form-control calendar" name="record_date" value="<?= date("Y-m-d") ?>"/>
                </div>
                <div class="form-group">
                    <label>用药剂量 <?= $medicine->unit ?> <span class="red">(必填)</span></label>
                    <input type="text" class="form-control" name="<?= 1 == $patient->diseaseid ? 'value' : 'drug_dose' ?>"/>
                </div>
                <div class="form-group">
                    <label>用药频率</label>
                    <select class="form-control" name="drug_frequency">
                        <option value="">请选择...</option>
                        <?php foreach($drug_frequency_arr as $a){ ?>
                        <option value="<?= $a ?>"><?= $a ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>备注</label>
                    <textarea class="form-control" rows="7" name="content"></textarea>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                关闭
            </button>
            <button type="button" class="btn btn-primary addDrugItemBtn">
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
