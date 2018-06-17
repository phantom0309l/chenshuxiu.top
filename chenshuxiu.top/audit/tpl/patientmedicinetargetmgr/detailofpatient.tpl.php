<?php
$pagetitle = "{$patient->name}患者用药";
$cssFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20180320',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20180320',
    $img_uri . '/v5/page/audit/patientmedicinetargetmgr/detailofpatient/detailofpatient.js?v=20180228',
]; //填写完整地址
$pageStyle = <<<STYLE
#main-container {
    background: #f5f5f5 !important;
}
.js-table-sections-header.open > tr {
    background-color: #f7f7f7;
}
.tali {
    text-align:left !important;
}
.h4-title {
    margin-bottom: 10px;
    padding-left: 10px;
    border-left: 2px solid #44b4a6;
}
.control-label {
    font-weight:500;
    text-align:left;
}
.medicine-history-p {
    padding:10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}
.medicine-history-p:hover {
    background-color: #edf6fd;
}
.table-div {
    overflow:hidden;
}

.cursor-pointer {
    cursor: pointer;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
//颜色数组
$colorArr = [
    -1 => '',
    1 => 'text-success',
    2 => 'text-warning',
    3 => 'text-danger',
];
$colorArr2 = [
    0 => '',
    1 => 'text-success',
    2 => 'text-warning',
    3 => 'text-danger',
];
$iconArr = [
    -1 => 'fa fa-clock-o',
    0 => 'si si-question',
    1 => 'fa fa-check-circle',
    2 => 'si si-close',
    3 => 'fa fa-circle-thin',
];
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12" id="top">
    <?php
    $pcard = $patient->getMasterPcard();
    ?>
    <?php include_once $tpl . "/patientmgr/_menu.tpl.php"; ?>
    <div class="content-div">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>" id="patientid"/>
        <section class="col-md-12">
            <div class="block">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">
                        <i class="si si-user"></i>
                        <span>患者基本信息</span>
                    </h3>
                </div>
                <div class="block-content">
                    <p>
                        <span>患者姓名：<?= $patient->name ?></span>
                        <span>所属医生:<?= $patient->doctor->name ?></span>
                    </p>
                    <p>
                        <span>具体疾病：<?= $patient->disease->name ?></span>
                    </p>
                    <p>
                        <span>性别：<?= $patient->getSexStr() ?></span>
                        <span>年龄：<?= $patient->getAgeStr() ?> 岁</span>
                        <span>城市：<?= $patient->getXprovinceStr(); ?> <?= $patient->getXcityStr(); ?></span>
                    </p>
                </div>
            </div>

            <!-- 不良反应监测 模态框 -->
            <div class="modal" id="open_adr_monitor" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="submit_monitor" action="/patientmedicinetargetmgr/openadr_monitorpost" method="post">
                            <div class="block block-themed block-transparent remove-margin-b">
                                <div class="block-header bg-primary">
                                    <ul class="block-options">
                                        <li>
                                            <button data-dismiss="modal" type="button">
                                                <i class="si si-close"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <h3 class="block-title">不良反应监测</h3>
                                </div>
                                <div class="block-content">
                                    <input type="hidden" id="patientid" name="patientid" value="<?= $patient->id ?>">
                                    <?php
                                    $items = PatientService::getCheckItemsByPatient($patient);

                                    foreach ($items as $ename => $title) {
                                        ?>
                                        <div class="form-group">
                                            <label class="" for="title"><?= $title ?></label>
                                            <div class="">
                                                <input class="form-control calendar checkdates" type="text" id="<?= $ename ?>"
                                                       name="checkdates[<?= $ename ?>]" placeholder="填写下一次开始日期">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                                <button class="btn btn-sm btn-primary" type="submit" id="adr_monitor_submit" data-dismiss="modal">
                                    <i class="fa fa-check"></i>提交
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 用药核对 模态框 -->
            <div class="modal" id="open_medicine_check" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="submit_medicine_check" action="/patientmedicinetargetmgr/openMedicine_checkpost" method="get">
                            <input type="hidden" name="patientid" value="<?= $patient->id ?>">
                            <div class="block block-themed block-transparent remove-margin-b">
                                <div class="block-header bg-primary">
                                    <ul class="block-options">
                                        <li>
                                            <button data-dismiss="modal" type="button">
                                                <i class="si si-close"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <h3 class="block-title">用药核对</h3>
                                </div>
                                <div class="block-content">
                                    <div class="form-group">
                                        <label class="" for="title">用药核对 <font size="1">输入下一次核对日期（如果为当天则立即发送）</font></label>
                                        <label class="" for="title"></label>
                                        <div class="">
                                            <input class="form-control calendar" type="text" id="next_check_time" name="next_check_time"
                                                   placeholder="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                                <button class="btn btn-sm btn-primary" type="button" id="medicine_check_submit" data-dismiss="modal">
                                    <i class="fa fa-check"></i>提交
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-header">
                    <div class="block-options">
                    </div>
                    <h3 class="block-title">业务开关</h3>
                </div>
                <div class="block-content">
                    <table class="table table-hover table-borderless">
                        <tbody>
                        <tr>
                            <td><font size="5">用药核对</font></td>
                            <td>最近一次提交<span class="label label-info"><?= $patient->getLastPatientMedicineCheckSubmitTime(); ?></span></td>
                            <?php if ($patient->is_medicine_check == 1) { ?>
                                <td>
                                    <?php
                                    $pmCheck = PatientMedicineCheckDao::getLastByPatientid($patient->id);
                                    $content = $pmCheck->getTypeStr() . '核对：' . $pmCheck->plan_send_date . '（' . $pmCheck->getStatusStr() . '）';
                                    ?>
                                    <span class="label label-info cursor-pointer" data-toggle="popover" title="" data-placement="top"
                                          data-content="<?= $content ?>" data-original-title="近期核对日期">进行中</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-minw btn-danger" id="close_medicine_check" type="button">停止</button>
                                </td>
                            <?php } else { ?>
                                <td><span class="label label-danger">已停止</span></td>
                                <td class="text-center">
                                    <button class="btn btn-minw btn-success" data-toggle="modal" data-target="#open_medicine_check" type="button">开启
                                    </button>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php
                        // 暂时先不隐藏了，都显示
                        //                            $items = PatientService::getCheckItemsByPatient($patient);
                        //                            if (count($items) > 0) {
                        ?>
                        <tr>
                            <td><font size="5">不良反应监测</font></td>
                            <td>最近一次提交<span class="label label-info"><?= $patient->getLastPADRMonitorSubmitTime(); ?></span></td>
                            <?php if ($patient->is_adr_monitor == 1) { ?>
                                <td>
                                    <?php
                                    $padrmonitors = PADRMonitorDao::getLastPlanGroupListByPatientid($patient->id);
                                    $content = '';
                                    foreach ($padrmonitors as $padrmonitor) {
                                        $content .= $padrmonitor->getEnameStr() . ' - ' . $padrmonitor->getTypeStr() . '：' . $padrmonitor->plan_date . '（' . $padrmonitor->getStatusStr() . '）';
                                    }
                                    ?>
                                    <span class="label label-info cursor-pointer" data-toggle="popover" title="" data-placement="top"
                                          data-content="<?= $content ?>" data-original-title="近期不良反应监测">进行中</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-minw btn-danger" id="close_adr_monitor" type="button">停止</button>
                                </td>
                            <?php } else { ?>
                                <td><span class="label label-danger">已停止</span></td>
                                <td class="text-center">
                                    <button class="btn btn-minw btn-success" data-toggle="modal" data-target="#open_adr_monitor" type="button">开启
                                    </button>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php
                        //                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="block">
                <ul class="nav nav-tabs nav-tabs-alt">
                    <li class="active">
                        <a href="javascript:">应用药</a>
                    </li>
                    <li>
                        <a href="javascript:">历史医嘱用药</a>
                    </li>
                    <li>
                        <a href="javascript:">患者用药反馈</a>
                    </li>
                    <li>
                        <a href="javascript:">全部历史用药</a>
                    </li>
                </ul>
                <div class="block-content tab-content">
                    <div class="tab-pane active" id="btabs-alt-static-home">
                        <button class="btn btn-info btn-sm btn-add-standard-medicine pull-left" data-patientid="<?= $patient->id ?>"
                                data-toggle="modal" data-target="#modal-add-standard-medicine"><i class="fa fa-plus"></i> 医嘱用药
                        </button>
                        <?php if ($pcard->send_pmsheet_status) { ?>
                            <span class="fr send_pmsheet_yes text-success">已发送</span>
                            <span class="fr send_pmsheet_no none text-warning">未发送</span>
                        <?php } else { ?>
                            <span class="fr send_pmsheet_yes none text-success">已发送</span>
                            <span class="fr send_pmsheet_no text-warning">未发送</span>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="js-table-sections table table-hover">
                                <thead>
                                <tr>
                                    <th style="width:30px;"></th>
                                    <th>药名</th>
                                    <th>医嘱日期</th>
                                    <th>首次用药日期</th>
                                    <th>最新用药日期</th>
                                    <th>剂量</th>
                                    <th>频次</th>
                                    <th>状态</th>
                                    <th>创建者</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <?php foreach ($pmTargets as $pmTarget) { ?>
                                    <tbody class="js-table-sections-header">
                                    <tr>
                                        <td class="text-center"><i class="fa fa-angle-right"></i></td>
                                        <td><?= $pmTarget->medicine->name ?></td>
                                        <td><?= $pmTarget->getRecordDate() ?></td>
                                        <td><?= $pmTarget->getOldestDrugTime() ?></td>
                                        <td><?= $pmTarget->getNewestDrugTime() ?></td>
                                        <td>
                                            <?= $pmTarget->drug_dose ?>
                                            <i class="si si-info text-info" data-toggle="popover" data-placement="right"
                                               data-content="<?= $pmTarget->drug_change ?>" data-original-title="调药规则"></i>
                                        </td>
                                        <td>
                                            <?= $pmTarget->drug_frequency ?>
                                        </td>
                                        <td>
                                            <?php $drugStatus = $pmTarget->getNewestDrugStatus(); ?>
                                            <span class="<?= $colorArr[$drugStatus] ?> push-5-r">
                                    <!--<i class="<?= $iconArr[$drugStatus] ?>"></i>-->
                                                <?php
                                                $statusDesc = $pmTarget->getDrugStatusDesc($drugStatus);
                                                echo $statusDesc;
                                                ?>
                                    </span>
                                            <?php $newestPmsItem = $pmTarget->getNewestPmsItem(); ?>
                                            <?php if ($newestPmsItem && $newestPmsItem->auditremark) { ?>
                                                <i class="si si-info text-info" data-toggle="popover" data-placement="right"
                                                   data-content="<?= $newestPmsItem->auditremark ?>" data-original-title=""></i>
                                            <?php } ?>
                                        </td>
                                        <td><?= $pmTarget->getCreator() ?></td>
                                        <td>
                                            <button class="btn btn-default btn-xs btn-add-medicine" data-toggle="modal"
                                                    data-target="#modal-add-medicine" data-pmtargetid="<?= $pmTarget->id ?>"><i
                                                        class="fa fa-plus"></i> 实际用药
                                            </button>
                                            <button class="btn btn-warning btn-xs btn-stop-medicine" data-toggle="modal"
                                                    data-target="#modal-stop-medicine" data-pmtargetid="<?= $pmTarget->id ?>"><i
                                                        class="fa fa-circle-thin"></i> 停药
                                            </button>
                                            <button class="btn btn-danger btn-xs btn-delete-pmtarget" data-pmtargetid="<?= $pmTarget->id ?>"><i
                                                        class="fa fa-trash-o"></i> 删除
                                            </button>
                                            <button class="btn btn-default btn-xs btn-modify-medicine" data-toggle="modal"
                                                    data-target="#modal-modify-medicine" data-pmtargetid="<?= $pmTarget->id ?>"><i
                                                        class="fa fa-pencil"></i> 修改
                                            </button>
                                            <?php if ($pmTarget->auditremark) { ?>
                                                <button class="btn btn-info btn-xs" data-toggle="popover" data-placement="left"
                                                        data-content="<?= $pmTarget->auditremark ?>" data-original-title="运营备注"><i
                                                            class="fa fa-info-circle"></i> 运营备注
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tbody class="bg-gray-lighter">
                                    <?php if ($pmsitems = $pmTarget->getPMSheetItems()) { ?>
                                        <?php foreach ($pmsitems as $pmsitem) { ?>
                                            <tr>
                                                <td style="width:30px;">-</td>
                                                <td class="text-gray-dark"><?= $pmsitem->medicine->name ?></td>
                                                <td class="text-gray-dark"></td>
                                                <td class="text-gray-dark"></td>
                                                <td class="text-gray-dark"><?= $pmsitem->getDrugDate() ?></td>
                                                <td class="text-gray-dark">
                                                    <?= $pmsitem->drug_dose ?>
                                                </td>
                                                <td class="text-gray-dark">
                                                    <?= $pmsitem->drug_frequency ?>
                                                </td>
                                                <td>
                                                    <span class="<?php echo $colorArr2[$pmsitem->status]; ?> push-5-r"> <?= $pmsitem->getStatusDesc() ?></span>
                                                    <?php if ($pmsitem->auditremark) { ?>
                                                        <i class="fa fa-info-circle text-gray-dark" data-toggle="popover" data-placement="right"
                                                           data-content="<?= $pmsitem->auditremark ?>" data-original-title=""></i>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-gray-dark"> <?= $pmsitem->getCreator() ?> </td>
                                                <td>
                                                    <?php if ($pmsitem->createby == 'Auditor') { ?>
                                                        <button data-pmsitemid="<?= $pmsitem->id ?>" class="btn-delete-pmsitem btn btn-danger btn-xs">
                                                            <i class="fa fa-trash-o"></i> 删除
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <td class="text-center" colspan="9">暂无历史用药数据</td>
                                    <?php } ?>
                                    </tbody>
                                <?php } ?>
                            </table>
                        </div>
                        <!--end of responsive-->
                        <div class="push-10">
                            <a href="javascript:" id="sendmsg" data-patientid="<?= $patient->id ?>" class="btn btn-success btn-sm"><i
                                        class="fa fa-share"></i> 发送给患者</a>
                            <a target="_blank" href="<?= $wx_uri ?>/patientmedicinesheet/one?openid=<?= $openid ?>" class="btn btn-success btn-sm"><i
                                        class="fa fa-eye"></i> 预览</a>
                        </div>
                    </div>
                    <div class="tab-pane" id="btabs-alt-static-profile">
                        <?php
                        foreach ($pmpkgs as $patientmedicinepkg) {
                            $patientmedicinepkgitems = array();
                            if ($patientmedicinepkg instanceof PatientMedicinePkg) {
                                $patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($patientmedicinepkg->id);
                            }
                            ?>
                            <div>
                                <h4 class="h4-title"><?= $patientmedicinepkg->revisitrecord->thedate ?></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>药名</th>
                                            <th>剂量</th>
                                            <th>频率</th>
                                            <th>调药方案</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($patientmedicinepkgitems as $a) {
                                            ?>
                                            <tr>
                                                <td><?= $a->medicine->name ?></td>
                                                <td><?= $a->drug_dose ?></td>
                                                <td><?= $a->getDrug_frequencyStr(); ?></td>
                                                <td><?= $a->drug_change ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane" id="btabs-alt-static-settings">
                        <div class="form-horizontal">
                            <?php
                            foreach ($pmsheets as $patientmedicinesheet) {
                                $patientmedicinesheetitems = PatientMedicineSheetItemDao::getListByPatientmedicinesheetid($patientmedicinesheet->id);
                                if (!$patientmedicinesheetitems && $patientmedicinesheet->content === '') {
                                    continue;
                                }
                                ?>
                                <h4 class="h4-title"><?= $patientmedicinesheet->thedate ?>
                                    <?php if ($patientmedicinesheet->auditstatus == 0) { ?>
                                        <span class="fr">
                                    <a class="auditwrong btn btn-danger btn-sm push-10-r" style="margin-top:-10px;"
                                       data-patientmedicinesheetid="<?= $patientmedicinesheet->id ?>"><i class="fa fa-close"></i> 错误</a>
                                    <a class="auditright btn btn-success btn-sm" style="margin-top: -10px;"
                                       data-patientmedicinesheetid="<?= $patientmedicinesheet->id ?>"><i class="fa fa-check"></i> 正确</a>
                                    <a class="pmsiModify-all btn btn-info btn-sm" style="margin-top: -10px;"
                                       data-patientmedicinesheetid="<?= $patientmedicinesheet->id ?>"><i class="fa fa-save"></i> 保存</a>
                                </span>
                                    <?php } else { ?>
                                        <span class="fr send_pmsheet_yes mt10 text-success font-s13 font-w500">
                                    <i class="fa fa-check-circle"></i> 已审核
                                    <a class="pmsiModify-all btn btn-info btn-sm push-10-l" style="margin-top: -10px;"
                                       data-patientmedicinesheetid="<?= $patientmedicinesheet->id ?>"><i class="fa fa-save"></i> 保存</a>
                                </span>
                                    <?php } ?>
                                </h4>
                                <div class="clearfix"></div>
                                <div class="table-responsive" id="pms-<?= $patientmedicinesheet->id ?>">
                                    <table class="table table-bordered table-striped ">
                                        <thead>
                                        <tr>
                                            <th>药名</th>
                                            <th>用药时间</th>
                                            <th>剂量</th>
                                            <th>频率</th>
                                            <th style="min-width:150px;">对错</th>
                                            <th>备注</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($patientmedicinesheetitems as $a) {
                                            ?>
                                            <tr data-pmsitemid="<?= $a->id ?>">
                                                <td><label class="control-label tali"><?= $a->medicine->name ?></label></td>
                                                <td><?= $a->getDrugDate() ?></td>
                                                <td>
                                                    <div class="col-md-6 remove-padding-l">
                                                        <input type="text" name="drug_dose" class="pmsi-drug_dose form-control"
                                                               value="<?= $a->drug_dose ?>"/>
                                                    </div>
                                                    <div class="col-md-6 remove-padding-l text-left">
                                                        <label class="control-label tali">医嘱：<?= $a->target_drug_dose; ?></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-md-6 remove-padding-l">
                                                        <input type="text" name="drug_frequency" class="pmsi-drug_frequency form-control"
                                                               value="<?= $a->drug_frequency ?>"/>
                                                    </div>
                                                    <div class="col-md-6 remove-padding-l text-left">
                                                        <label class="control-label tali">医嘱：<?= $a->target_drug_frequency; ?></label>
                                                    </div>
                                                </td>
                                                <td><?php echo HtmlCtr::getSelectCtrImp(PatientMedicineSheetItem::$statusDescArray, 'status', $a->status, 'pmsi-status form-control'); ?></td>
                                                <td><textarea class="form-control pmsi-auditremark"><?= $a->auditremark ?></textarea></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="bg-gray-lighter push-20 J_imageViewer" style="padding: 10px;">
                                    <p class="font-w600">患者反馈：</p>
                                    <?= $patientmedicinesheet->content ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="tab-pane" id="btabs-alt-static-home">
                        <button class="btn btn-info btn-sm btn-add-history-medicine" data-toggle="modal" data-target="#modal-add-history-medicine"
                                data-patientid="<?= $patient->id ?>" data-doctorid="<?= $patient->doctorid ?>"><i class="fa fa-plus"></i> 历史用药
                        </button>
                        <p></p>
                        <?php foreach ($allpmsitems as $medicineName => $pmsitems) { ?>
                            <p class="medicine-history-p"><span class="font-w600 text-info"><?= $medicineName ?></span><span
                                        class="pull-right push-20-r"><i class="fa fa-angle-down"></i></span></p>
                            <div class="table-div">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>药名</th>
                                        <th>创建日期</th>
                                        <th>用药日期</th>
                                        <th>剂量</th>
                                        <th>频次</th>
                                        <th>状态</th>
                                        <th>创建者</th>
                                        <th class="collapse">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($pmsitems as $pmsitem) { ?>
                                        <tr>
                                            <td><?= $pmsitem->medicine->name ?></td>
                                            <td><?= $pmsitem->getCreateDay() ?></td>
                                            <td><?= $pmsitem->getDrugDate() ?></td>
                                            <td>
                                                <?= $pmsitem->drug_dose ?>
                                            </td>
                                            <td>
                                                <?= $pmsitem->drug_frequency ?>
                                            </td>
                                            <td>
                                                <span class="<?php echo $colorArr2[$pmsitem->status]; ?> push-5-r"> <?= $pmsitem->getStatusDesc() ?></span>
                                                <?php if ($pmsitem->auditremark) { ?>
                                                    <i class="fa fa-info-circle text-gray-dark" data-toggle="popover" data-placement="right"
                                                       data-content="<?= $pmsitem->auditremark ?>" data-original-title=""></i>
                                                <?php } ?>
                                            </td>
                                            <td> <?= $pmsitem->getCreator() ?> </td>
                                            <td class="collapse">
                                                <?php if ($pmsitem->createby == 'Auditor') { ?>
                                                    <button data-pmsitemid="<?= $pmsitem->id ?>" class="btn-delete-pmsitem btn btn-danger btn-xs"><i
                                                                class="fa fa-trash-o"></i> 删除
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                    <!--end of tab-pane -->
                </div>
            </div>
        </section>
    </div>
</div>
<div class="modal fade" id="modal-add-standard-medicine" tabindex="-1" role="dialog" aria-labelledby="drugItemAddLabel" aria-hidden="true"></div>
<div class="modal fade" id="modal-add-medicine" tabindex="-1" role="dialog" aria-labelledby="drugItemAddLabel" aria-hidden="true"></div>
<div class="modal fade" id="modal-stop-medicine" tabindex="-1" role="dialog" aria-labelledby="drugStopLabel" aria-hidden="true"></div>
<div class="modal fade" id="modal-modify-medicine" tabindex="-1" role="dialog" aria-labelledby="drugStopLabel" aria-hidden="true"></div>
<div class="modal fade" id="modal-add-history-medicine" tabindex="-1" role="dialog" aria-labelledby="drugItemAddLabel" aria-hidden="true"></div>
<?php
$footerScript = <<<STYLE
    $(function(){
        $('.J_imageViewer').viewer({
            inline: false,
            url: 'data-url',
            class: 'viewer-toggle',
            navbar: false,
            scalable: false,
            fullscreen: false,
            shown: function (e) {
            }
        })
        $('.J_imageViewer').viewer('update');    
    
        App.initHelper('table-tools');
        $(document).off("click", ".nav-tabs>li").on("click", ".nav-tabs>li", function() {
            var me = $(this);
            var index = me.index();
            var tab = me.parent().parent();
            var contents = tab.children(".tab-content").children(".tab-pane");
            me.addClass("active").siblings().removeClass("active");
            contents.eq(index).show().siblings().hide();
        });

        $("#adr_monitor_submit").on("click", function(){
            var flag = 0;
            $(".checkdates").each(function(){
                if ($(this).val() == '') {
                    flag = 1;
                    return;
                }
            });

            if (flag == 1) {
                alert("全为必填项，请填写日期");
                return false;
            } else {
                $("#submit_monitor").submit();
            }
        });

        $("#close_adr_monitor").on("click", function(){
            var patientid = $("#patientid").val();

            if (! confirm("确认关闭不良反应监测吗?")) {
                return false;
            }

            $.ajax({
                type: "post",
                url: "/patientmedicinetargetmgr/CloseAdr_monitorJson",
                data: {
                    patientid: patientid,
                },
                dataType: "text",
                success: function (d) {
                    window.location.href = window.location.href;
                }
            });
        });

        $("#medicine_check_submit").on("click", function(){
            var patientid = $("#patientid").val();
            var next_check_time = $('#next_check_time').val();
            var flag = 0;

            if (next_check_time == '') {
                alert("下次核对用药日期必填");
                flag = 1;
            }

            if (flag == 1) {
                return false;
            } else {
                $("#submit_medicine_check").submit();
            }
        });

        $("#close_medicine_check").on("click", function(){
            var patientid = $("#patientid").val();

            if (! confirm("确认关闭用药核对吗?")) {
                return false;
            }

            $.ajax({
                type: "post",
                url: "/patientmedicinetargetmgr/CloseMedicine_checkJson",
                data: {
                    patientid: patientid,
                },
                dataType: "text",
                success: function (d) {
                    window.location.href = window.location.href;
                }
            });
        });

        $(".pmsiModify-all").on("click",function(){
            if(! confirm("您都仔细核对过用药了吗？")){
                return;
            }
            var patientmedicinesheetid = $(this).data("patientmedicinesheetid");
            var me = $(this);
            var data = [];
            $('#pms-' + patientmedicinesheetid + ' tbody tr').each(function() {
                var me_tr = $(this);
                var obj = {
                    "drug_dose": me_tr.find(".pmsi-drug_dose").val(),
                    "drug_frequency": me_tr.find(".pmsi-drug_frequency").val(),
                    "auditremark": me_tr.find(".pmsi-auditremark").val(),
                    "status": me_tr.find(".pmsi-status").val(),
                    "patientmedicinesheetitemid": me_tr.data("pmsitemid")
                }
                data.push(obj);
            });

            $.ajax({
                type: "post",
                url: "/patientmedicinesheetmgr/savealljson",
                data: {
                    patientmedicinesheetid: patientmedicinesheetid,
                    data: data
                },
                dataType: "text",
                success: function (d) {
                    if (d == 'ok') {
                        alert('保存成功');
                    } else {
                        alert(d);
                    }
                }
            })
        });

        $("#sendmsg").on("click",function(){
            if(! confirm("确定发送？")){
                return;
            }
            var patientid = $(this).data("patientid");

            $.ajax({
                "type" : "get",
                "data" : {
                    patientid : patientid
                },
                "url" : "/patientmedicinesheetmgr/sendmsgJson",
                "dataType" : "text",
                "success" : function(data){
                    if(data == 'success'){
                        alert("发送成功");
                        $(".send_pmsheet_yes").show();
                        $(".send_pmsheet_no").hide();
                    } else {
                        alert(data);
                    }
                }
            });
        });

        $(".auditright").on("click",function(){
            if(! confirm("您都仔细核对过用药了吗？")){
                return;
            }
            var patientmedicinesheetid = $(this).data("patientmedicinesheetid");
            var me = $(this);
            $.ajax({
                "type" : "get",
                "data" : {
                    patientmedicinesheetid : patientmedicinesheetid
                },
                "url" : "/patientmedicinesheetmgr/auditrightJson",
                "dataType" : "text",
                "success" : function(data){
                    if(data == 'success'){
                        alert("审核完毕");
                        me.hide();
                        me.prev().hide();
                    }
                }
            });
        });

        $(".auditwrong").on("click",function(){
            if(! confirm("您都仔细核对过用药了吗？")){
                return;
            }
            var patientmedicinesheetid = $(this).data("patientmedicinesheetid");
            var me = $(this);
            $.ajax({
                "type" : "get",
                "data" : {
                    patientmedicinesheetid : patientmedicinesheetid
                },
                "url" : "/patientmedicinesheetmgr/auditwrongJson",
                "dataType" : "text",
                "success" : function(data){
                    if(data == 'success'){
                        alert("审核完毕");
                        me.hide();
                        me.next().hide();
                    }
                }
            });
        });

        //历史用药展开与收起
        $('.medicine-history-p').on('click', function() {
            $(this).next('div').animate({height: 'toggle', opacity: 'toggle'}, 200);
            var fa = $(this).find('i.fa');
            if (fa.attr('class').indexOf('fa-angle-down') > -1) {
                fa.removeClass('fa-angle-down').addClass('fa-angle-right');
            } else if (fa.attr('class').indexOf('fa-angle-right') > -1) {
                fa.removeClass('fa-angle-right').addClass('fa-angle-down');
            }
        });

    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
