<div class="optaskOneShell">
<?php
$patient = $optask->patient;
$patientnote = $optask->obj;
$pagetitle = "{$patient->name}{$optask->optasktpl->title}详情";
include $tpl . "/_pagetitle.php"; ?>
<div class="patientworkShell">
    <?php if ($patientnote instanceof PatientNote) { ?>
        <p><?= $patientnote->content ?></p>
    <?php } ?>
</div>
</div>
