<?php
$pagetitle = "每日统计 of {$patient->id } : {$patient->getNameOrNameOfUser() } ({$patient->doctor->name })";
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
                        <td>序号</td>
                        <td>日期</td>
                        <td>状态</td>
                        <td>服药</td>
                        <td>
                            行为
                            <br />
                            上次
                        </td>
                        <td>
                            行为
                            <br />
                            下次
                        </td>
                        <td>全部流</td>
                        <td>图片</td>
                        <td>文本</td>
                        <td>答卷</td>
                        <td>日记</td>
                        <td>作业</td>
                        <td>回执单</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($list as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= $a->thedate ?></td>
                        <td><?= $a->activitystr ?></td>
                        <td><?= $a->medicinestr ?></td>
                        <td><?= $a->lastactivitydate ?></td>
                        <td><?= $a->nextactivitydate ?></td>
                        <td><?= $a->pipe_cnt ?></td>
                        <td><?= $a->wxpicmsg_cnt ?></td>
                        <td><?= $a->wxtxtmsg_cnt ?></td>
                        <td><?= $a->answersheet_cnt ?></td>
                        <td><?= $a->patientnote_cnt ?></td>
                        <td><?= $a->fbt_cnt ?></td>
                        <td>TODO</td>
                    </tr>
                <?php } ?>
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
