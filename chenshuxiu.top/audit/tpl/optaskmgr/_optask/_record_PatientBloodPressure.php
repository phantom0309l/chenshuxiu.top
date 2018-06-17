<div class="optaskOneShell">
    <?php
    $pbp = $optask->obj;
    if ($pbp instanceof PatientBloodPressure) {
        ?>
        <div class="optaskContent">
            <p>收缩压：<?= $pbp->high ?></p>
            <p>舒张压：<?= $pbp->low ?></p>
        </div>
    <?php } ?>
</div>
