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
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/shopordermgr/listformarket" class="form-horizontal shopOrderForm">
                <div class="form-group">
                    <label class="col-md-2 control-label">类型 :</label>
                    <div class="col-md-6">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderTypeCtrArray(),'type', $type, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">开始：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="startdate" value="<?= $startdate ?>" placeholder="开始时间" />
                    </div>
                    <label class="control-label col-md-2">截止：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="enddate" value="<?= $enddate ?>" placeholder="截至时间" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>

                <div>
                    <div class="col-md-2">
                        实际销售额: <span class="red">¥<?= $left_amount_yuan_all ?></span>
                    </div>
                    <div class="col-md-2">
                        订单数: <span class="red"><?= $shop_order_cnt ?></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="100">支付时间</td>
                    <td width="100">订单类型</td>
                    <td>第几单</td>
                    <td>患者</td>
                    <td>医生</td>
                    <td>市场</td>
                    <td width="150">商品详情</td>
                    <td>金额(元)</td>
                    <td>退款</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($shopOrders as $i => $a) {
                    ?>
                <tr>
                    <td>
                        <?= substr($a->time_pay,0,10) ?>
                    </td>
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
                    <td><?= $a->thedoctor->name?></td>
                    <td><?= $a->thedoctor->marketauditor->name ?></td>
                    <td>
                        品类数：<?= $a->getShopOrderItemCnt() ?><br/>
                        总数量：<?= $a->getShopProductSumCnt() ?><br/>
                        <p style="font-size:12px;"><?= $a->getTitleOfShopProducts() ?></p>
                    </td>
                    <td align="right">
                        <?= $a->getItem_sum_price_yuan() ?>
                    </td>
                    <td align="center"><?= $a->getRefundStr()?></td>
                </tr>
                <?php } ?>
                </tbody>
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
