<?php
$pagetitle = "订单明细";
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
        <div class="col-md-10 m10">
            <form action="/shoporderitemmgr/list">
                <input type="hidden" name="patientid" value="<?=$patient->id ?>">
                <input type="hidden" name="shopproductid" value="<?=$shopProduct->id ?>">
                <a href="/shoporderitemmgr/list">全部</a>
                <span class="bold">
                    <?php if($patient instanceof Patient) { echo "患者: {$patient->name}"; } ?>
                    <?php if($shopProduct instanceof ShopProduct) { echo "商品: {$shopProduct->title}"; } ?>
                </span>
                <br />
                <span class="gray">支付 : </span>
                <?= HtmlCtr::getRadioCtrImp(CtrHelper::getShopOrderPayCtrArray(),'pay', $pay, '')?>
                <input type="submit" value="筛选" />
            </form>
        </div>
        <div style="clear: both"></div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="40">#</td>
                    <td width="100">日期</td>
                    <td width="90">订单ID</td>
                    <td width="90">明细ID</td>
                    <td width="130">患者(点击筛选)</td>
                    <td width="110">图片</td>
                    <td>商品(点击筛选)</td>
                    <td width="100" style="text-align: right">单价(元)</td>
                    <td width="60" style="text-align: right">数量</td>
                    <td width="100" style="text-align: right">金额(元)</td>
                    <td width="70">支付</td>
                    <td width="100">操作</td>
                </tr>
            </thead>
            <tbody>
                    <?php
                    $preShopOrder = null;
                    foreach ($shopOrderItems as $i => $a) {
                        $shopProduct = $a->shopproduct;
                        $sumCnt += $a->cnt;
                        $sumAmount_yuan += $a->getAmount_yuan();

                        $mergeOrder = true;
                        if ($preShopOrder->id != $a->shoporderid) {
                            $mergeOrder = false;
                            $preShopOrder = $a->shoporder;
                        }
                        ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td>
                    <?php
                        if (false == $mergeOrder) {
                            echo $a->getCreateDay();
                        } else {
                            echo "同上";
                        }
                        ?>
                    </td>
                    <td>
                        <?php if(false == $mergeOrder){ ?>
                        <a target="_blank" href="/shopordermgr/one?shoporderid=<?= $a->shoporderid ?>"><?= $a->shoporderid ?></a>
                        <?php
                        } else {
                            echo "同上";
                        }
                        ?>
                    </td>
                    <td><?= $a->id ?></td>
                    <td>
                        <a href="/shoporderitemmgr/list?patientid=<?=$a->shoporder->patientid ?>"><?= $a->shoporder->patient->name ?></a>
                    </td>
                    <td>
                        <a href="/shoporderitemmgr/list?shopproductid=<?=$shopProduct->id ?>">
                            <img src="<?=$shopProduct->picture->getSrc(100,100) ?>">
                        </a>
                    </td>
                    <td>
                        <a href="/shoporderitemmgr/list?shopproductid=<?=$shopProduct->id ?>">
                                <?= $a->shopproductid?>
                                <?= $shopProduct->title?>
                            </a>
                    </td>
                    <td align="right"><?= $a->getPrice_yuan()?></td>
                    <td align="right"><?= $a->cnt?></td>
                    <td align="right"><?= $a->getAmount_yuan()?></td>
                    <td><?= $a->shoporder->getIs_payStr()?></td>
                    <td>
                        <a target="_blank" href="/shopproductmgr/one?shopproductid=<?=$a->shopproductid ?>">商品</a>
                        <a target="_blank" href="/shopordermgr/one?shoporderid=<?= $a->shoporderid ?>">订单</a>
                    </td>
                </tr>
                    <?php } ?>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">合计:</td>
                    <td align="right"><?=$sumCnt ?></td>
                    <td align="right"><?=$sumAmount_yuan ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <td>#</td>
                    <td>日期</td>
                    <td>订单ID</td>
                    <td>明细ID</td>
                    <td>患者</td>
                    <td>图片</td>
                    <td>商品</td>
                    <td style="text-align: right">单价(元)</td>
                    <td style="text-align: right">数量</td>
                    <td style="text-align: right">金额(元)</td>
                    <td>支付</td>
                    <td>操作</td>
                </tr>
            </thead>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
