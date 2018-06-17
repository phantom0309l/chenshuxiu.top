<style>
.flow-item {
	padding: 5px;
}

.flow-item-title {
	font-size: 16px;
}

.flow-time {
	color: #797979;
	font-size: 12px;
	font-weight: normal;
}

.flow-writer {
	color: #797979;
	font-size: 12px;
	font-weight: normal;
	margin-left: 5px;
}

.flow-content {
	margin-top: 5px;
	padding: 5px;
	font-size: 14px;
}
</style>
<?php
$i = count($wxopmsgs) + 1;
foreach ($wxopmsgs as $a) {
    $i --;
    ?>
<div class="flow-item-wxopmsg offsettime" data-offsetcreatetime="<?=$a->createtime ?>">
    <div class="grayBgColorBox contentBoxTitle">
<?php
    if ($a->auditorid == 0) {
        echo $a->doctor->name . "医生";
    } else {
        echo $a->auditor->name . "医助";
    }
    ?>
        <span class="flow-time"><?=$a->createtime ?></span>
        <span class="flow-writer">回复
<?php
    if ($a->auditorid == 0) {
        echo $a->auditor->name . "医助";
    } else {
        echo $a->doctor->name . "医生";
    }
    ?>
        </span>
        <div class="fr">#<?=$i ?></div>
        <div class="clear"></div>
    </div>
    <div class="flow-content">
	    <?=$a->content?>
    </div>
</div>
<?php
}
?>
