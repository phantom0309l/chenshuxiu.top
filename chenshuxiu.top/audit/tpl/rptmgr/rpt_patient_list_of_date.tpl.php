<?php
$pagetitle = "{$thedate} 总报到患者列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <?php
    if (false == $rpt_date_patient instanceof Rpt_date_patient) {
        exit();
    }
    ?>

    <div class="col-md-12">
        <section class="col-md-12">
                <?php if($istest){ ?>
                <a href="/rptmgr/rpt_patient_list_of_date?thedate=<?=$thedate ?>">标准</a>
                测试+韩颖
                <?php }else{ ?>
                标准
                <a href="/rptmgr/rpt_patient_list_of_date?thedate=<?=$thedate ?>&istest=1">测试+韩颖</a>
                <?php } ?>

            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>patientid</td>
                        <td>姓名</td>
                        <td>状态</td>
                        <td>服药</td>
                        <td>报到日期</td>
                        <td>
                            末次换药
                            <br />
                            首次服药
                        </td>
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
                        <td>流</td>
                        <td>图片</td>
                        <td>文本</td>
                        <td>答卷</td>
                        <td>日记</td>
                        <td>作业</td>
                        <td>回执单</td>
                        <td>历史</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($list as $i => $a) {
                    if (false == $a->patient instanceof Patient) {
                        continue;
                    }

                    ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= $a->patientid ?></td>
                        <td><?= $a->patient->getNameOrNameOfUser() ?> (<?= $a->patient->doctor->name ?>)</td>
                        <td><?= $a->getActivityStrFix($day_rpt_array[$a->patientid]) ?></td>
                        <td><?= $a->medicinestr ?></td>
                        <td><?= $a->patient->getBaodaoDay() ?></td>
                        <td>todo</td>
                        <td><?= $a->lastactivitydate ?></td>
                        <td><?= $a->nextactivitydate ?></td>
                        <td><?= $a->pipe_cnt ?></td>
                        <td><?= $a->wxpicmsg_cnt ?></td>
                        <td><?= $a->wxtxtmsg_cnt ?></td>
                        <td><?= $a->answersheet_cnt ?></td>
                        <td><?= $a->patientnote_cnt ?></td>
                        <td><?= $a->fbt_cnt ?></td>
                        <td>TODO</td>
                        <td>
                            <a href="/rptmgr/rpt_patient_list_of_patient?patientid=<?= $a->patientid ?>"><?= $a->patient->getNameOrNameOfUser() ?></a>
                        </td>
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
