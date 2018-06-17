<?php
$pagetitle = "当月管理收益详情";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12" style="display: block">

            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>医院</th>
                        <th>疾病</th>
                        <th>市场负责人</th>
                        <th>
                    <?=$themonth?>月报到和活跃患者数
                </th>
                        <th>新报到患者</th>
                        <th>六个月内持续管理患者</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?=$doctor->name?>
                        </td>
                        <td>
                            <?=$doctor->hospital->name?>
                        </td>
                        <td>
                            <?= $doctor->getDiseaseNamesStr()?>
                        </td>
                        <td>
                            <?=$doctor->marketauditor->name?>
                        </td>
                        <td>
                            <?=$settleorder->activecnt?>人
                        </td>
                        <td>
                            <?=count($rpt_patient_month_settles_baodao)?>人
                        </td>
                        <td>
                            <?=count($rpt_patient_month_settles_manage)?>人
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        <?php
        $pagetitle = "新报到患者";
        include $tpl . "/_pagetitle.php";
        ?>
            <div class="table-responsive">
                <table class="table table-bordered thcenter tdcenter" style="width: 60%">
                <tr>
                    <th>微信名</th>
                    <th>患者名</th>
                    <th>关注时间</th>
                    <th>是否报到</th>
                </tr>
            <?php

            foreach ($rpt_patient_month_settles_baodao as $a) {
                $masterwxuser = $a->patient->getMasterWxUser(1);
                ?>
                <tr>
                    <td>
                        <?= $masterwxuser->nickname ;?>
                    </td>
                    <td>
                        <a href="/patientmgr/list?keyword=<?= $a->patient->name ?>"><?= $a->patient->name ?></a>
                    </td>
                    <td>
                        <?= substr($a->patient->createtime, 0, 10)?>
                    </td>
                    <td class="has_active">已报到（￥15）</td>
                </tr>
                <?php
            }
            ?>
            <?php foreach($wxusers_notbaodao as $wxuser){?>
                <tr>
                    <td>
                        <?=$wxuser->nickname?>
                    </td>
                    <td></td>
                    <td>
                        <?=substr($wxuser->createtime, 0, 10)?>
                    </td>
                    <td>未报到</td>
                </tr>
                <?php
            }
            ?>
            <tr>
                    <td colspan="2">共计：</td>
                    <td colspan="2">
                    <?=count($rpt_patient_month_settles_baodao)+count($wxusers_notbaodao)?>人
                </td>
                </tr>
                <tr>
                    <td colspan="2">未报到人数：</td>
                    <td colspan="2">
                    <?=count($wxusers_notbaodao)?>人
                </td>
                </tr>
                <tr>
                    <td colspan="2">本月新报到患者：</td>
                    <td class="has_active" colspan="2">
                    <?=count($rpt_patient_month_settles_baodao)?>人
                </td>
                </tr>
            </table>
            </div>
        <?php if(strtotime($themonth) > strtotime("2016-04")) { ?>
        <?php
            $pagetitle = "六个月内持续管理患者";
            include $tpl . "/_pagetitle.php";
            ?>

            <div class="table-responsive">
                <table class="table table-bordered tdcenter" style="width: 60%">
                <tr>
                    <th>患者名</th>
                    <th>报到时间</th>
                    <th>持续管理时长</th>
                </tr>
            <?php foreach ($rpt_patient_month_settles_manage as $a) { ?>
                <tr>
                    <td>
                        <a href="/patientmgr/list?keyword=<?= $a->patient->name ?>"><?= $a->patient->name ?></a>
                    </td>
                    <td>
                        <?= $a->baodaodate?>
                    </td>
                    <td>
                        <?= $a->month_pos ?>个月
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr>
                    <td colspan="2">共计：</td>
                    <td class="has_active">
                    <?= count($rpt_patient_month_settles_manage) ?>人
                </td>
                </tr>
            </table>
            </div>
<?php
        }
        ?>
    </section>
    </div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>