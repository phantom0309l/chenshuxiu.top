<?php
$pagetitle = "商城商品 详情页";
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
                <th width='140'>shopproductid</th>
                <td>
                    <?= $shopProduct->id?>
                </td>
            </tr>
            <tr>
                <th>商品类型</th>
                <td>
                    <?= $shopProduct->shopproducttype->name; ?>
                </td>
            </tr>
            <tr>
                <th>图片</th>
                <td>
                    <img alt="" src="<?= $shopProduct->picture->getSrc(200,200)?>">
                </td>
            </tr>
            <tr>
                <th>配图</th>
                <td>
                    <?php foreach($shopProduct->getShopProductPictures() as $a){ ?>
                    <img class="border1" src="<?= $a->picture->getSrc(100,100)?>">
                    <?php } ?>
                    <a href="/shopproductpicturemgr/list?shopproductid=<?=$shopProduct->id ?>">修改配图</a>
                </td>
            </tr>
            <tr>
                <th>商品标题</th>
                <td>
                    <?= $shopProduct->title; ?>
                </td>
            </tr>
            <tr>
                <th>商品sku_code</th>
                <td>
                    <?= $shopProduct->sku_code; ?>
                </td>
            </tr>
            <tr>
                <th>商品介绍</th>
                <td>
                    <?= nl2br($shopProduct->content); ?>
                </td>
            </tr>
            <tr>
                <th>价格</th>
                <td>
                    <?= $shopProduct->getPrice_yuan(); ?>
                    元
                </td>
            </tr>
            <tr>
                <th>市场价格</th>
                <td>
                    <?= $shopProduct->getMarket_price_yuan(); ?>
                    元
                </td>
            </tr>
            <tr>
                <th>包装单位</th>
                <td>
                    <?= $shopProduct->pack_unit; ?>
                </td>
            </tr>
            <tr>
                <th>库存量</th>
                <td>
                    <?= $shopProduct->left_cnt; ?>
                </td>
            </tr>
            <tr>
                <th>警戒值</th>
                <td>
                    <?= $shopProduct->warning_cnt; ?>
                </td>
            </tr>
            <tr>
                <th>提醒运营线</th>
                <td>
                    <?= $shopProduct->notice_cnt; ?>
                </td>
            </tr>
            <tr>
                <th>关联obj</th>
                <td>
                    <?= $shopProduct->objtype?>
                    <?= $shopProduct->objid?>
                </td>
            </tr>
            <tr>
                <th>状态</th>
                <td>
                    <?= $shopProduct->getStatusDesc()?>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <a href="/shopproductmgr/modify?shopproductid=<?=$shopProduct->id ?>">修改</a>
                </td>
            </tr>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
