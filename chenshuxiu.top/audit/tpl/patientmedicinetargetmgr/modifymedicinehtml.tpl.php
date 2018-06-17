 <div class="modal-dialog modal-dialog-popin">
    <div class="modal-content">
        <div class="block block-themed block-transparent remove-margin-b">
            <div class="block-header bg-primary">
                <ul class="block-options">
                    <li>
                        <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                    </li>
                </ul>
                <h3 class="block-title" >修改医嘱药</h3>
            </div>
            <div class="block-content">
                <form class="drugBox drugBox-standard">
                    <input type="hidden" value="<?= $patient->id ?>" id="patientid" name="patientid" />
                    <input type="hidden" value="<?=$medicine->id?>" id="medicineid" name="medicineid" />
                    <input type="hidden" value="<?=$pmtarget->id?>" id="pmtargetid" name="pmtargetid" />
                    <div id="form-content">
                    <div class="form-group">
                        <label class="col-md-12 remove-padding">药<span class="red">(必填)</span></label>
                        <input type="text" class="form-control" id="medicine_name" value="<?=$medicine->name?>" readonly />
                    </div>
                    <div class="form-group collapse">
                        <label>用药日期<span class="red">(必填)</span></label>
                        <input type="text" class="form-control calendar" name="record_date" value="<?= date("Y-m-d") ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label>用药剂量 <span class="medicine-unit"></span><span class="red">(必填)</span></label>
                        <input type="text" class="form-control" name="drug_dose" placeholder="别忘记填写剂量单位哦" value="<?=$pmtarget->drug_dose?>"/>
                    </div>
                    <div class="form-group">
                        <label>用药频率</label>
                        <select class="form-control" name="drug_frequency">
                            <option value="">请选择...</option>
                            <?php foreach($drug_frequency_arr as $a){ ?>
                            <option value="<?= $a ?>" name="<?=$pmtarget->drug_frequency;?>" <?php if($pmtarget->drug_frequency == $a) {?>selected="selected"<?php }?>><?= $a ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>调药规则</label>
                        <textarea class="form-control" rows="7" name="drug_change"><?=$pmtarget->drug_change?></textarea>
                    </div>
                    <div class="form-group">
                        <label>医嘱备注（运营）</label>
                        <textarea class="form-control" rows="7" name="auditremark"><?=$pmtarget->auditremark?></textarea>
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
            <button class="btn btn-sm btn-primary modifyMedicineSubmitBtn" type="button"><i class="fa fa-check"></i> 提交</button>
        </div>
    </div>
</div>
<script>
$(function() {
    $('.modal-dialog').draggable({ cursor: "move"});
    var fa = function(e) {
        var q = $('#medicine-search').val();
        $('#form-content').hide();
        $('.search-result-div').show();
        $('.addStandardMedicineSubmitBtn').prop('disabled', true);
        $.ajax({
            type: "post",
            url: "/medicinemgr/listOfSearchJson",
            data: {"q" : q},
            dataType: "json",
            success : function(data){
                var str = '<tr><td>药品ID</td><td>药名</td><td>学名</td><td>单位</td></tr>';
                $.each(data, function(index, one){
                    var tr = "<tr><td>"+one.id+"</td><td>"+one.name+"</td><td>"+one.scientificname+"</td><td>"+one.unit+"</td></tr>";
                    str += tr;
                });
                $('.search-result').html(str);
            } 
        })
    }

    $(document).off('click', '#btn-medicine-search').on('click', '#btn-medicine-search', fa);
    $(document).off('keyup', '#medicine-search').on('keyup', '#medicine-search', function(e) {
        if (e.keyCode == 13) {//回车
            fa(e);
        }
    });

    $(document).off('click', '.btn-close-search-result').on('click', '.btn-close-search-result', function(e){
        $('.search-result-div').hide();
        $('#form-content').show();
        $('.addStandardMedicineSubmitBtn').prop('disabled', false);
    });
    $(document).off('click', '.search-result tr').on('click', '.search-result tr', function(e){
        var medicineid = $(this).find('td:first-child').text();
        var medicine_name = $(this).find('td:eq(1)').text();
        var medicine_unit = $(this).find('td:eq(3)').html();
        $('.medicine-unit').html(medicine_unit);
        $('#medicineid').val(medicineid);
        $('#medicine_name').val(medicine_name);

        $('.search-result-div').hide();
        $('#form-content').show();
        $('.addStandardMedicineSubmitBtn').prop('disabled', false);
    });
})
</script>
