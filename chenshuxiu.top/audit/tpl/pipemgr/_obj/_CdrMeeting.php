<div class="bg-gray-lighter" style="padding:10px;">
    <?php
    if ($a instanceof CdrMeeting) {
        $cdrMeeting = $a;
    } else {
        $cdrMeeting = $a->obj;
    }
    ?>
<div class="collapse">
    <?=$cdrMeeting->id . " | " . $cdrMeeting->cdr_main_unique_id?>
</div>
    <div>
        <?php
            $inputs = $cdrMeeting->getInputStatusArr();//客户呼入
            $outputs = $cdrMeeting->getOutputStatusArr();//运营呼出
            echo "呼叫类型：";
            if (in_array($cdrMeeting->cdr_call_type, $inputs)) {
                echo "<span class='text-danger'>患者呼入</span>";
            } else {
                echo "运营呼出";
            }
        ?>
        <span style="float:right">天润融通</span>
    </div>
<?php if ($cdrMeeting->auditorid) {?>
    <?php if($cdrMeeting->cdr_call_type == 1) { ?>
        <div>接听坐席：<?= $cdrMeeting->auditor->name ?> </div>
    <?php } else if ($cdrMeeting->cdr_call_type == 3 || $cdrMeeting->cdr_call_type == 4) { ?>
        <div>呼出坐席：<?= $cdrMeeting->auditor->name ?> </div>
    <?php } ?>
<?php } ?>
    <div>呼叫时间：<?= $cdrMeeting->formatStartTime() ?></div>
    <div>挂机时间：<?= $cdrMeeting->formatEndTime() ?> </div>
<?php if ($cdrMeeting->isCallOk()) { ?>
    <div>通话时长：<?= $cdrMeeting->formatDuration() ?> </div>
    <br />
    <?php if ($cdrMeeting->needDownloadVoiceFile()) { ?>
            <div class="btn btn-success download-cdr" data-cdrmeetingid="<?=$cdrMeeting->id?>">下载录音</div>
    <?php } ?>
    <div>
        <audio src="<?= $cdrMeeting->getVoiceUrl() ?>" controls="controls"></audio>
    </div>
<?php } else { ?>
    <p class="text-danger"><?= $cdrMeeting->getCallResultDesc() ?></p>
<?php } ?>
</div>
<div class="cdr-meeting-box" style="border: 1px solid #f0f0f0;padding: 5px;"></div>