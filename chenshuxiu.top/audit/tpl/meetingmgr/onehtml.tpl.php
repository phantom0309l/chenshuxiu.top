<div class="border1-blue mt20">
    <div>
        <div class="mt20 contentBoxTitle">
            <span class="f16">电话录音 of <?= $patient->name?></span>
        </div>
        <?php
        foreach ($meetings as $a) {
            include $tpl . "/pipemgr/_obj/_Meeting.php";
        }
        ?>
    </div>
</div>
