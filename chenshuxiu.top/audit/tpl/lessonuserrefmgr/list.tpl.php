<?php
$pagetitle = "用户学课记录 LessonUserRef";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>id</td>
                <td>创建日期</td>
                <td>患者名</td>
                <td>用户名</td>
                <td>课程</td>
                <td>课文</td>
                <td>作业</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($lessonuserrefs as $a) {
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->getCreateDayHi() ?></td>
                    <td>
                        <a href="/lessonuserrefmgr/list?patientid=<?= $a->patientid ?>">
                            <?php
                            if ($a->patient instanceof Patient) {
                                echo $a->patient->getMaskName();
                            }
                            ?>
                        </a>
                    </td>
                    <td>
                        <a href="/lessonuserrefmgr/list?userid=<?= $a->userid ?>">
                            <?= $a->user->name ?> (<?= $a->user->shipstr ?>)</a>
                    </td>
                    <td><?= $a->course->title ?><br/><?= $a->course->subtitle ?></td>
                    <td>
                        <a href="/lessonuserrefmgr/list?lessonid=<?= $a->lessonid ?>">
                            <?= $a->lesson->title ?></a>
                    </td>
                    <td>
                        <?php if ($a->hasHwkAnswerSheet()) { ?>
                            <a href="/lessonuserrefmgr/one?lessonuserrefid=<?= $a->id ?>">作业</a>
                        <?php } else { ?>
                            <a href="/lessonuserrefmgr/one?lessonuserrefid=<?= $a->id ?>">--</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan=10>
                    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                </td>
            </tr>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
