<div class="optaskOneShell">
    <?php
    $studyplan = $optask->obj;
    $studys = array();
    if ($studyplan instanceof StudyPlan) {
        $patientpgroupref = $studyplan->patientpgroupref;
        $studys = StudyDao::getListByStudyplanid($studyplan->id);
    }
    ?>
    <div style="margin-top: 10px;">
        <?php foreach ($studys as $a) { ?>
            <div style="margin-bottom: 25px;">
                <?php
                foreach ($a->xanswersheet->getAnswers() as $xanswer) {
                    echo $xanswer->getQuestionCtr()->getQaHtml4lesson();
                }
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="replyBox replyShell">
        <input type="hidden" name="patientpgrouprefid" class="patientpgrouprefid" value="<?= $patientpgroupref->id ?>"/>
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
        <textarea class="reply-msg push-10-t"></textarea>
        <p>
            <span class="btn btn-default reply-studyplanbtn">回复</span>
        </p>
    </div>
</div>
