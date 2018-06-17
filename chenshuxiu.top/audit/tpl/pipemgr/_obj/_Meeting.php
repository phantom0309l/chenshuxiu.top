<div class="border1-blue">
    <?php
    if ($a instanceof Meeting) {
        $meeting = $a;
    } else {
        $meeting = $a->obj;
    }
    ?>
    <div>呼叫类型：
        <span style="float:right">云通信</span>
    </div>
    <div>呼叫时间：<?= $meeting->formatStartTime() ?> </div>
    <div>挂机时间：<?= $meeting->formatEndTime() ?> </div>
    <div>通话时长：<?= $meeting->formatDuration() ?> </div>
    <br />
    <div>
        <audio src="<?= $meeting->getVoiceUrl() ?>" controls="controls">
        </audio>
    </div>
</div>
