<?php
$pagetitle = "关注列表 / 微信列表 WxUsers";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
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

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<div class="col-md-12">
    <section class="col-md-12">
        <form action="/wxusermgr/list" method="get" class="pr">
            <div class="searchBar">
                <label>微信名模糊搜索:</label>
                <input name="nickname" value="<?=$nickname ?>">
                <input type="submit" class="btn_style4" value="搜索">
            </div>
        </form>
        <form action="/wxusermgr/list" method="get" class="pr">
            <div class="searchBar">
                <div>
                    <label>医院: </label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toHospitalCtrArray($hospitals,true),"hospitalid",$hospitalid ,"f18"); ?>
                        &nbsp;
                    	<label>市场负责人: </label>
                       	<?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),"auditorid_market",$auditorid_market ,"f18");?>
            		</div>
                <div class="mt10">
                    <div class="col-md-4" style="width: 330px;margin-top: 8px;padding-right: 0px;padding-left: 0px">
                        <label>医生:(如果选了医生, 疾病,医院和市场负责人选择失效) </label>
                    </div>
                    <div class="col-md-3">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                </div>
                <div class="mt10">
                    <label>市场推广人员:</label>
            			<?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),"auditorid_expand",$auditorid_expand ,"f18");?>
            		</div>
                <div class="mt10">
                    <label>关注时间: </label>
                    从
                    <input type="text" class="calendar" style="width: 100px" name="fromdate" value="<?= $fromdate ?>" />
                    到
                    <input type="text" class="calendar" style="width: 100px" name="todate" value="<?= $todate ?>" />
                    (左闭右开)
                    <button class="btn_style4" id="cleardate">清空日期</button>
                </div>
                <div class="mt10">
                    <label>服务号:</label>
                    	<?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getWxShopCtrArray(true),"wxshopid",$wxshopid ,"f18");?>
                    </div>
                <div class="mt10">
                    <label>关注: </label>
                    <?php
                    $arr = array(
                        'all' => '全部',
                        'yes' => '未退订',
                        'no' => '已退订');
                    echo HtmlCtr::getRadioCtrImp($arr, 'subscribe', $subscribe, '');
                    ?>
                    </div>
                <div class="mt10">
                    <label>医生类别: </label>
                    <?php
                    $arr = array(
                        'all' => '全部',
                        'valid' => '有效医生',
                        'invalid' => '无效医生',
                        'null' => '空医生');
                    echo HtmlCtr::getRadioCtrImp($arr, 'doctortype', $doctortype, '');
                    ?>
                    </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value=" 组合筛选 ">
                </div>
            </div>
        </form>
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
                        <td style="text-align: left">
                            <?= $a->doctor->name ?>
                            <br />
                            <span class="gray f10">
                            <?= $a->doctor->hospital->name ?>
                            </span>
                        </td>
                        <td style="text-align: left">
							<?php
    $_master_doctor = null;
    if ($a->user->patient instanceof Patient) {
        $_master_doctor = $a->user->patient->doctor;
        echo $_master_doctor->name;
    }
    ?>
                            <br />
                            <span class="gray f10">
                            <?= $_master_doctor->hospital->name ?>
                            </span>
                        </td>
                        <td class="black">
<?php
    if ($a->user->patient instanceof Patient) {
        echo $a->user->patient->getMaskName();
    }
    ?>
                        </td>
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
