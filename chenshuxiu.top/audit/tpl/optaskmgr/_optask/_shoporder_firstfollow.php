<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    include $tpl . "/_pagetitle.php"; ?>
    <div class="optaskContent">
        <?= $optask->content ?>
    </div>
</div>
