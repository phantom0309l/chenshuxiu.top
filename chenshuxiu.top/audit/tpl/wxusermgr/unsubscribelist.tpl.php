<?php
$pagetitle = "取消关注列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
<p class="red">
    有<?= $resultArr['baodaocnt'] ?>人报到过, 有<?= $resultArr['notbaodaocnt'] ?>人从未报到<br/>
    有<?= $resultArr['num1'] ?>人一天内取消报到, 有<?= $resultArr['num2'] ?>人一周内取消报到, 有<?= $resultArr['num3'] ?>一周后取消报到。
</p>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>微信号</th>
                        <th>所属医生</th>
                        <th>患者名</th>
                        <th>报到时间</th>
                        <th>取消关注</th>
                        <th>daycnt</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($wxusers as $a) { ?>
                    <tr>
                        <td><?= $a->nickname ?></td>
                        <td><?= $a->doctor->name ?></td>
                        <td><?= $a->user->patient->name ?></td>
                        <td>
                            <?php
                                if ($a->user->patient instanceof Patient) {
                                    echo $a->user->patient->getCreateDay();
                                }
                            ?>
                        </td>
                        <td><?= substr($a->unsubscribe_time, 0, 10) ?></td>
                        <td><?= $a->getFromBaodaoToUnsubscribeDayCnt() ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan=6 class="pagelink">
                            <?php include $dtpl."/pagelink.ctr.php";  ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
