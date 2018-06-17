<?php
$pagetitle = "活跃度日列表";
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
            <div class="searchBar">活跃率 = 总活跃报到人数 / 总报到人数</div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>日期</td>
                        <td>总人数</td>
                        <td>
                            总报到
                            <br />
                            人数
                        </td>
                        <td>
                            总活跃
                            <br />
                            人数
                        </td>
                        <td>活跃率</td>
                        <td>
                            新流
                            <br />
                            总数
                        </td>
                        <td>
                            图片
                            <br />
                            总数
                        </td>
                        <td>
                            文本
                            <br />
                            总数
                        </td>
                        <td>
                            答卷
                            <br />
                            总数
                        </td>
                        <td>
                            日记
                            <br />
                            总数
                        </td>
                        <td>
                            作业
                            <br />
                            总数
                        </td>
                        <td>
                            新流
                            <br />
                            人数
                        </td>
                        <td>
                            图片
                            <br />
                            人数
                        </td>
                        <td>
                            文本
                            <br />
                            人数
                        </td>
                        <td>
                            答卷
                            <br />
                            人数
                        </td>
                        <td>
                            日记
                            <br />
                            人数
                        </td>
                        <td>
                            作业
                            <br />
                            人数
                        </td>
                        <td>各种率</td>
                        <td>明细</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($list as $a) {
                    ?>
                    <tr>
                        <td><?= $a->getMd() ?></td>
                        <td><?= $a->allcnt ?></td>
                        <td><?= $a->sumcnt0 ?></td>
                        <td><?= $a->sumcnt1 ?></td>
                        <td><?= $a->getActivityRate() ?></td>
                        <td><?= $a->pipe_sumcnt ?></td>
                        <td><?= $a->wxpicmsg_sumcnt ?></td>
                        <td><?= $a->wxtxtmsg_sumcnt ?></td>
                        <td><?= $a->answersheet_sumcnt ?></td>
                        <td><?= $a->patientnote_sumcnt ?></td>
                        <td><?= $a->fbt_sumcnt ?></td>
                        <td><?= $a->pipe_pcnt ?></td>
                        <td><?= $a->wxpicmsg_pcnt ?></td>
                        <td><?= $a->wxtxtmsg_pcnt ?></td>
                        <td><?= $a->answersheet_pcnt ?></td>
                        <td><?= $a->patientnote_pcnt ?></td>
                        <td><?= $a->fbt_pcnt ?></td>
                        <td>
                            <a href="/rptmgr/rpt_date_patient_one?rpt_date_patient_id=<?= $a->id ?>"><?= $a->getMd() ?></a>
                        </td>
                        <td>
                            <a href="/rptmgr/rpt_patient_list_of_date?thedate=<?= $a->thedate ?>"><?= $a->getMd() ?></a>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=23>
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
