<div class="optaskOneShell">
    <?php
    $optaskpiperefs = OpTaskPipeRefDao::getListByOptaskid($optask->id);
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    include $tpl . "/_pagetitle.php";
    ?>
    <div class="optaskContent"><?= $optask->content ?></div>
<?php if (!empty($optaskpiperefs)) { ?>
<style>
.flow-item h4 {
	background: #F5F5F5;
	padding: 8px 0 8px 10px;
	border-top: 1px solid #ccc;
}

.flow-item-title {
	position: relative;
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

.flow-writer .wxshop-name {
	color: #006699;
}

.flow-pipeevent-title {
	color: #0000ff;
	font-size: 12px;
	font-weight: normal;
	margin-left: 5px;
}

.flowContentBox {
	margin: 5px 0px;
	padding: 10px;
	background: #fAfAfB;
}

.replyBox {
	margin-top: 10px;
}

.pipeeventBoxShell {
	position: absolute;
	right: 10px;
	top: 0px;
	font-size: 12px;
	font-weight: normal;
}

.pipeeventBox {
	position: absolute;
	width: 400px;
	padding: 20px;
	border: 1px solid #ddd;
	right: 0px;
	top: 0px;
	z-index: 10;
	background: #fff;
}

.pipeRemark {
	margin-top: 10px;
	padding: 5px;
	border: 1px solid #ffe86b;
	background: #fff2b0;
}
</style>
    <div>
    [_follow.php]
        <?php
    foreach ($optaskpiperefs as $_a) {
        $a = $_a->pipe;
        if (! file_exists($tpl . "/pipemgr/_obj/_" . $a->objtype . ".php")) {
            continue;
        }
        if ('DrugItem' == $a->objtype && 1 != $mydisease->id) {
            continue;
        }
        ?>

            <div class="flow-item" data-pipeid="<?= $a->id ?>" data-openid="<?= $a->getWxUserForPushMsg()->openid ?>" data-offsetpipeid="<?= $a->id ?>" data-offsetpipetime="<?= $a->createtime ?>">
            <h4 class="flow-item-title">
                <?= $a->pipetpl->title; ?>
                <span class="flow-time"><?= $a->createtime ?></span>
                <span class="flow-writer"><?= $a->objtype == "PushMsg" ? "回复给" : "填写人" ?>:<?= $a->user->shipstr?>
                (<span class="wxshop-name"><?= $a->wxuser->wxshop->shortname; ?></span>
                    )
                </span>
            </h4>
            <div class="flowContentBox11">
        <?php
        $_pipe_file = $tpl . "/pipemgr/_obj/_" . $a->objtype . ".php";
        if (file_exists($_pipe_file)) {
            include $_pipe_file;
        } else {
            include $tpl . "/pipemgr/_obj/_xobjtype.php";
        }
        ?>
                    <?php if( $a->content ){ ?>
                    <div class="pipeRemark"><?= $a->content ?></div>
                    <?php } ?>
                </div>
        </div>
        <?php } ?>
    </div>
<?php } ?>
</div>
