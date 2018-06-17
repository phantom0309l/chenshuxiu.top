<?php
$optasktplids = $configs['optasktpl'] ?? [];
$optasktplstr = $configs['showstr']['optasktplstr'] ?? [];

$default_optasktplid = $optasktplids[0] ?? 0;
//$optasktplstr = [];
//foreach ($optasktplids as $optasktplid) {
//    $optasktplstr[] = OpTaskTpl::getById($optasktplid)->title;
//}

$optasktplids_input = implode(',', $optasktplids);
$optasktplstr_input = implode(',', $optasktplstr);
?>
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e6e6e6;
    }
</style>
<div class="form-group optaskfilter-list" data-key="optasktpl">
    <div>
        <input type="hidden" id="optasktpl" name="optasktpl" value="<?= $optasktplids_input ?>">
        <input type="hidden" id="optasktplstr" name="optasktplstr" value="<?= $optasktplstr_input ?>">
        <label class="col-sm-3 control-label" for="optasktplids">任务类型</label>
        <div class="col-sm-9">
            <select id="optasktplid" class="J_select form-control" multiple="multiple" style="width: 100%;"
                    name="optasktplid"
                    data-placeholder="选择任务类型">
                <?php
                $arr = CtrHelper::toOptaskTplForDiseaseGroupCtrArray($myauditor->diseasegroup, $optasktpls);
                foreach ($arr as $key => $value) { ?>
                    <option <?= in_array($key, $optasktplids) ? 'selected' : '' ?>
                            value="<?= $key ?>"><?= $value ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <script>
        $(function () {
            $('#optasktplid').on('select2:selecting', function(e) {
                var vals = $(this).val();
                if (e.params.args.data.id === '0') {
                    $(this).val('0').trigger("change");
                } else if (vals !== null && vals[0] === '0') {
                    $(this).val(e.params.args.data.id).trigger("change");
                }
            });
            $('#optasktplid').on('change', function(e) {
                var vals = $(this).val();
                var opnode_select = $(".optaskfilter-list-opnode #opnodeid");
                opnode_select.val(null).trigger("change");
                if(vals !== null && vals.length == 1 && vals[0] !== '0'){
                    $(".optaskfilter-list-opnode").removeClass("none");
                    var optasktplid = vals[0];
                    $.ajax({
                        url : '/opnodemgr/getArrByOptasktplidjson',
                        type : 'get',
                        dataType : 'json',
                        data : {
                            optasktplid : optasktplid
                        },
                        success : function (result) {
                            if (result.errno == -1) {
                                alert(result.errmsg);
                            } else {
                                var opnodes = result.opnodes;
                                var html_str = "";
                                $.each(opnodes,function(key,value) {
                                    html_str += "<option value=" + key + ">" + value + "</option>";
                                });
                                opnode_select.html(html_str);
                                opnode_select.val(0).trigger("change");
                            }
                        }
                    });
                }else {
                    $(".optaskfilter-list-opnode").addClass("none");
                    opnode_select.html("");
                }
            });
            $(".J_select").select2();
        });
    </script>
</div>
<?php include_once dirname(__FILE__) . "/_opnode.tpl.php"; ?>
