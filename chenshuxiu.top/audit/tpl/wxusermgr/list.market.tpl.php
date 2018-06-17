<?php
$pagetitle = 'wxuser列表';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.register {
	color: #0066ff
}

.saoma {
	color: #0caf2f
}
STYLE;
$pageScript = <<<SCRIPT
	$(function(){
		$("#cleardate").on("click",function(){
			$(".calendar").val('');
			return false;
		});
	});
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<section class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered tdcenter">
        <thead>
            <tr>
                <th width="40">#</th>
                <th width="55">头像</th>
                <th width="80">wxuserid</th>
                <th>服务号</th>
                <th>微信号</th>
                <th>扫码医生</th>
                <th>报到医生</th>
                <th>患者名</th>
                <th>关系</th>
                <th>国家,省,市</th>
                <th width="150">关注时间</th>
                <th>退订</th>
            </tr>
        </thead>
        <tbody>

<?php
foreach ($wxusers as $i => $a) {
    ?>
                    <tr>
                <td><?=$pagelink->getStartRowNum ()+$i ?></td>
                <td>
                    <a target="_blank" href="<?=$a->headimgurl ?>">
                        <img src="<?= $a->getHeadImgPictureSrc(50,50); ?>" />
                    </a>
                </td>
                <td><?= $a->id ?></td>
                <td><?= $a->wxshop->shortname ?></td>
                <td><?= $a->nickname ?></td>
                <td><?= $a->doctor->name ?></td>
						<?php if($a->user->patient instanceof Patient ){?>
							<td><?= $a->user->patient->doctor->name ?></td>
                <td class="black"><?= $a->user->patient->getMaskName()?></td>
						<?php }else{?>
							<td><?= 'patient不存在' ?></td>
                <td class="black"><?= 'patient不存在'?></td>
						<?php }?>
                        <td><?= $a->user->shipstr?></td>
                <td><?= $a->country ?>,<?= $a->province ?>,<?= $a->city ?></td>
                <td><?= $a->subscribe_time ?></td>
                <td><?= $a->subscribe?'':'退订' ?> <?= $a->subscribe?'':$a->unsubscribe_time; ?></td>
            </tr>
                    <?php } ?>
                    <?php if(!empty($pagelink)){?>
                    <tr>
                <td colspan=100 class="pagelink">
                            <?php include $dtpl."/pagelink.ctr.php";  ?>
                        </td>
            </tr>
                    <?php } ?>

                </tbody>
    </table>
    </div>
    </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
