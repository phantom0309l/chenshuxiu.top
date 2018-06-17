<div class="optaskOneShell">
    <?php
    $doctorComment = $optask->obj;
    $content = '';
    if ($doctorComment instanceof DoctorComment) {
        $content = $doctorComment->content;
    }
    ?>
    <div class="optaskContent">
        <?= $content ?>
    </div>
</div>
