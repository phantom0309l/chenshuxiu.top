<div class="">
    <form class="patientrecord-panel form form-horizontal">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>">
        <input type="hidden" name="code" value="cancer">
        <input type="hidden" name="type" value="diagnose">
        <div class="form-group">
            <label class="control-label col-md-2">诊断日期</label>
            <div class="col-md-8">
            	<input type="text" class="calendar form-control" name="diagnose[thedate]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">部位</label>
            <div class="col-md-8">
                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('diagnose_position'),"diagnose[position]",'not','form-control diagnose_position'); ?>
            	<input type="text" style="display:none" class="form-control" id="diagnose_position_other_show" name="diagnose[position_other]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">组织起源</label>
            <div class="col-md-8">
                <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('diagnose_start'),"diagnose[diagnose_start]",'not','form-control diagnose_start'); ?>
                <input type="text" style="display:none" class="form-control" id="diagnose_start_other" name="diagnose[diagnose_start_other]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">特殊</label>
            <div class="col-md-8">
            	<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('diagnose_special'),"diagnose[special]",'','form-control diagnose_special'); ?>
            	<input type="text" style="display:none" class="form-control" id="diagnose_special_other" name="diagnose[special_other]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">转移日期</label>
            <div class="col-md-8">
                <input type="text" class="calendar form-control" name="diagnose[shift_thedate]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">转移位置</label>
            <div class="col-md-8">
                <?php echo HtmlCtr::getCheckboxCtrImp4OneUi(PatientRecordCancer::getOptionByCode('diagnose_shift_position'),"diagnose_shift_position",[], ''); ?>
                <input type="text" style="display:none" class="form-control" id="diagnose_shift_position_V" name="diagnose[shift_position]" value="">
                <input type="text" style="display:none" class="form-control" id="diagnose_shift_position_other" name="diagnose[shift_position_other]" value="">
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
            <a style="line-height:2.2" target='blank' href="/patientrecordmgr/list?patientid=<?= $patient->id ?>&code=cancer&type=diagnose"><i class="fa fa-history"></i> 历史记录</a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
	$(function(){
        $(document).on('click', 'input[name="diagnose_shift_position"]', function(){
            var option_other = 0;
            var options = [];
            $('input[name="diagnose_shift_position"]').each(function() {
                var m = $(this);

                if (m.prop('checked') == true) {
                    options.push(m.val());

                    if (m.val() == '其他') {
                        option_other = 1;
                    }
                }

                if (m.prop('checked') == true && m.val() == '其他') {
                    option_other = 1;
                }
            });

            var options_str = options.join(',');

            $('#diagnose_shift_position_V').val(options_str);

            if (option_other == 1) {
                $("#diagnose_shift_position_other").show();
            } else {
                $("#diagnose_shift_position_other").hide();
            }
        });
        $(document).on('change', '.diagnose_position', function(){
            var me = $(this);
            var option = me.val();
            if (option == '其他') {
                $("#diagnose_position_other_show").show();
            } else {
                $("#diagnose_position_other_show").hide();
            }
        });
		$(document).on('change', '.diagnose_start', function(){
			var me = $(this);
			var option = me.val();
			if (option == '其他') {
				$("#diagnose_start_other").show();
			} else {
				$("#diagnose_start_other").hide();
			}
		});
		$(document).on('change', '.diagnose_special', function(){
			var me = $(this);
			var option = me.val();
			if (option == '其他') {
				$("#diagnose_special_other").show();
			} else {
				$("#diagnose_special_other").hide();
			}
		});
	});
</script>
