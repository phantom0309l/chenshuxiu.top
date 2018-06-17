<div class="optaskOneShell">
    <?php
    $patient = $optask->patient;
    $optaskpiperefs = OpTaskPipeRefDao::getListByOptaskid($optask->id);
    $pagetitle = $patient->name . "评估任务详情";
    include $tpl . "/_pagetitle.php";
    ?>
    <div class="patientworkShell">
        <div class="past-list">
            <p><?= substr($optask->createtime, 0, 10) ?></p>
            <div class="tab">
                <ul class="tab-menu">
                    <?php
                    foreach ($optaskpiperefs as $i => $_a) {
                        $a = $_a->pipe;
                        ?>
                        <li class="<?= $i == 0 ? 'active' : '' ?>"><?= $a->obj->papertpl->title ?></li>
                    <?php } ?>
                </ul>
                <div class="tab-content" style="margin-top: 15px">
                    <?php
                    foreach ($optaskpiperefs as $i => $_a) {
                        $a = $_a->pipe;
                        ?>
                        <div class="tab-content-item  <?= $i > 0 ? 'none' : '' ?>">
                            <?php include $tpl . "/pipemgr/_obj/_{$a->objtype}.php"; ?>
                            <div class="replyBox replyShell">
                                <div class="col-md-6 remove-padding">
                                    <?php
                                    $diseaseGroup = $patient->getDiseaseGroup();
                                    $dealwithTpls = [];
                                    echo HtmlCtr::getSelectCtrImp(DealwithTplService::getCtrArrayForPatient($diseaseGroup->id), "dealwith_group", "noselect",
                                        "dealwith_group form-control js-select2", 'width: 100%;');
                                    ?>
                                </div>
                                <div class="col-md-6 remove-padding-r">
                                    <select class="handleSelect dealwithTplSelect form-control js-select2 clear" style="width: 100%">
                                        <?php if (count($dealwithTpls) > 0) { ?>
                                            <option value="">请选择....</option>
                                        <?php } ?>
                                        <?php foreach ($dealwithTpls as $c) { ?>
                                            <option value="<?= $c->id ?>" data-msgcontent="<?= $c->msgcontent ?>"><?= $c->title ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <textarea rows="5" class="reply-msg push-10-t" style="width: 100%"></textarea>
                                <p>
                                    <span class="btn btn-default reply-paperbtn" data-optaskid="<?= $optask->id ?>">回复</span>
                                    <input type="checkbox" class="noteBtn"/>
                                    <span>添加至作业批注</span>
                                </p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
