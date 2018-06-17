<?php
$pagetitle = "处方条目列表 PrescriptionItems";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <td>处方ID</td>
                    <td>处方明细ID</td>
                    <td>日期</td>
                    <td>药品名</td>
                    <td>规格</td>
                    <td>用量</td>
                    <td>方法</td>
                    <td>频次</td>
                    <td>数量</td>
                    <td>单位</td>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($prescriptionitems as $a) {
                ?>
                <tr>
                    <td>
                        <a target="_blank" href="/prescriptionmgr/one?prescriptionid=<?= $a->prescriptionid ?>"><?= $a->prescriptionid ?></a>
                    </td>
                    <td><?= $a->id ?></td>
                    <td><?= $a->getCreateDay() ?></td>
                    <td><?= $a->medicine_title ?></td>
                    <td><?= $a->size_pack ?></td>
                    <td><?= $a->drug_dose ?></td>
                    <td><?= $a->drug_way ?></td>
                    <td><?= $a->drug_frequency ?></td>
                    <td><?= $a->cnt ?></td>
                    <td><?= $a->pack_unit ?></td>
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