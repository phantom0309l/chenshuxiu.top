<div class="optaskOneShell">
    <?php $patient = $optask->patient; ?>
    <?php if($patient instanceof Patient){ ?>
        <?php
        $patientname = $patient->name;
        $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
        include $tpl . "/_pagetitle.php"; ?>
        <div class="optaskContent"><?= $optask->content ?></div>
    <?php } ?>
</div>
