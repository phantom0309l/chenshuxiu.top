<div class="block block-rounded push-10-t">
    <div class="block-header bg-gray-light" style="padding:10px 15px;">
        <ul class="block-options">
            <li>
            <a target="_blank" style="opacity: 1.0;color:#3169b1;" href="/patientmgr/drugdetail?patientid=<?=$patient->id?>" data-toggle="tooltip" title="" data-original-title="用药详情"><i class="fa fa-search">用药详情</i></a>
            </li>
        </ul>
        <h3 class="block-title">
            <p style="padding:0px;margin:0px;">
                患者用药：
            <?php
            if ($patient->isNoDruging() === true) {
            echo '<em class="red noDrug">[不服药]</em>';
            } elseif ($patient->isDruging()) {
            echo '<em class="red noDrug">[服药中]</em>';
            } elseif ($patient->isStopDruging()) {
            echo '<em class="red noDrug">[停药]</em>';
            } else {
            echo '<em class="red noDrug">[未知]</em>';
            }
            ?>
            </p>
        </h3>
    </div>

    <div class="block-content" style="padding:0px;">
    <?php if(isset($shopOrders) && count($shopOrders) > 0){ ?>
    <table class="table table-bordered">
        <tr>
            <td>购药日期</td>
            <td>开药人</td>
            <td>购药详情</td>
            <td>退单</td>
        </tr>
        <?php foreach($shopOrders as $shopOrder){ ?>
            <tr>
                <td><?= substr($shopOrder->time_pay, 0, 10) ?></td>
                <td><?= $shopOrder->user->shipstr ?></td>
                <td>
                    <?php foreach($shopOrder->getShopOrderItems() as $shopOrderItem){ ?>
                        <p><?= $shopOrderItem->shopproduct->title ?> <span class="red"><?= $shopOrderItem->cnt ?><?= $shopOrderItem->shopproduct->pack_unit ?></span></p>
                    <?php } ?>
                </td>
                <td>
                    <?php foreach($shopOrder->getShopOrderItems() as $shopOrderItem){
                        $hasGoodsBackCnt = $shopOrderItem->getHasGoodsBackCnt();
                        if($hasGoodsBackCnt == 0){
                            continue;
                        }
                    ?>
                        <p><?= $shopOrderItem->shopproduct->title ?> <span class="red">退<?= $hasGoodsBackCnt ?><?= $shopOrderItem->shopproduct->pack_unit ?></span></p>
                    <?php } ?>
                    <?php if($shopOrder->refund_amount > 0){ ?>
                        <p>已退款金额<?= $shopOrder->getRefund_amount_yuan()?>(元)</p>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php } ?>
    <table class="table table-bordered table-condensed table-striped" style="text-align:center;">
        <thead>
            <tr>
                <th>药名</th>
                <th>首次服药时间</th>
                <th>更新时间</th>
                <th>剂量</th>
                <th>频次</th>
                <th>状态</th>
            </tr>
        </thead>
        <tbody>
    <?php
    $patientmedicinerefs = PatientMedicineRefDao::getAllListByPatient($patient);
    foreach ($patientmedicinerefs as $a) { ?>
            <tr>
                <td><?= $a->medicine->name?></td>
                <td>
                    <?= $a->first_start_date ?>
                </td>
                <td>
                    <?= substr($a->last_drugchange_date,0,10) ?>
                </td>
                <td>
                    <?= $a->getDrugDose() ?>
                </td>
                <td>
                    <?= $a->drug_frequency ?>
                </td>
                <td>
                    <?php if(1 == $a->status){ ?>
                        <span class="green">用药中</span>
                    <?php } ?>
                    <?php if(0 == $a->status){ ?>
                        <span class="red">已停药</span>
                    <?php } ?>
                </td>
            </tr>
    <?php } ?>
        </tbody>
    </table>
    </div>
</div>
<script>
$(function() {
    // Initialize tooltip
    $('.block-header [data-toggle="tooltip"]').tooltip();
})
</script>
