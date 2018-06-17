<?php
$pagetitle = "腾讯OCR接口测试 OCRText";
$cssFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170829',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170829'
]; //填写完整地址
?>

<style>
.flow-item {
	position: relative;
}

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
<input type="hidden" id="patientName" value="<?= $patient->name ?>" />
<input type="hidden" id="doctor_name" value="<?= $patient->doctor->name ?>" />
<input type="hidden" id="disease_name" value="<?= $patient->disease->name ?>" />
<?php
if (empty($pipes) && ! empty($pipeeventid)) {
    echo "该标签还未使用!";
    return;
}
?>
<?php if (! empty($pipes)) { ?>
    <div>
        <?php
            foreach ($pipes as $a) {
                // 跳过, 非多动症的 DrugItem
                if ('DrugItem' == $a->objtype && 1 != $mydisease->id) {
                    continue;
                }
                ?>
                    <div class="block block-bordered flow-item remove-padding" data-pipeid="<?= $a->id ?>" data-openid="<?= $a->getWxUserForPushMsg()->openid ?>" data-offsetpipeid="<?= $a->id ?>" data-offsetpipetime="<?= $a->createtime ?>">
                        <div class="block-header bg-gray-lighter">
                            <span class="font-s13 font-w500"><?= $a->getPipeTplTitle(); ?></span>
					        <span class="flow-time"><?= $a->getPipeCreatetime() ?></span>
                            <?php if ($a->objtype == "PushMsg") { ?>
                                <span class="flow-writer">回复给:<?= $a->user->shipstr?>
                                    (<?= $a->wxuser->wxshop->shortname; ?> : <span class="wxshop-name"><?= $a->wxuser->nickname; ?></span>)
                    			</span>
                    		<?php } else { ?>
                    			<span class="flow-writer">填写人:<?= $a->getWriter()?>
                                    (<?= $a->wxuser->wxshop->shortname; ?> : <span class="wxshop-name"><?= $a->wxuser->nickname; ?></span>)
                    			</span>
                    		<?php }?>
                		</div>
                    	<div class="block-content" <?php if (false == in_array($a->objtype, array("PushMsg","WxOpMsg"))) {?> style="margin-bottom: 43px;" <?php }?>>
                        	<div class="flowContentBox11 clearfix">
                                <?php
                                    $_pipe_file = dirname(__FILE__) . "/_obj/_" . $a->objtype . ".php";
                                    if (file_exists($_pipe_file)) {
                                        include $_pipe_file;
                                    } else {
                                        include dirname(__FILE__) . "/_obj/_xobjtype.php";
                                    }
                                    ?>
                                    <?php if ($a->content) { ?>
                                        <div class="pipeRemark">
                                        	<?= $a->content ?>
                                       	</div>
                                    <?php } ?>
                            </div>
                            <?php
                                if (false == in_array($a->objtype, array(
                                    "PushMsg",
                                    "WxOpMsg"))) {
                                    include dirname(__FILE__) . "/_reply.php";
                                }
                            ?>
                            <p></p>
                		</div>
                		<div class="clearfix"></div>
        			</div>
            	<?php
            }
        ?>
    </div>
<?php } ?>
