<?php
$auditorid = $configs['auditor'][0] ?? -3;
$auditorstr = $configs['showstr']['auditorstr'][0] ?? '';
?>
<div class="form-group optaskfilter-list" data-key="auditor">
    <input type="hidden" id="auditor" name="auditor" value="<?=$auditorid?>">
    <input type="hidden" id="auditorstr" name="auditorstr" value="<?=$auditorstr?>">
    <label class="col-sm-3 control-label" for="auditorid">责任人</label>
    <div class="col-sm-9">
        <?php
        echo HtmlCtr::getSelectCtrImp(CtrHelper::getYunyingAuditorCtrArrayForOpTask(), "auditorid", $auditorid, 'js-select2 form-control', 'width: 100%;');
        ?>
    </div>
</div>
<script>
    $(function () {
        $('#auditorid').on('change', function () {
            var me = $(this);
            var auditorid = me.val();
            var auditorstr = me.find('option:selected').text();

            $('#auditor').val(auditorid);
            $('#auditorstr').val(auditorstr);
        });
    });
</script>
