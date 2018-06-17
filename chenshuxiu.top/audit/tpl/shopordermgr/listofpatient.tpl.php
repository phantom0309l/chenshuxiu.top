<?php
$pagetitle = "订单/处方申请单";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
STYLE;
$pageScript = <<<SCRIPT
$(function(){
    $('.js-select2').select2();
})
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <?php include_once $tpl . "/patientmgr/_menu.tpl.php"; ?>
    <div class="content-div">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="80">创建时间</td>
                    <td width="80">支付时间</td>
                    <td width="80">订单类型</td>
                    <td>第几单</td>
                    <td>患者</td>
                    <td>收货人</td>
                    <td>医生</td>
                    <td>市场</td>
                    <td width="150">商品详情</td>
                    <td>快递+挂号+商品=总金额(元)</td>
                    <td>支付</td>
                    <td>退款</td>
                    <td>状态</td>
                    <td>发货</td>
                    <td>留言</td>
                    <td width="50">快递单号</td>
                    <td width="50">处方编号</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($shopOrders as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->time_pay ?></td>
                    <td><?= $a->getTypeDesc() ?></td>
                    <td><?= $a->pos ?></td>
                    <td>
                        <a href="/shopordermgr/list?patientid=<?= $a->patientid ?>">
                            <?php if($a->patient instanceof Patient){ ?>
                            <?= $a->patient->getMaskName() ?>
                            <sup>[<?= ShopOrderDao::getShopOrderCntByPatientTime_paydate($a->patient, substr($a->time_pay,0,10)) ?>]</sup>
                            <?php } ?>
                        </a>
                    </td>
                    <td>
                        <?php if($a->shopaddress instanceof ShopAddress){ ?>
                            <span><?= $a->shopaddress->linkman_name ?></span>
                        <?php } ?>
                    </td>
                    <td><?= $a->thedoctor->name?></td>
                    <td><?= $a->thedoctor->marketauditor->name ?></td>
                    <td>
                        品类数：<?= $a->getShopOrderItemCnt() ?><br/>
                        总数量：<?= $a->getShopProductSumCnt() ?><br/>
                        <p style="font-size:12px;"><?= $a->getTitleOfShopProducts() ?></p>
                    </td>
                    <td align="right">
                        <?= $a->getExpress_price_yuan()?> + <?= $a->getGuahao_price_yuan() ?> + <?= $a->getItem_sum_price_yuan() ?> = <?= $a->getAmount_yuan()?>
                    </td>
                    <td align="center"><?= $a->getIs_payStr()?></td>
                    <td align="center"><?= $a->getRefundStr()?></td>
                    <td align="center"><?= $a->getStatusStr()?></td>
                    <td align="center">
                        <?php $shopPkgs = $a->getShopPkgs();
                        foreach ($shopPkgs as $shopPkg){ ?>
                            <?= $shopPkg->getIs_sendoutStr()?>
                        <?php } ?>
                    </td>
                    <td align="center"><?= $a->remark ?></td>
                    <td align="center">
                        <?php foreach ($shopPkgs as $shopPkg){ ?>
                            <?= $shopPkg->express_no ?><br/>
                        <?php } ?>
                    </td>
                    <td align="center">
                        <?php
                            $prescription = PrescriptionDao::getPrescriptionByShopOrder($a);
                            if($prescription instanceof Prescription){
                                echo $prescription->chufang_cfbh;
                            }
                        ?>
                    </td>
                    <td align="center">
                        <a target="_blank" href="/shopordermgr/one?shoporderid=<?=$a->id ?>">详情</a>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
    </div>
</div>
<div class="clear"></div>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
