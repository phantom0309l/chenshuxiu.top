<?php
$pagetitle = "退货";
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
        <form action="/shoporderitemmgr/refundShopProductPost" method="post">
            <input type="hidden" value="<?= $shopOrderItem->id ?>" name="shoporderitemid"/>
            <input type="hidden" value="<?= $is_recycle ?>" name="is_recycle"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td width='140'></td>
                    <td>
                        <p class="red">当前最大退货数为：<?= $shopOrderItem->getMaxGoodsBackCnt() ?></p>
                    </td>
                </tr>
                <tr>
                    <td>商品</td>
                    <td>
                        <p><?= $shopOrderItem->shopproduct->title ?></p>
                        <p><img src="<?= $shopOrderItem->shopproduct->picture->getSrc(100,100) ?>"/></p>
                    </td>
                </tr>
                <tr>
                    <td>销售单价</td>
                    <td>
                        <div>¥<?= $shopOrderItem->getPrice_yuan() ?></div>
                    </td>
                </tr>
                <tr>
                    <td>退货数量</td>
                    <td>
                        <input type="text" name="goods_back_cnt" value=""/>
                        <span class="red">最多可退：<?= $shopOrderItem->getMaxGoodsBackCnt() ?></span>
                    </td>
                </tr>
                <tr>
                    <td>备注</td>
                    <td>
                        <textarea rows="10" cols="120" name="remark"></textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" class="btn btn-success" value="提交" />
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
