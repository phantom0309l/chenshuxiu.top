<div class="patientRecordTplBox">
    <label data-patientrecordtplid="0" data-patientid="<?= $patient->id ?>" class="css-input css-radio css-radio-sm css-radio-success push-10-r patientRecordTplBox-triggerBtn">
        <input type="radio" name="radio-group4" <?= 0 == $patientrecordtplid ? 'checked' : '' ?>><span></span> 全部
    </label>
    <?php
        $patientRecordTpl_cnt = count($patientRecordTpls);
        foreach($patientRecordTpls as $i => $a){
    ?>
        <label data-patientrecordtplid="<?= $a->id ?>" data-patientid="<?= $patient->id ?>" class="patientRecordTplBox-triggerBtn css-input css-radio css-radio-sm css-radio-success <?= $i + 1 == $patientRecordTpl_cnt ? '' : 'push-10-r'?>">
            <input type="radio" name="radio-group4" <?= $a->id == $patientrecordtplid ? 'checked' : '' ?>><span></span> <?= $a->title ?>
        </label>
    <?php } ?>
</div>

<div class="push-10-t" style="max-height:520px; overflow:auto;">
    <div class="block block-themed" style="margin-bottom:0px;">
        <div class="" style="border-top:1px dashed #5c90d2;"></div>
        <div class="block-content" style="padding:40px 0px 0px">
            <ul class="list list-timeline pull-t">
                <?php foreach($patientRecords as $a){ ?>
                    <li>
                        <div class="list-timeline-time"><?= $a->thedate ?><br/><?= $a->create_auditor->name ?></div>
                        <i class="<?= $a->patientrecordtpl->style_class ?> list-timeline-icon"></i>
                        <div class="list-timeline-content">
                            <p class="font-w600"><?= $a->patientrecordtpl->title ?></p>
                            <div class="font-s13 patientRecordContent push-10-t">
                                <div class="patientRecordContent-show"><?= $a->content ?><i class="fa fa-edit patientRecordEdit push-5-l text-muted"></i></div>
                                <div class="patientRecordContent-edit none">
                                    <textarea class="form-control" rows="6"><?= $a->content ?></textarea>
                                    <div class="text-right push-5-t"><button data-patientrecordid="<?= $a->id ?>" class="btn btn-default btn-sm patientRecordSave"><i class="fa fa-save"></i> <span>保存</span></button></div>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
