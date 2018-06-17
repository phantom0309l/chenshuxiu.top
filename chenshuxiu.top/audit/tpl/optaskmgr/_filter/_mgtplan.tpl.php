<?php
$mgtgrouptplid = $configs['mgtgrouptpl'][0] ?? -2;
$mgtgrouptplstr = $configs['showstr']['mgtgrouptplstr'][0] ?? '全部';
?>
<div class="form-group optaskfilter-list" data-key="mgtgrouptpl">
    <input type="hidden" id="mgtgrouptpl" name="mgtgrouptpl" value="<?=$mgtgrouptplid?>">
    <input type="hidden" id="mgtgrouptplstr" name="mgtgrouptplstr" value="<?=$mgtgrouptplstr?>">
    <label class="col-xs-3 control-label" for="mgtgrouptplid">管理计划</label>
    <div class="col-xs-9">
        <?php
            echo HtmlCtr::getSelectCtrImp(CtrHelper::getMgtGroupTplCtrArrayForFilter(), 'mgtgrouptplid', $mgtgrouptplid, 'js-select2 form-control', 'width: 100%');
        ?>
    </div>
    <script>
        $(function () {
            $('#mgtgrouptplid').on('change', function () {
                var me = $(this);
                var mgtgrouptplid = me.val();
                var mgtgrouptplstr = me.find('option:selected').text();

                $('#mgtgrouptpl').val(mgtgrouptplid);
                $('#mgtgrouptplstr').val(mgtgrouptplstr);
            });
        });
    </script>
</div>