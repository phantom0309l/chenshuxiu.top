<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    include $tpl . "/_pagetitle.php"; ?>
    <div class="optaskContent">
        <?php if($mydisease->id == 1 ){?>
            <a href="/usermgr/listforpatient" target="_blank" class="btn btn-default">去审核</a>
        <?php }?>
    </div>
</div>
