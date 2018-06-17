<?php if($pipes && is_array($pipes)) {?>
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

.reply-msg {
	border: 1px solid #ddd;
	padding: 5px;
	width: 100%;
	border-radius: 3px;
}
</style>
<input type="hidden" id="wxuserid" value="<?= $wxuser->id ?>" />
<input type="hidden" id="patientName" value="<?= $wxuser->nickname ?>" />
<input type="hidden" id="doctor_name" value="<?= $wxuser->doctor->name ?>" />
<input type="hidden" id="disease_name" value="<?= $wxuser->wxshop->disease->name ?>" />
<?php
    foreach ($pipes as $a) {
        ?>
<div class="flow-item block block-bordered" data-pipeid="<?= $a->id ?>" data-openid="<?= $a->wxuser->openid ?>" data-offsetpipeid="<?= $a->id ?>" data-offsetpipetime="<?= $a->createtime ?>">
    <div class="block-header bg-gray-lighter">
        <h3 class="block-title">
                <?= $a->getPipeTplTitle(); ?>
                <span class="flow-time push-20-l"><?= $a->createtime ?></span>
                    <?php if($a->objtype == "PushMsg"){ ?>
                <span class="flow-writer">回复给:<?= $a->user->shipstr?>
                     (<?= $a->wxuser->wxshop->shortname; ?> : <span class="wxshop-name"><?= $a->wxuser->nickname; ?></span>
                )
            </span>
                    <?php }else{ ?>
                <span class="flow-writer">填写人:<?= $a->getWriter()?>
                     (<?= $a->wxuser->wxshop->shortname; ?> : <span class="wxshop-name"><?= $a->wxuser->nickname; ?></span>
                )
            </span>
                    <?php } ?>
                <?php if (false == in_array($a->objtype, array("PushMsg","WxOpMsg"))) {?>
                <ul class="block-options">
                <li>
                    <button type="button" class="reply-triggerBtn">
                        <i class="si si-action-redo"></i>
                    </button>
                </li>
            </ul>
                <?php }?>
            </h3>
    </div>
    <div class="block-content pb10">
        <div class="flowContentBox11 clearfix">
        <?php
        $_pipe_file = dirname(__FILE__) . "/_obj/_" . $a->objtype . ".php";
        if (file_exists($_pipe_file)) {
            include $_pipe_file;
        } else {
            include dirname(__FILE__) . "/_obj/_xobjtype.php";
        }
        ?>

            <?php if( $a->content ){ ?>
            <div class="pipeRemark"><?= $a->content ?></div>
            <?php } ?>
            </div>
    	    <?php

        if (false == in_array($a->objtype, array(
            "PushMsg",
            "WxOpMsg"))) {
            include dirname(__FILE__) . "/_replyofwxuser.php";
        }
        ?>
        </div>
</div>
<?php } ?>
</div>
<?php }?>
