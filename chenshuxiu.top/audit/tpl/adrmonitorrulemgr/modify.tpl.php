<?php
$pagetitle = "修改药品不良反应监测规则";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v5/page/audit/adrmonitorrulemgr/modify/modify.js",
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
                <input type="hidden" name="adrmonitorruleid" value="<?= $adrmonitorrule->id ?>">
                <div class="form-group">
                    <label class="col-md-5 control-label" for="medicine_common_name">药品通用名称</label>
                    <div class="col-md-7">
                        <input class="form-control" type="text" id="medicine_common_name" name="medicine_common_name"
                               placeholder="请填写药品通用名称.."
                               value="<?= $adrmonitorrule->medicine_common_name ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-5 control-label">药品</label>
                    <div class="col-md-7">
                        <select class="form-control" style="width: 100%;"
                                name="medicineid"
                                data-placeholder="请选择药品..">
                            <?php foreach ($medicines as $medicine) { ?>
                                <option value="<?= $medicine->id ?>"
                                    <?= $adrmonitorrule->medicineid == $medicine->id ? "selected" : '' ?>><?= $medicine->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-5 control-label">疾病</label>
                    <div class="col-md-7">
                        <select class="form-control" style="width: 100%;"
                                name="diseaseid"
                                data-placeholder="请选择疾病..">
                            <?php foreach ($diseases as $disease) { ?>
                                <option value="<?= $disease->id ?>"
                                    <?= $adrmonitorrule->diseaseid == $disease->id ? "selected" : '' ?>><?= $disease->name ?></option>
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
                                                                   data-name="items"
                                                                   value="<?= $key ?>"><span></span> <?= $value ?>
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
                                    <?php
                                    $items = $adrmonitorrule->getItems();
                                    $arr = [];
                                    foreach ($items as $item) {
                                        $tempArr = $arr["{$item->week_from},{$item->week_to},{$item->week_interval}"];
                                        if (empty($tempArr)) {
                                            $tempArr = [
                                                'week_from' => $item->week_from,
                                                'week_to' => $item->week_to,
                                                'week_interval' => $item->week_interval,
                                                'checkeds' => [],
                                            ];
                                        }
                                        $tempArr['checkeds'][] = $item->ename;
                                        $arr["{$item->week_from},{$item->week_to},{$item->week_interval}"] = $tempArr;
                                    }
                                    $index = 0;
                                    foreach ($arr as $item) { ?>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-md-5 control-label">周期区间</label>
                                                    <div class="col-md-7">
                                                        <div class="col-md-12 remove-padding">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">[</span>
                                                                <input class="form-control tc" type="text"
                                                                       data-name="week_from" placeholder="最小为1"
                                                                       name="rules[<?= $index ?>][week_from]"
                                                                       value="<?= $item['week_from'] ?>">
                                                                <span class="input-group-addon"
                                                                      style="border-left: 0; border-right: 0;">,</span>
                                                                <input class="form-control tc" type="text"
                                                                       data-name="week_to" placeholder="最大为∞"
                                                                       name="rules[<?= $index ?>][week_to]"
                                                                       value="<?= $item['week_to'] ?>">
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
                                                                   data-name="week_interval" placeholder="最小为1"
                                                                   name="rules[<?= $index ?>][week_interval]"
                                                                   value="<?= $item['week_interval'] ?>">
                                                            <span class="input-group-addon">周</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-5 control-label">监测项目</label>
                                                    <div class="col-md-7">
                                                        <?php
                                                        $checkeds = $item['checkeds'];
                                                        foreach ($itemtpls as $key => $value) { ?>
                                                            <label class="mr10 css-input css-checkbox css-checkbox-rounded css-checkbox-info">
                                                                <input type="checkbox"
                                                                       data-name="items"
                                                                       name="rules[<?= $index ?>][items][]"
                                                                       value="<?= $key ?>"
                                                                    <?= in_array($key, $checkeds) ? 'checked' : '' ?>>
                                                                <span></span> <?= $value ?>
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
                                        <?php
                                        $index++;
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <button id="form_submit" class="btn btn-success">修改监测规则</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php

$footerScript = <<<STYLE

    $(function() {
        $("select").prop("disabled", true); 
    })

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>