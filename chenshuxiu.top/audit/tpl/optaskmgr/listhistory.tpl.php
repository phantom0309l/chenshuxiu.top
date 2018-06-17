<?php
$pagetitle = '历史任务';
$cssFiles = [
    $img_uri . '/v5/page/audit/optaskmgr/list/list.css?v=20180128',
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170820',
];
$jsFiles = []; //填写完整地址
$jsFiles[] = $img_uri . '/v3/js/amr/amrnb.js';
$jsFiles[] = $img_uri . '/v3/js/amr/amritem.js';
$jsFiles[] = $img_uri . '/v5/page/audit/optaskmgr/list/pipe.js?v=2018050901';
$jsFiles[] = $img_uri . '/v5/common/wxvoicemsg_content_modify.js?v=20171208';
$jsFiles[] = $img_uri . '/v5/common/dealwithtpl.js?v=2018050401';
$jsFiles[] = $img_uri . "/v5/common/pipelevelfix.js?v=20171222";
$jsFiles[] = $img_uri . '/v5/page/audit/optaskmgr/list/listnew.js?v=2018060701';
$jsFiles[] = $img_uri . '/v5/page/audit/optaskmgr/list/pgroup.js?v=20171206';
$jsFiles[] = $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js";
$jsFiles[] = $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170820';
$sideBarMini = true;
$pageStyle = <<<STYLE
    .content-left label.control-label {
        padding-right: 0;
        font-weight: 500;
    }
    #main-container {
     background: #f5f5f5 !important;
    }

    .divOnSelected {
        border: 1px solid #5c90d2;
    }
    .block-title-user {
        margin-left: -10px;
        margin-right: 10px;
    }

    .block-content .optask:last-child .optask-t {
        border-bottom: 0;
    }
    .float_l {
        float: left;
    }
    .float_r {
        float: right;
    }
    .padding-l-r {
        padding-left:5%;
        padding-right:5%;
    }
    .margin-t-remove {
        margin-top: 0px;
    }
    .padding-t-remove {
        padding-top: 0px;
    }
    .remove_font_weight {
        font-weight: normal!important;
    }
STYLE;
?>
<?php include_once dirname(__FILE__) . '/../_header.new.tpl.php'; ?>
<input type="hidden" id="patientid"/>
<div class="col-md-12 contentShell">
        <section class="col-md-3 content-left sectionItem">
            <div class="bg-white" style="padding:10px;margin-bottom:10px;">
                <form action="/optaskmgr/listhistory" method="get" class="form form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-md-3">患者姓名</label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" name="patient_name" value="<?= $patient_name ?>" placeholder="优先查询条件"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">运营</label>
                        <div class="col-md-9">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getYunyingAuditorCtrArray(),"auditorid_yunying",$auditorid_yunying, "form-control js-select2"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">优先级</label>
                        <div class="col-md-9">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getOptaskLevelCtrArray(true),"level",$level, "form-control js-select2"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">任务状态</label>
                        <div class="col-md-9">
                        <?= HtmlCtr::getRadioCtrImp4OneUi([0 => '只显示关闭', 1=> '显示全部'],"show_open_task",$show_open_task, "css-radio-warning"); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">任务关闭日期</label>
                        <div class="col-md-4">
                        <input type="text" class="calendar form-control"  name="donedate_from" value="<?= $donedate_from ?>"/>
                        </div>
                        <div class="col-md-1 remove-padding">
                            <label class="control-label">至</label>
                        </div>
                        <div class="col-md-4 remove-padding-l">
                        <input type="text" class="calendar form-control" name="donedate_to" value="<?= $donedate_to ?>"/>
                        </div>
                    </div>

                    <div class="form-group remove-margin-b padding-l-r">
                        <div class="float_l">
                            <input type="checkbox" name="isRandom" id="isRandom" value="1" <?= $isRandom==1?"checked":" "?>>
                            <label for="isRandom" class="remove_font_weight">随机显示10条</label>
                        </div>
                        <div class="float_r">
                            <input class="btn btn-success btn-sm btn-minw" type="submit" value="搜索" />
                        </div>
                    </div>
                </form>
            </div>

                <?php
                    foreach ($optasks as $a) {
                        $patient = $a->patient;
                ?>
                        <div class="block task-block">
                            <div class="block-header" <?php if($a->level > 3) { ?>style="background:<?=$a->getLevelColor()?>"<?php }?>>
                                <ul class="block-options">
                                    <?php if($a->level == 4) { ?>
                                    <li><span class="text-danger">紧急</span></li>
                                    <?php } else if ($a->level == 5) { ?>
                                    <li><span style="color:#ff0000">立刻</span></li>
                                    <?php } ?>
                                    <li>
                                    <a href="#goPatientBase" data-patientname="<?= $patient->name ?>" data-patientid="<?= $patient->id ?>" data-diseaseid="<?= $pcard->diseaseid ?>" data-doctorid="<?= $pcard->doctorid ?>" data-optaskid="<?= $a->id ?>" class="showOptask showHistory showPatientOneHtml patientid-<?= $patient->id ?>" style="color:#70b9eb;opacity:1" >查看</a>
                                    </li>
                                </ul>
                                <h3 class="block-title">
                                    <?php
                                        if ($patient instanceof Patient) {
                                            $female = $patient->sex == 2 ? '-female' : '';
                                            $color = '';
                                            //$color = $patient->sex == 2 ? 'text-smooth-light' : 'text-modern-light';
                                            echo '<i class="si si-user' . $female . ' block-title-user ' .$color. '"></i> ';
                                            echo $patient->getMaskName();
                                            $showtitle = $a->optasktpl->title;
                                            ?>
                                            <span class="push-10-l font-s12 text-success"><?=$showtitle?></span>
                                            <?php
                                        } else {
                                            echo "患者不存在";
                                        }
                                    ?>
                                </h3>
                            </div>
                            <div class="block-content">
                                <p>运营：<?=$a->auditor->name?></p>
                                <p>关闭时间：<?= $a->getFixDonetime() ?></p>
                            </div>
                        </div>
                <?php } ?>
        <?php if($isRandom != 1)include $dtpl . "/pagelink.ctr.php"; ?>
        </section>
        <section class="col-md-5 content-right sectionItem">
            <?php include_once $tpl . "/_pipelayout_optask.php"; ?>
        </section>
        <section class="col-md-4 sectionItem" id="oneHtml">
        <div class="optaskshell">
            <div id="oneHistoryPatientHtml"></div>
            <div class="block block-bordered push-10-t">
                <div class="block-header bg-gray-lighter">
                    <span class="">所有任务</span>
                </div>
                <div id="optaskhistoryshell" class="block-content remove-padding onePatientHtml">
                </div>
                <div class="showMoreShell text-center">
                    <span class="btn btn-default AP showMore push-10-t push-10" id="showMoreHistory"><i class="fa fa-angle-double-down"></i> 查看更多</span>
                </div>
            </div>
        </div>
        </section>
    </div>
    <div class="clear"></div>
    <?php include_once($tpl . "/_thankbox.php"); ?>
    <?php include_once($tpl . "/_pipelevelfixbox.php"); ?>
    <?php include_once($tpl . "/optaskmgr/_pipe_bind_optask.php"); ?>
<?php
$footerScript = <<<SCRIPT
$(function(){
    App.initHelper('select2');
});
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
