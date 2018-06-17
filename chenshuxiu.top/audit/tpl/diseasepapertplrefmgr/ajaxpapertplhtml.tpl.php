<?php
$show_in_wx = 1;
$show_in_audit = 1;
if ($diseasepapertplref instanceof DiseasePaperTplRef) {
    $show_in_wx = $diseasepapertplref->show_in_wx;
    $show_in_audit = $diseasepapertplref->show_in_audit;
}
?>
<form class="form-horizontal" action="/diseasepapertplrefmgr/addofdoctorpost" method="post">
    <input type="hidden" name="papertplid" value="<?= $papertpl->id ?>">
    <input type="hidden" name="doctorid" value="<?= $doctor->id ?>">
    <input type="hidden" name="diseaseid" value="<?= $disease->id ?>">
    <div class="block block-bordered" id="tpl_block">
        <div class="block-header bg-gray-lighter">
            <ul class="block-options">
                <li>
                    <label class="css-input css-checkbox css-checkbox-primary remove-margin">
                        <input type="checkbox"
                               name="show_in_wx" <?= $show_in_wx ? 'checked' : '' ?>><span></span>患者可见
                    </label>
                </li>
                <li>
                    <label class="css-input css-checkbox css-checkbox-primary remove-margin">
                        <input type="checkbox"
                               name="show_in_audit" <?= $show_in_audit ? 'checked' : '' ?>><span></span>运营可见
                    </label>
                </li>
            </ul>
            <h3 class="block-title"><?= $papertpl->title ?></h3>
        </div>
        <div class="block-content scroll-y">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <th>问题</th>
                    <th class="tc" style="width: 130px;">类型</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $questions = [];
                if ($xquestionsheet instanceof XQuestionSheet) {
                    $questions = $xquestionsheet->getQuestions();
                }
                foreach ($questions as $key => $question) {
                    $index = $key + 1
                    ?>
                    <tr>
                        <td><?= $question->getTitle() ?></td>
                        <td class="tc" style="width: 130px;"><?= $question->getTypeDesc() ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        <?php if (false == $diseasepapertplref instanceof DiseasePaperTplRef) { ?>
            <div class="block-footer tc">
                <button class="btn btn-minw btn-success J_submit" type="submit">保存</button>
            </div>
        <? } ?>
    </div>
</form>
