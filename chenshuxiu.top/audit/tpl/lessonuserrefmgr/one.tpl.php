<?php
$pagetitle = "{$lessonUserRef->patient->name}作业详情";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
        .refsubtitle {
            color: #1b809e;
        }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <h4 class="refsubtitle">基本信息</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>lessonUserRefId</td>
                <td>创建日期</td>
                <td>患者名</td>
                <td>用户名</td>
                <td>关系</td>
                <td>微信名</td>
                <td>课文名</td>
                <td>联系方式</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= $lessonUserRef->id ?></td>
                <td><?= $lessonUserRef->getCreateDayHi() ?></td>
                <td>
                    <a href="/lessonuserrefmgr/list?patientid=<?= $lessonUserRef->patientid ?>">
                        <?= $lessonUserRef->patient->name ?></a>
                </td>
                <td>
                    <a href="/usermgr/modify?userid=<?= $lessonUserRef->userid ?>">
                        <?= $lessonUserRef->user->name ?></a>
                </td>
                <td><?= $lessonUserRef->user->shipstr ?></td>
                <td><?= $lessonUserRef->wxuser->nickname ?></td>
                <td>
                    <a href="/lessonuserrefmgr/list?lessonid=<?= $lessonUserRef->lessonid ?>">
                        <?= $lessonUserRef->course->title ?>
                        <br/><?= $lessonUserRef->lesson->title ?></a>
                </td>
                <td>
                    <?php
                    if ($lessonUserRef->user instanceof User) {
                        echo $lessonUserRef->user->getMaskMobile();
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
        </div>
        <h4 class="refsubtitle">课堂巩固</h4>
        <?php

        if ($lessonUserRef->hasTestAnswerSheet()) {
            foreach ($lessonUserRef->getTestAnswerSheet()->getAnswers() as $a) {
                echo $a->getQuestionCtr()->getQaHtml4lesson();
            }
        } else {
            ?>
            <p>暂时没写</p>
        <?php } ?>

        <h4 class="refsubtitle">课堂作业</h4>
        <?php

        if ($lessonUserRef->hasHwkAnswerSheet()) {
            foreach ($lessonUserRef->getHwkAnswerSheet()->getAnswers() as $a) {
                if (false == $a->isDefaultHide()) {
                    echo $a->getQuestionCtr()->getQaHtml4lesson();
                }
            }
        } else {
            ?>
            <p>暂时没写</p>
        <?php } ?>

    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
