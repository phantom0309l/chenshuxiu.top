<div class="optaskOneShell">
    <?php $patient = $optask->patient; ?>
    <?php if($patient instanceof Patient){ ?>
        <?php
        $patientname = $patient->name;
        $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
        include $tpl . "/_pagetitle.php";
        $arr = json_decode($optask->content, true);
        ?>
        <div class="optaskContent">问题：<?= $arr["question"] ?></div>
        <div class="optaskContent">患者选择的答案：<?= $arr["answer"] ?></div>
    <?php } ?>
</div>
