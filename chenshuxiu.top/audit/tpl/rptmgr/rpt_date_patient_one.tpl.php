<?php
$pagetitle = "{$rpt_date_patient->thedate } 各种率";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
    <?php

    if (false == $rpt_date_patient instanceof Rpt_date_patient) {
        exit();
    }
    ?>
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>标题</th>
                        <th>百分比</th>
                        <th>有效人数</th>
                        <th>总数</th>
                        <th>说明</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>报到率</td>
                        <td><?= $rpt_date_patient->getBaodaoRate(); ?></td>
                        <td><?= $rpt_date_patient->sumcnt0; ?></td>
                        <td><?= $rpt_date_patient->allcnt; ?></td>
                        <td>报到人数/扫码总人数</td>
                    </tr>
                    <tr>
                        <td>服药率</td>
                        <td><?= $rpt_date_patient->getMedicineRate(); ?></td>
                        <td><?= $rpt_date_patient->yes_sumcnt0; ?></td>
                        <td><?= $rpt_date_patient->allcnt; ?></td>
                        <td>服药人数/扫码总人数</td>
                    </tr>
                    <tr>
                        <td>活跃率</td>
                        <td><?= $rpt_date_patient->getActivityRate(); ?></td>
                        <td><?= $rpt_date_patient->sumcnt1; ?></td>
                        <td><?= $rpt_date_patient->sumcnt0; ?></td>
                        <td>活跃报到人数/报到总人数</td>
                    </tr>
                    <tr>
                        <td>报到用户服药率</td>
                        <td><?= $rpt_date_patient->getMedicineRateOfBaodao(); ?></td>
                        <td><?= $rpt_date_patient->yes_sumcnt0; ?></td>
                        <td><?= $rpt_date_patient->sumcnt0; ?></td>
                        <td>服药人数/报到总人数</td>
                    </tr>
                    <tr>
                        <td>服药用户活跃率</td>
                        <td><?= $rpt_date_patient->getActivityRateOfMedicine(); ?></td>
                        <td><?= $rpt_date_patient->yes_sumcnt1; ?></td>
                        <td><?= $rpt_date_patient->yes_sumcnt0; ?></td>
                        <td>服药用户活跃人数/服药用户总人数</td>
                    </tr>
                    <tr>
                        <td>择思达服药率</td>
                        <td><?= $rpt_date_patient->getZsdRateOfMedicine(); ?></td>
                        <td><?= $rpt_date_patient->yes_zsd_cnt0; ?></td>
                        <td><?= $rpt_date_patient->yes_sumcnt0; ?></td>
                        <td>择思达总人数/服药用户总人数</td>
                    </tr>
                    <tr>
                        <td>专注达服药率</td>
                        <td><?= $rpt_date_patient->getZzdRateOfMedicine(); ?></td>
                        <td><?= $rpt_date_patient->yes_zzd_cnt0; ?></td>
                        <td><?= $rpt_date_patient->yes_sumcnt0; ?></td>
                        <td>专注达总人数/服药用户总人数</td>
                    </tr>
                    <tr>
                        <td>择思达活跃率</td>
                        <td><?= $rpt_date_patient->getActivityZsdRateOfMedicine(); ?></td>
                        <td><?= $rpt_date_patient->yes_zsd_cnt1; ?></td>
                        <td><?= $rpt_date_patient->yes_zsd_cnt0; ?></td>
                        <td>活跃择思达人数/总活跃服药人数</td>
                    </tr>
                    <tr>
                        <td>专注达活跃率</td>
                        <td><?= $rpt_date_patient->getActivityZzdRateOfMedicine(); ?></td>
                        <td><?= $rpt_date_patient->yes_zzd_cnt1; ?></td>
                        <td><?= $rpt_date_patient->yes_zzd_cnt0; ?></td>
                        <td>活跃择思达人数/总活跃服药人数</td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>标题</th>
                        <th>数值</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach(Rpt_date_patient::getKeyDescArray() as $k => $v){ ?>
                    <tr>
                        <td width="200"><?=$v ?></td>
                        <td><?= $rpt_date_patient->$k; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include $tpl . "/_footer.php"; ?>
</body>
</html>
