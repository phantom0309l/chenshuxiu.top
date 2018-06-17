<?php
$pagetitle = "市场部绩效详情（按月）";
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
                        <th>月份</th>
                        <th>新增患者数</th>
                        <th>未报到患者数</th>
                        <th>共计</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                    <?php
                    if ($auditor) {
                        ?>
                        <?=$auditor->name?>
                        <?php
                    } else {
                        ?>
                        全部
                        <?php
                    }
                    ?>
                </td>
                        <td>
                    <?=$themonth?>
                </td>
                        <td style="color: red">
                    <?=count($baodao_patients)?>
                </td>
                        <td>
                    <?=count($notbaodao_wxusers)?>
                </td>
                        <td>
                    <?=count($notbaodao_wxusers) + count($baodao_patients)?>
                </td>
                    </tr>
                </tbody>
            </table>
            </div>
        <?php
        $pagetitle = "市场部绩效详情（按周）";
        include $tpl . "/_pagetitle.php";
        ?>
            <div class="table-responsive">
                <table class="table table-bordered thcenter tdcenter" style="width: 60%">
                <tr>
                    <th>姓名</th>
                    <th>日期</th>
                    <th>新增患者数</th>
                    <th>未报到患者数</th>
                    <th>共计</th>
                </tr>
            <?php

            foreach ($arr_staticsbywoy as $k => $v) {
                ?>
                <tr>
                    <td>
                        <?php
                if ($auditor) {
                    ?>
                            <?=$auditor->name?>
                            <?php
                } else {
                    ?>
                            全部
                            <?php
                }
                ?>
                    </td>
                    <td>
                        <?= $v["themonth"]?>
                    </td>
                    <td style="color: red">
                        <?= $v["baodao"]?>
                    </td>
                    <td>
                        <?= $v["notbaodao"]?>
                    </td>
                    <td>
                        <?= $v["baodao"] + $v["notbaodao"]?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
            </div>
            <?php
            $pagetitle = "患者列表";
            include $tpl . "/_pagetitle.php";
            ?>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter" style="width: 60%">
                <tr>
                    <th>微信名</th>
                    <th>患者名</th>
                    <th>关注时间</th>
                    <th>是否报到</th>
                    <th>所属医生</th>
                    <th>市场负责人</th>
                </tr>
                <?php foreach ($baodao_patients as $a) {
                    $masterwxuser = $a->getMasterWxUser();
                    ?>
                    <tr>
                    <td>
                            <?= $masterwxuser->nickname?>
                        </td>
                    <td>
                        <a href="/patientmgr/list?keyword=<?=$a->name?>"><?= $a->name ?></a>
                    </td>
                    <td>
                            <?= substr($masterwxuser->createtime, 0, 10)?>
                        </td>
                    <td>是</td>
                    <td>
                            <?= $a->doctor->name?>
                        </td>
                    <td>
                            <?= $a->doctor->marketauditor->name?>
                        </td>
                </tr>
                    <?php
                }
                ?>
                <?php foreach ($notbaodao_wxusers as $a) { ?>
                    <tr>
                    <td>
                            <?= $a->nickname?>
                        </td>
                    <td></td>
                    <td>
                            <?= substr($a->createtime, 0, 10)?>
                        </td>
                    <td>否</td>
                    <td>
                            <?= $a->doctor->name?>
                        </td>
                    <td>
                            <?= $a->doctor->marketauditor->name?>
                        </td>
                </tr>
                    <?php
                }
                ?>
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
