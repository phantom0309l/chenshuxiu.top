<!-- 运营备注 begin-->
<div class="remarkBox push-10-t">
    <?php if ($patient->disease->isInDiseaseGroupOfADHD()) { ?>
        <div class="remarkBox-ADHD">
            <div class="block">
                <ul class="nav nav-tabs nav-tabs-alt nav-justified" data-toggle="tabs">
                    <li class="active remarkBox-ADHD-listTriggerBtn">
                        <a href="#">
                            <i class="fa fa-align-justify"></i>
                            <span class="push-5-l">总览</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-pencil"></i>
                            <span class="push-5-l">添加</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-question-circle"></i>
                            <span class="push-5-l">其它</span>
                        </a>
                    </li>
                </ul>

                <div class="block-content tab-content" style="padding:10px 0px 0px">
                    <div class="tab-pane active patientRecordListOfADHD" id="btabs-alt-static-justified-home">
                    </div>
                    <div class="tab-pane" id="btabs-alt-static-justified-profile">
                        <?php
                        $diseaseGroup = $patient->disease->diseasegroup;
                        $patientRecordTpls = PatientRecordTplDao::getIsShowListByDiseaseGroup($diseaseGroup);
                        ?>
                        <div class="patientRecordAddBox">
                            <?php foreach ($patientRecordTpls as $a) { ?>
                                <div class="form-group patientRecordAddBox-item"
                                     data-patientrecordtplid="<?= $a->id ?>">
                                    <div>
                                        <div class="form-material form-material-warning push-30-t">
                                            <input class="form-control" type="text" name="" placeholder="">
                                            <label class="text-muted"><?= $a->title ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="text-danger text-right patientRecordAddNotice push-15-r none"
                                 style="margin-bottom:15px;">已添加备注
                            </div>
                            <div class="clearfix">
                                <button class="btn btn-minw btn-rounded btn-warning pull-right patientRecordAddBox-btn"
                                        type="button" data-patientid="<?= $patient->id ?>">提交
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="btabs-alt-static-justified-settings">
                        <textarea class="form-control remarkBox-ta" rows="6"><?= $patient->opsremark ?></textarea>
                        <div class="remarkBox-notice text-danger text-right push-15-r push-10-t none">已备注</div>
                        <div class="push-20-t clearfix">
                            <a class="btn btn-danger btn-minw btn-rounded remarkBox-btn pull-right">运营备注</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="form-group" style="margin-left: -15px; margin-right: -15px; margin-bottom: 5px;">
            <div class="col-xs-12">
                <textarea class="form-control remarkBox-ta" rows="6"><?= $patient->opsremark ?></textarea>
            </div>
            <div class="clear"></div>
        </div>
        <div class="remarkBox-notice red none">已备注</div>
        <div class="form-group" style="margin-left: -15px; margin-right: -15px;">
            <div class="col-xs-12">
                <a class="btn btn-danger btn-sm remarkBox-btn">运营备注</a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clearfix"></div>
        <?php $patientrecords = PatientRecordDao::getListByPatientid($patient->id); ?>
        <div class="form-control" style="height:200px;overflow:scroll">
            <?php foreach ($patientrecords as $a) { ?>
                <div style="margin-bottom: 5px;">
                    <p class="remove-margin"><?= PatientRecordHelper::getShortDesc($a) ?></p>
                    <?php
                    $children = $a->getChildren();
                    if (!empty($children)) {
                        foreach ($children as $child) { ?>
                            <p class="remove-margin">*** <?= PatientRecordHelper::getShortDesc($child) ?></p>
                        <?php }
                    } ?>
                </div>
            <?php } ?>
        </div>
        <?php include_once $tpl . "/patientrecordmgr/listrecord.php"; ?>
    <?php } ?>
</div>
<!-- 运营备注 end-->
