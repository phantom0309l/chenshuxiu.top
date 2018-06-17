<?php
$ename = $paper->ename;

if ($ename == "adhd_iv") {
    $adhddata = PaperService::getAdhd_ivData($paper);
    $count = $adhddata["count"];
    $cell1 = $count[0]['无'] + $count[0]['偶尔'];
    $cell2 = $count[0]['常常'] + $count[0]['总是'];
    $cell3 = $count[1]['无'] + $count[1]['偶尔'] + $count[2]['无'] + $count[2]['偶尔'];
    $cell4 = $count[1]['常常'] + $count[1]['总是'] + $count[2]['常常'] + $count[2]['总是'];
    ?>
<div class="adhd_count">
    <p style="padding:10px;">
        <span class="fb">总得分:</span>
        <?= $adhddata["scores"]?>分 (无:0，偶尔:1，常常:2，总是:3)
    </p>
    <div class="table-responsive">
        <table class="table table-bordered tc bg-white">
        <tbody>
            <tr>
                <th></th>
                <td>无+偶尔</td>
                <td>常常+总是</td>
            </tr>
            <tr>
                <td>注意</td>
                <td><?= $cell1 ?></td>
                <td><?= $cell2 ?></td>
            </tr>
            <tr>
                <td>多动+冲动</td>
                <td><?= $cell3 ?></td>
                <td><?= $cell4 ?></td>
            </tr>
        </tbody>
    </table>
    </div>
</div>
<?php } ?>

<?php if( $ename=="medicine_parent" ){?>
<div class="mp_last">
    <p class="fb pl10">用户所关心的其他问题：</p>
    <div><?= $paper->getLastAnswer()->content ?></div>
</div>
<?php } ?>
<div class="TriggerBox">
    <div class="TriggerContent colorBox colorBox-paper " id="paper_<?=$paper->id?>">
        <h5 class="refsubtitle push-10">量表明细(得分：<?= $paper->xanswersheet->score ?>)
        <a class="btn btn-default btn-xs pull-right" target="_blank" href="/xanswersheetmgr/modify?xanswersheetid=<?=$paper->xanswersheetid; ?>"><i class="fa fa-pencil"></i> 修改答卷</a>
    </h5>
<?php
if ($paper->hasAnswerSheet()) {
    ?>
<?php

    foreach ($paper->getAnswerSheet()->getAnswers() as $xanswer) {
        if ($paper->patient->notShowAdhd_ivOf26() && ($xanswer->xquestion->ename == "section_3" || $xanswer->xquestion->ename == "section_title3")) {
            continue;
        }

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
<?php $paper = null;?>
