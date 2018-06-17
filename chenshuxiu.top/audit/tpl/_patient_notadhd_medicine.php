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
    <?php if(count($shopOrders > 0)){ ?>
    <table class="table table-bordered">
        <tr>
            <td>购药日期</td>
            <td>购药详情</td>
        </tr>
        <?php foreach($shopOrders as $shopOrder){ ?>
            <tr>
                <td><?= substr($shopOrder->time_pay, 0, 10) ?></td>
                <td>
                    <?php foreach($shopOrder->getShopOrderItems() as $shopOrderItem){ ?>
                        <p><?= $shopOrderItem->shopproduct->title ?> <span class="red"><?= $shopOrderItem->cnt ?><?= $shopOrderItem->shopproduct->pack_unit ?></span></p>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php } ?>
    <?php
        $colorArr = [
            -1 => '',
            1 => 'text-success',
            2 => 'text-warning',
            3 =>'text-danger',
        ];
        $iconArr = [
            -1 => 'fa fa-clock-o',
            0 => 'si si-question',
            1 => 'fa fa-check-circle',
            2 => 'si si-close',
            3 => 'fa fa-circle-thin',
        ];
        $cond = ' AND patientid=:patientid AND doctorid=:doctorid';
        $bind = [
            ':patientid' => $patient->id,
            ':doctorid' => $patient->doctorid,
        ];
        $pmTargets = Dao::getEntityListByCond('PatientMedicineTarget', $cond, $bind);
    ?>
    <table class="table table-hover optask-pmtarget-table">
        <thead>
        <tr><th>药名</th><th>首次<br/>用药时间</th><th>最新<br/>用药时间</th><th>剂量</th><th>频次</th><th>状态</th></tr>
        </thead>
        <tbody class="">
        <?php foreach ($pmTargets as $pmTarget) {?>
            <tr>
            <td><?=$pmTarget->medicine->name?></td>
            <td><?=$pmTarget->getOldestDrugTime()?></td>
            <td><?=$pmTarget->getNewestDrugTime()?></td>
            <td>
                <?=$pmTarget->drug_dose?>
                <i class="si si-info text-info" data-toggle="popover" data-placement="right" data-content="<?=$pmTarget->drug_change?>" data-original-title="调药规则"></i>
            </td>
            <td>
                <?=$pmTarget->drug_frequency?>
            </td>
            <td>
            <?php $drugStatus = $pmTarget->getNewestDrugStatus();?>
            <span class="<?=$colorArr[$drugStatus]?> push-5-r">
            <?php
            $statusDesc = $pmTarget->getDrugStatusDesc($drugStatus);
            echo $statusDesc;
            ?>
            </span>
            <?php $newestPmsItem = $pmTarget->getNewestPmsItem(); ?>
            <?php if ($newestPmsItem && $newestPmsItem->auditremark) { ?>
                <i class="si si-info text-info" data-toggle="popover" data-placement="left" data-content="<?=$newestPmsItem->auditremark?>" data-original-title=""></i>
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
    // Initialize Popovers
    $('.optask-pmtarget-table [data-toggle="popover"]').popover({
        container: 'body',
        animation: true,
        trigger: 'hover'
    });
    $('.block-header [data-toggle="tooltip"]').tooltip();
})
</script>
