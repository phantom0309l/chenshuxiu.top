 <div class="modal-dialog modal-dialog-popin">
    <div class="modal-content">
        <div class="block block-themed block-transparent remove-margin-b">
            <div class="block-header bg-primary">
                <ul class="block-options">
                    <li>
                        <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                    </li>
                </ul>
                <h3 class="block-title" >添加<span class="text-warning"><?= $medicine->name ?></span>实际用药记录</h3>
            </div>
            <div class="block-content">
                <form class="drugBox drugBox-add">
                    <input type="hidden" value="<?= $patient->id ?>" id="patientid" name="patientid" />
                    <input type="hidden" value="<?= $medicine->id ?>" id="medicineid" name="medicineid" />
                    <input type="hidden" value="<?= $pmtarget->id ?>" id="pmtargetid" name="pmtargetid" />
                    <div id="form-content">
                    <div class="form-group">
                        <label class="col-md-12 remove-padding">药</label>
                        <input class="form-control" id="medicine_name" value="<?=$medicine->name?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>用药日期<span class="red">(必填)</span></label>
                        <input type="text" class="form-control calendar" name="record_date" value="<?= date("Y-m-d") ?>"/>
                    </div>
                    <div class="form-group">
                        <label>用药剂量 <?= $medicine->unit ?> <span class="red">(必填)</span></label>
                        <input type="text" class="form-control" name="drug_dose" placeholder="别忘记填写剂量单位哦" value="<?=$pmtarget->drug_dose?>"/>
                    </div>
                    <div class="form-group">
                        <label>用药频率</label>
                        <select class="form-control" name="drug_frequency">
                            <option value="">请选择...</option>
                            <?php foreach($drug_frequency_arr as $k => $v){ ?>
                            <option value="<?= $k ?>" <?php if($v == $pmtarget->drug_frequency){ ?>selected="selected"<?php }?>><?= $v ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>用药状态</label>
                        <div class="">
                        <?php 
                            $statusDescArray = PatientMedicineSheetItem::$statusDescArray;
                            array_pop($statusDescArray);
                        ?>
                        <?php echo HtmlCtr::getRadioCtrImp4OneUi($statusDescArray, "status", 0 ,'css-radio-warning'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>备注</label>
                        <textarea class="form-control" rows="7" name="auditremark"></textarea>
                    </div>
                    </div>
                </form>
                <div class="search-result-div collapse">
                <p class="text-right push-10">
                    <a class="btn btn-warning btn-sm btn-close-search-result" href="javascript:">关闭</a>
                </p>
                <table class="table table-bordered table-hover search-result"></table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
            <button class="btn btn-sm btn-primary addMedicineSubmitBtn" type="button"><i class="fa fa-check"></i> 提交</button>
        </div>
    </div>
</div>
<script>
$(function() {
    $('.modal-dialog').draggable({ cursor: "move"});
})
</script>
