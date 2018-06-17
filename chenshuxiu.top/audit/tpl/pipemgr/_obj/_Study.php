<?php $study = $a->obj; ?>
<?php if($study instanceof Study){ ?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
<?php if ($study->xanswersheet instanceof XAnswerSheet) { ?>
<?php
    foreach ($study->xanswersheet->getAnswers() as $xanswer) {
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
<?php } ?>
<?php $study = null; ?>
