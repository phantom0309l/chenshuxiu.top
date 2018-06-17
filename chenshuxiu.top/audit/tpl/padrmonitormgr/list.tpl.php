<?php
$pagetitle = "药物不良反应监测列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/vendor/oneui/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js",
    $img_uri . "/v5/page/audit/padrmonitormgr/list/list.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .searchBar {
        padding: 8px 5px 8px 10px;
        border: 1px solid #e9e9e9;
        margin: 10px 15px 10px;
    }

    .doctor-select {
        width: 100px;
        display: inline;
    }
    
    .J_patientPictures_button {
        margin-left: 5px; 
        border: 0; 
        background-image: url('{$img_uri}/m/img/add.jpg'); 
        text-indent: -9999px; 
        height: 44px; 
        line-height: 44px; 
        width: 66px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="block block-bordered" style="margin: 10px 15px 10px;">
            <div class="block-header bg-gray-lighter">
                <span>患者：<b class="text-primary"><?= $patient->name ?></b></span>
                <span class="ml10">疾病： <b class="text-primary"><?= $disease->name ?></b></span>
            </div>
            <div class="block-content tab-content" style="padding: 20px 10px;">
                <table class="js-table-sections table table-hover">
                    <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>监测项目</th>
                        <th class="">本次规则药品</th>
                        <th class="">计划检查日期</th>
                        <th class="">实际检查日期</th>
                        <th class="">类型</th>
                        <th class="">任务状态</th>
                        <th class="">状态</th>
                        <th class="tc">操作</th>
                    </tr>
                    </thead>
                    </tbody>
                    <?php foreach ($padrmonitors as $padrmonitor) { ?>
                        <tbody class="js-table-sections-header open">
                        <tr>
                            <td class="tc">
                                <i class="fa fa-angle-right"></i>
                            </td>
                            <td>
                                <span class="text-primary"><?= ADRMonitorRuleItem::getItemStr($padrmonitor->adrmonitorruleitem_ename) ?></span>
                            </td>
                            <td class="">
                                <?= $padrmonitor->medicine->name ?>
                            </td>
                            <td class=""><?= $padrmonitor->plan_date ?></td>
                            <td class="">
                                <?= $padrmonitor->the_date ?>
                                <?php
                                $firstDrugDate = PADRMonitor_AutoService::getFirstDrugDate($padrmonitor->patientid, $padrmonitor->diseaseid, $padrmonitor->medicineid);
                                if ("0000-00-00" == $padrmonitor->the_date || !$firstDrugDate) {
                                    echo "";
                                } else {
                                    echo " | 第 <span class=\"text-info\">" . floor(PADRMonitor_AutoService::getWeek($firstDrugDate, $padrmonitor->the_date)) . "</span> 周";
                                }
                                ?>
                            </td>
                            <td class=""><?= $padrmonitor->getTypeStr() ?></td>
                            <td class="">
                                <?php
                                $optask = $padrmonitor->getOpTask();
                                if ($optask instanceof OpTask) {
                                    switch ($optask->status) {
                                        case 0:
                                        case 2:
                                            echo "<span class='text-info'>" . $optask->getStatusAndOpNodeTitle() . "</span>";
                                            break;
                                        case 1:
                                            echo "<span class='text-success'>" . $optask->getStatusAndOpNodeTitle() . "</span>";
                                            break;
                                    }
                                } else {
                                    echo "<span class='text-warning'>未找到关联任务</span>";
                                }
                                ?>
                            </td>
                            <td class="">
                                    <span class="label
                                    <?php switch ($padrmonitor->status) {
                                        case 0:
                                        case 1:
                                            echo 'label-info';
                                            break;
                                        case 2:
                                            echo 'label-success';
                                            break;
                                        case 3:
                                            echo 'label-danger';
                                            break;
                                    } ?>"><?= $padrmonitor->getStatusStr() ?></span>
                            </td>
                            <td class="tc">
                                <button class="btn btn-xs btn-default push-5-r push-10" type="button"
                                        data-backdrop="static" data-toggle="modal"
                                        data-target="#modal-modify"
                                        data-padrmonitorid="<?= $padrmonitor->id ?>"
                                        data-enamestr="<?= $padrmonitor->getEnameStr() ?>"
                                        data-thedate="<?= $padrmonitor->the_date ?>">
                                    <i class="fa fa-pencil"></i> <?= $padrmonitor->status == 1 ? '代患者填写' : '修改' ?>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                        <tbody>
                        <?php foreach ($padrmonitor->getHistory() as $history) { ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="">
                                    <?= $history->medicine->name ?>
                                </td>
                                <td class=""><?= $history->plan_date ?></td>
                                <td class="">
                                    <?= $history->the_date ?>
                                    <?php
                                    $history_firstDrugDate = PADRMonitor_AutoService::getFirstDrugDate($history->patientid, $history->diseaseid, $history->medicineid);
                                    if ("0000-00-00" == $history->the_date || !$firstDrugDate) {
                                        echo "";
                                    } else {
                                        echo " | 第 <span class=\"text-info\">" . floor(PADRMonitor_AutoService::getWeek($history_firstDrugDate, $history->the_date)) . "</span> 周";
                                    }
                                    ?>
                                </td>
                                <td class=""><?= $history->getTypeStr() ?></td>
                                <td class="">
                                    <?php
                                    $optask = $history->getOpTask();
                                    if ($optask instanceof OpTask) {
                                        switch ($optask->status) {
                                            case 0:
                                            case 2:
                                                echo "<span class='text-info'>" . $optask->getStatusAndOpNodeTitle() . "</span>";
                                                break;
                                            case 1:
                                                echo "<span class='text-success'>" . $optask->getStatusAndOpNodeTitle() . "</span>";
                                                break;
                                        }
                                    } else {
                                        echo "<span class='text-warning'>未找到关联任务</span>";
                                    }
                                    ?>
                                </td>
                                <td class="">
                                    <span class="label
                                    <?php
                                    switch ($history->status) {
                                        case 1:
                                            echo 'label-info';
                                            break;
                                        case 2:
                                            echo 'label-success';
                                            break;
                                        case 3:
                                            echo 'label-danger';
                                            break;

                                    } ?>"><?= $history->getStatusStr() ?></span>
                                </td>
                                <td class="tc">
                                    <button class="btn btn-xs btn-default push-5-r push-10" type="button"
                                            data-backdrop="static" data-toggle="modal"
                                            data-target="#modal-modify"
                                            data-padrmonitorid="<?= $history->id ?>"
                                            data-enamestr="<?= $history->getEnameStr() ?>"
                                            data-thedate="<?= $history->the_date ?>">
                                        <i class="fa fa-pencil"></i> 修改
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    <?php } ?>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title J_modal_modify_title"></h3>
                </div>
                <div class="block-content" style="max-height: 500px; overflow: auto;">
                    <form id="modify_form" onsubmit="return false;">
                        <input type="hidden" name="padrmonitorid" value="">
                        <div class="form-group">
                            <label>实际检查日期</label>
                            <input type="text" name="thedate" class="form-control calendar" value=""
                                   placeholder="请选择患者实际检查日期">
                        </div>
                        <div class="form-group">
                            <label for="example-nf-password">检查照片</label>
                            <div>
                                <div>
                                    <?php
                                    $picWidth = 140;
                                    $picHeight = 140;
                                    $maxImgLen = 0;
                                    $pictureInputName = "pictureids";
                                    $isCut = false;
                                    $objtype = "Auditor";
                                    $objid = $myauditor->id;
                                    require_once("$dtpl/mult_picture.ctr.php");
                                    ?>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary J_modal_submit" type="button">
                    <i class="fa fa-check"></i> 确定
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-patientPictures" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">患者图片</h3>
                </div>
                <div class="block-content" style="max-height: 500px; overflow: auto;">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary J_modal_submit" type="button">
                    <i class="fa fa-check"></i> 确定
                </button>
            </div>
        </div>
    </div>
</div>
<?php
$footerScript = <<<STYLE
    var patientid = {$patient->id};
    var diseaseid = {$disease->id};
    var doctorid = {$patient->doctorid};
    var img_uri = '{$img_uri}';
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
