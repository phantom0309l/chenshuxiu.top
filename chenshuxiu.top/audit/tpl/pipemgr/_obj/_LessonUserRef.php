<?php $lessonUserRef = $a->obj; ?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
<?php
if ($lessonUserRef->hasHwkAnswerSheet()) {
    foreach ($lessonUserRef->getHwkAnswerSheet()->getAnswers() as $c) {
        if ($c->xquestion->isFbtHwkJinbuQuestion()) {
            echo $c->getQuestionCtr()->getQaHtml4lesson();
        }
    }
}
?>
        <button class="TriggerBtn btn btn-default btn-sm">展开答卷</button>
        <a target="_blank" href="/lessonuserrefmgr/one?lessonuserrefid=<?= $lessonUserRef->id ?>"> 新页面查看答卷 </a>
    </div>
    <div class="TriggerContent colorBox colorBox-paper none" id="lessonUserRef_<?=$lessonUserRef->id?>">
        <h5 class="refsubtitle">课堂巩固</h5>
<?php
if ($lessonUserRef->hasTestAnswerSheet()) {
    foreach ($lessonUserRef->getTestAnswerSheet()->getAnswers() as $xanswer) {
        echo $xanswer->getQuestionCtr()->getQaHtml4lesson();
    }
} else {
    echo '<p>暂时没写</p>';
}
?>
        <br />
        <h5 class="refsubtitle">课堂作业</h5>

<?php
if ($lessonUserRef->hasHwkAnswerSheet()) {
    ?>
<?php

    foreach ($lessonUserRef->getHwkAnswerSheet()->getAnswers() as $xanswer) {
        if (false == $xanswer->isDefaultHide()) {
            echo $xanswer->getQuestionCtr()->getQaHtml4lesson();
        }
    }
} else {
    echo '<p>暂时没写</p>';
}
?>
    </div>
</div>
<?php $lessonUserRef = null; ?>
