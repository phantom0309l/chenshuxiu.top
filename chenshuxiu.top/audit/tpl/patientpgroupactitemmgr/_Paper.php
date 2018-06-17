<?php
$paper = $a->obj;
?>
<div class="actItem-t">
    <span class="actItem-title"><?= $paper->papertpl->title ?></span>
    <span class="actItem-time"><?= $a->createtime ?></span>
</div>
<div class="actItem-c <?= $i > 0 ? 'none' : ''?>">
    <div class="TriggerBox">
    <?php
    if ($paper->hasAnswerSheet()) {
        ?>
    <?php
        foreach ($paper->getAnswerSheet()->getAnswers() as $xanswer) {
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
<?php
$paper = $null;
?>
