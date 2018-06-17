<?php $xanswersheet = $a->obj; ?>
    <div class="TriggerBox">
        <div class="grayBgColorBox">
            <?php
                foreach ($xanswersheet->getAnswers() as $c) {
                    if ($c->xquestion->isFbtHwkJinbuQuestion()) {
                        echo $c->getQuestionCtr()->getQaHtml4lesson();
                    }
                }
            ?>
            <?php
            if($xanswersheet->xquestionsheet->isOfGantong()){
                $course = Course::getById(119289565);
                ?>
                <div>
                    <span class="red">感统训练(第<?=$xanswersheet->obj->lesson->getPosInCourse($course)?>课　第 <?= $xanswersheet->getCnt(" and id <= {$xanswersheet->id} ") ?> 次提交)</span>
                </div>
                <?php
            }
            ?>
            <button class="TriggerBtn btn btn-default btn-sm">展开答卷</button>
            <a target="_blank" href="/xanswersheetmgr/modify?xanswersheetid=<?= $xanswersheet->id ?>"> 新页面查看答卷 </a>
        </div>
        <div class="TriggerContent colorBox none" id="lessonUserRef_<?= $xanswersheet->id ?>">
            <h5 class="refsubtitle">答卷明细(得分：<?= $xanswersheet->score ?>)</h5>
            <?php
                foreach ($xanswersheet->getAnswers() as $xanswer) {
                    if (false == $xanswer->isDefaultHide()) {
                        echo $xanswer->getQuestionCtr()->getQaHtml4lesson();
                    }
                }
            ?>
        </div>
    </div>
<?php $xanswersheet = null; ?>
