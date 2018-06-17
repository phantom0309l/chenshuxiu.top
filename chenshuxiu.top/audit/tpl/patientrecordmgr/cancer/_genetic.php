<div class="">
    <form class="patientrecord-panel form form-horizontal">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>">
        <input type="hidden" name="code" value="cancer">
        <input type="hidden" name="type" value="genetic">
        <div class="form-group">
            <label class="control-label col-md-2">检测日期</label>
            <div class="col-md-8">
            <input type="text" class="calendar form-control" name="thedate" value="<?= date('Y-m-d')?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-2">内容</label>
            <div class="col-md-8">
            <?php echo HtmlCtr::getCheckboxCtrImp4OneUi(PatientRecordCancer::getOptionByCode('genetic'),"items",[], ''); ?>
            <input type="text" style="display:none" class="form-control" id="genetic_items" name="genetic[items]" value="">
            <input type="text" style="display:none" class="form-control" id="genetic_item_other" name="genetic[item_other]" value="">
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
            <a style="line-height:2.2" target='blank' href="/patientrecordmgr/list?patientid=<?= $patient->id ?>&code=cancer&type=genetic"><i class="fa fa-history"></i> 历史记录</a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function(){
        $(document).on('click', 'input[name="items"]', function(){
            var option_other = 0;
            var options = [];
            $('input[name="items"]').each(function() {
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

            $('#genetic_items').val(options_str);

            if (option_other == 1) {
                $("#genetic_item_other").show();
            } else {
                $("#genetic_item_other").hide();
            }
        });
    });
</script>
