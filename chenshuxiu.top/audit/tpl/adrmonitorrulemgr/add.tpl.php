<?php
$pagetitle = "新建药品不良反应监测规则";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v5/page/audit/adrmonitorrulemgr/add/add.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .form-group .control-label {
        width: 110px;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e6e6e6;
    }
    #rule_table td {
        padding-top: 20px;
    }
    #rule_table td {
        border-bottom: 1px dashed #DDD;
    }
    #rule_table tr:last-child td {
        border-bottom: 0;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12 block-content">
            <form class="form-horizontal myForm">
                <div class="form-group">
                    <label class="col-md-5 control-label" for="medicine_common_name">药品通用名称</label>
                    <div class="col-md-7">
                        <input class="form-control" type="text" id="medicine_common_name" name="medicine_common_name"
                               placeholder="请填写药品通用名称..">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-5 control-label">药品</label>
                    <div class="col-md-7">
                        <select class="J_select form-control" multiple="multiple" style="width: 100%;"
                                name="medicineids[]"
                                data-placeholder="请选择药品..">
                            <?php foreach ($medicines as $medicine) { ?>
                                <option value="<?= $medicine->id ?>"><?= $medicine->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-5 control-label">疾病</label>
                    <div class="col-md-7">
                        <select class="J_select form-control" multiple="multiple" style="width: 100%;"
                                name="diseaseids[]"
                                data-placeholder="请选择疾病..">
                            <?php foreach ($diseases as $disease) { ?>
                                <option value="<?= $disease->id ?>"><?= $disease->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-5 control-label">规则</label>
                    <div class="col-md-7">
                        <div class="block block-bordered remove-margin">
                            <div class="block-header bg-gray-lighter">
                                <ul class="block-options">
                                    <li>
                                        <a id="add_rule" href="javascript: void(0);"><i
                                                    class="fa fa-plus text-primary"></i><span
                                                    class="text-primary"> 添加</span></a>
                                    </li>
                                </ul>
                                规则
                            </div>
                            <div class="block-content">
                                <table id="rule_table" style="width: 100%;">
                                    <tbody>
                                    <tr class="hide">
                                        <td>
                                            <div class="form-group">
                                                <label class="col-md-5 control-label">周期区间</label>
                                                <div class="col-md-7">
                                                    <div class="col-md-12 remove-padding">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">[</span>
                                                            <input class="form-control tc" type="text"
                                                                   data-name="week_from" placeholder="最小为1">
                                                            <span class="input-group-addon"
                                                                  style="border-left: 0; border-right: 0;">,</span>
                                                            <input class="form-control tc" type="text"
                                                                   data-name="week_to" placeholder="最大为∞">
                                                            <span class="input-group-addon">)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-5 control-label" for="week_interval">间隔周期</label>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input class="form-control tc" type="text"
                                                               data-name="week_interval" placeholder="最小为1">
                                                        <span class="input-group-addon">周</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-5 control-label">监测项目</label>
                                                <div class="col-md-7">
                                                    <?php foreach ($itemtpls as $key => $value) { ?>
                                                        <label class="mr10 css-input css-checkbox css-checkbox-rounded css-checkbox-info">
                                                            <input type="checkbox"
                                                                   data-name="items" value="<?= $key ?>"><span></span> <?= $value ?>
                                                        </label>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"></label>
                                                <div class="col-md-9">
                                                    <a class="btn btn-warning J_remove_rule">删除</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <button id="form_submit" class="btn btn-success">创建监测规则</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php

$footerScript = <<<STYLE

    $(function() {
        $(".J_select").select2();
    })

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>