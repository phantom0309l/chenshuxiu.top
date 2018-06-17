<div class="optaskOneShell">
    <?php
    $patient = $optask->patient;
    $report = $optask->obj;
    if ($report instanceof Report) {
        ?>
        <div class="optaskContent">
            <a href="/reportmgr/listbypatient?patientid=<?= $report->patientid ?>&doctorid=<?= $report->doctorid ?>&reportid=<?= $report->id ?>" target="_blank">查看</a>
        </div>
    <?php } ?>
</div>
