<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    $patient = $optask->patient;

    include $tpl . "/_pagetitle.php"; ?>
    <div class="optaskContent">
        <?php include $tpl . "/_set_medicine_break_date.php";  ?>
    </div>
</div>
