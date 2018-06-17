<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    include $tpl . "/_pagetitle.php"; ?>
    <div class="optaskContent" style="background:#fff;">
        <?php $papers = $optask->getEvaluateList();?>
        <?php foreach ($papers as $k => $paper) { ?>
            <div class="evaluate-box">
                <div class="evaluate-box-title">
                    <span><?= $paper->createtime?></span>
                    <span><?= $paper->papertpl->title ?> [<?= $paper->user->shipstr ?>]</span>
                </div>
                <div class="evaluate-box-content none">
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
        <?php }?>
    </div>
</div>
