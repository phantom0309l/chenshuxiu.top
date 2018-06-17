<div style="margin-bottom:10px;">
    <span class="btn btn-primary triggerAddDrugBtn">添加用药</span>
    <?php if( null == $patient->isNoDruging() ){ ?>
    <span class="btn btn-primary noDrugBtn">标记为不服药</span>
    <? }else{ ?>
    <span class="btn btn-primary noDrugBtn">继续不服药</span>
        <?php include_once $tpl . "/patientmgr/_drugsheet.php"; ?>
    <? } ?>
</div>

<?php include_once $tpl . "/patientmgr/_adddrug_box_shell.php"; ?>
