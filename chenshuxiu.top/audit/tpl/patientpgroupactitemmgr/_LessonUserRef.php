<?php
$lessonuserref = $a->obj;
$title = $lessonuserref->lesson->title;
?>
<div class="actItem-t">
    <span class="actItem-title" title="<?= $title ?>">
        <?php if(mb_strlen($title) > 16){?>
            <?= mb_substr($title, 0, 16).'...' ?>
        <?php }else{?>
            <?= $title ?>
        <?php } ?>
    </span>
    <span class="red">
        <?php if($a->isok == 1){ ?>
            (符合)
        <?php }elseif($a->isok == 2){ ?>
            (不符合)
        <?php } ?>
    </span>
    <span class="actItem-time"><?= $a->createtime ?></span>
</div>
<div class="actItem-c <?= $i > 0 ? 'none' : ''?>">
    <div class="tab">
        <ul class="tab-menu">
            <li class="active">作业</li>
            <li>巩固</li>
            <li>课文</li>
        </ul>
        <div class="tab-content">
            <div class="tab-content-item">
<?php
if ($lessonuserref->hasHwkAnswerSheet()) {
    foreach ($lessonuserref->getHwkAnswerSheet()->getAnswers() as $xanswer) {
        if (false == $xanswer->isDefaultHide()) {
            echo $xanswer->getQuestionCtr()->getQaHtml4lesson();
        }
    }
} else {
    echo '<p>暂时没写</p>';
}
?>
            </div>
            <div class="tab-content-item none">
<?php
if ($lessonuserref->hasTestAnswerSheet()) {
    foreach ($lessonuserref->getTestAnswerSheet()->getAnswers() as $xanswer) {
        echo $xanswer->getQuestionCtr()->getQaHtml4lesson();
    }
} else {
    echo '<p>暂时没写</p>';
}
?>
            </div>
            <div class="tab-content-item none">
                <p class="mainp"><?= $lessonuserref->lesson->brief ?></p>
                <?= $lessonuserref->lesson->content?>
            </div>
        </div>
    </div>
</div>
<div class="okBtnShell <?= $i > 0 ? 'none' : '' ?>">
    <span class="btn btn-default isokBtn <?= $a->isok == 1 ? "btn-primary" : "" ?>" data-isok="1">
        符合
    </span>
    <span class="btn btn-default isokBtn <?= $a->isok == 2 ? "btn-primary" : "" ?>" data-isok="2">
        不符合
    </span>
</div>
<?php
$lessonuserref = $null;
?>
