<?php
$lessonuserref = $a->obj;
?>
<div class="actItem-t">
    <span class="actItem-title"><?= $lessonuserref->lesson->title ?></span>
    <span class="actItem-time"><?= $lessonuserref->createtime ?></span>
</div>
<div class="actItem-c <?= $i > 0 ? 'none' : ''?>">
    <div class="tab">
        <ul class="tab-menu">
            <li class="active">作业</li>
            <li>批注</li>
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
$comments = CommentDao::getListByObjtypeObjidTypestr("LessonUserRef", $lessonuserref->id, "auditorNote");
?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>时间</td>
                            <td>内容</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment) { ?>
                        <tr>
                            <td><?= $comment->createtime ?></td>
                            <td><?= $comment->content ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
<?php
$lessonuserref = $null;
?>
