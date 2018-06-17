<?php
$opnodeids = $configs['opnode'] ?? [];
$opnodestr = $configs['showstr']['opnodestr'] ?? [];

$opnodeids_input = implode(',', $opnodeids);
$opnodestr_input = implode(',', $opnodestr);
?>
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e6e6e6;
    }
</style>
<div class="form-group optaskfilter-list" data-key="opnode">
    <div class="optaskfilter-list-opnode <?= 0 == count($opnodeids) ? 'none' : '' ?>">
        <label class="col-sm-3 control-label" for="opnodeids">任务节点</label>
        <div class="col-sm-9">
            <select id="opnodeid" class="J_select form-control" multiple="multiple" style="width: 100%;"
                    name="opnodeid"
                    data-placeholder="选择任务节点">
                <?php
                $optasktplids = $configs['optasktpl'] ?? [];
                if(count($optasktplids) == 1 && $optasktplids['0'] > 0){
                    $optasktplid = $optasktplids['0'];
                    $optasktpl = OpTaskTpl::getById($optasktplid);
                    $opnodes = OpNodeDao::getListByOpTaskTpl($optasktpl);
                    $arr = CtrHelper::toOpnodeCtrArray($opnodes);
                    foreach ($arr as $key => $value) { ?>
                        <option <?= in_array($key, $opnodeids) ? 'selected' : '' ?>
                                value="<?= $key ?>"><?= $value ?></option>
                    <?php }
                } ?>
            </select>
        </div>
    </div>
    <script>
        $(function () {
            $('#opnodeid').on('select2:selecting', function(e) {
                var vals = $(this).val();
                // 已选其他选全部 或 已选全部选其他 都需要清除以前的选项重新选
                if ((e.params.args.data.id === '0') || (vals !== null && vals[0] === '0')) {
                    $(this).val(e.params.args.data.id).trigger("change");
                }
            });
            $(".J_select").select2();
        });
    </script>
</div>
