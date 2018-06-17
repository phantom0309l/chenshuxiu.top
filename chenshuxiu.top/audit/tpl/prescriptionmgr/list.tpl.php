<?php
$pagetitle = "处方列表 Prescriptions";
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
                    <td>id</td>
                    <td>处方类型</td>
                    <td>患者</td>
                    <td>医师</td>
                    <td>开方时间</td>
                    <td>审核药师</td>
                    <td>审核时间</td>
                    <td>发货药师</td>
                    <td>发货时间</td>
                    <td>状态</td>
                    <td>md5</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($prescriptions as $a) {
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->getTypeDesc();?></td>
                    <td><?= $a->patient_name ?></td>
                    <td><?= $a->yishi->name ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->yaoshi_audit->name ?></td>
                    <td><?= $a->time_audit ?></td>
                    <td><?= $a->yaoshi_send->name ?></td>
                    <td><?= $a->time_send ?></td>
                    <td><?= $a->getStatusDesc() ?></td>
                    <td><?= $a->md5str ?></td>
                    <td>
                        <a target="_blank" href="/prescriptionmgr/one?prescriptionid=<?= $a->id ?>">详情</a>
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