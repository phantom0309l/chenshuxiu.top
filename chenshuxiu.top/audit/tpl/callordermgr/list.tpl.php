<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/9/28
 * Time: 15:34
 */
$pagetitle = "电话订单 CallOrders";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                <tr>
                    <td width="90">id</td>
                    <td width="60">状态</td>
                    <td width="100">操作</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($callorders as $a) { ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getStatusDesc() ?></td>
                        <td>
                            <a href="/callordermgr/delete?callorderid=<?= $a->id ?>">删除</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
