<?php
$diseasegroupid = $configs['diseasegroup'][0] ?? -1;
$diseasegroupstr = $configs['showstr']['diseasegroupstr'][0] ?? '';
if ($myauditor->diseasegroupid > 0) {
    $diseasegroupid = $myauditor->diseasegroupid;
    $diseasegroupstr = $myauditor->diseasegroup->name;
}
?>
<div class="form-group optaskfilter-list" data-key="diseasegroup">
    <input type="hidden" id="diseasegroup" name="diseasegroup" value="<?=$diseasegroupid?>">
    <input type="hidden" id="diseasegroupstr" name="diseasegroupstr" value="<?=$diseasegroupstr?>">
    <label class="col-xs-3 control-label" for="diseasegroupid">疾病组</label>
    <div class="col-xs-9">
        <?php
            echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArrayForFilter($myauditor), 'diseasegroupid', $diseasegroupid, 'js-select2 form-control', 'width: 100%');
        ?>
    </div>
    <script>
        $(function () {
            $('#diseasegroupid').on('change', function () {
                var me = $(this);
                var diseasegroupid = me.val();
                var diseasegroupstr = me.find('option:selected').text();

                $('#diseasegroup').val(diseasegroupid);
                $('#diseasegroupstr').val(diseasegroupstr);
            });
        });
    </script>
</div>