<style>
    .tab-content .form-group label {
        width: auto;
    }
</style>

<div class="optaskOneShell">
    <?php
    $clinicaltest = $optask->obj;
    if ($clinicaltest instanceof ClinicalTest) { ?>
        <div class="optaskContent">
            <h5>
                <?= $clinicaltest->title ?>
            </h5>
        </div>
    <?php } ?>
</div>
<script>
</script>