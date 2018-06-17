<?php
$shopOrder = $a->obj;
$pipetpl = $a->pipetpl;
$objcode = $pipetpl->objcode;
?>
<?php if($shopOrder instanceof ShopOrder){ ?>
<div>
    <?php if($objcode == "pay"){ ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>支付时间</td>
                <td>第几单</td>
                <td>详情</td>
                <td>留言</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $shopOrder->time_pay ?></td>
                <td><?= $shopOrder->pos ?></td>
                <td><?= $shopOrder->getTitleAndCntOfShopProducts() ?></td>
                <td><?= $shopOrder->remark ?></td>
            </tr>
        </tbody>
    </table>
    <?php }else{ ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>创建时间</td>
                <td>第几单</td>
                <td>详情</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $shopOrder->createtime ?></td>
                <td><?= $shopOrder->pos ?></td>
                <td><?= $shopOrder->getTitleAndCntOfShopProducts() ?></td>
            </tr>
        </tbody>
    </table>
    <?php } ?>
</div>
<?php } ?>
<?php $shopOrder = null; ?>
