<div class="panel panel-default addDrugBoxShell none">
    <div class="panel-heading">
        <div class="title">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <span>添加用药<span>
        </div>
    </div>
    <div class="panel-body">
          <div class="addDrugBox">
            <div>
                <p>
                    <div class="col-xs-4">
                        <input type="text" placeholder="按药品名搜索" class="medicine_name form-control"/>
                    </div>
                    <span class="btn btn-primary searchMedicine">搜索</span>
                </p>
                <div class="medicineBox"></div>
            </div>
            <form id="drugitemForm" class="none">
                <input type="hidden" name="medicineid" value="0" class="medicineid"/>
                <input type="hidden" name="patientid" value="<?= $patient->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <td class="text-right">药名</td>
                        <td>
                            <span class="selectedDrugName"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">首次服药日期<span class="red">(填写后不可修改)</span></td>
                        <td>
                            <input type="text" name="first_start_date" value="" class="calendar"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">用药日期<span class="red">(必填)</span></td>
                        <td>
                            <input type="text" name="record_date" value="<?= date("Y-m-d") ?>" class="calendar"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">用药剂量 <span class="medicineUnit"></span> <span class="red">(必填)</span></td>
                        <td>
                            <input type="text" name="<?= 1 == $patient->diseaseid ? 'value' : 'drug_dose' ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">用药频率：</td>
                        <td>
                            <select class="form-control" name="drug_frequency">
                                <option value="">请选择...</option>
                                <?php foreach($drug_frequency_arr as $a){ ?>
                                <option value="<?= $a ?>"><?= $a ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">备注：</td>
                        <td>
                            <textarea class="form-control" rows="7" name="content"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <span class="btn btn-primary addDrugBtn">提交</span>
                        </td>
                    </tr>
                </table>
                </div>
            </form>
          </div>
    </div>
</div>
