<?php
$pagetitle = "电话商品 详情页";
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
    <section class="col-md-10">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width='140'>callproductid</th>
                    <td>
                        <?= $callProduct->id ?>
                    </td>
                </tr>
                <tr>
                    <th>商品标题</th>
                    <td>
                        <?= $callProduct->title; ?>
                    </td>
                </tr>
                <tr>
                    <th>商品介绍</th>
                    <td>
                        <?= nl2br($callProduct->content); ?>
                    </td>
                </tr>
                <tr>
                    <th>价格</th>
                    <td>
                        <?= $callProduct->getPrice_yuan(); ?>
                        元
                    </td>
                </tr>
                <tr>
                    <th>市场价格</th>
                    <td>
                        <?= $callProduct->getMarket_price_yuan(); ?>
                        元
                    </td>
                </tr>
                <tr>
                    <th>包装单位</th>
                    <td>
                        <?= $callProduct->pack_unit; ?>
                    </td>
                </tr>
                <tr>
                    <th>关联obj</th>
                    <td>
                        <?= $callProduct->objtype ?>
                        <?= $callProduct->objid ?>
                    </td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td>
                        <?= $callProduct->getStatusDesc() ?>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <a href="/callproductmgr/modify?callproductid=<?= $callProduct->id ?>">修改</a>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
