<?php
$pagetitle = "门诊信息列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.laydate_yms {
	-webkit-box-sizing: content-box;
	box-sizing: content-box;
}

.laydate_bottom {
	-webkit-box-sizing: content-box;
	box-sizing: content-box;
}

.laydate_ym {
	-webkit-box-sizing: initial;
	box-sizing: initial;
}

#laydate_box .laydate_y {
	margin-right: 4px;
}

.showList p:nth-child(odd) {
	background: #f5f5f5;
}

.showList p {
	height: 50px;
	line-height: 50px;
	margin: 0px;
	padding: 0px 10px;
}

.showList .btn {
	margin-top: 8px;
}

.stop-ops {
	height: 32px;
	width: 90px;
	position: relative;
	border: 1px solid #ddd;
	border-radius: 16px;
	background: #fff;
	margin-top: 10px;
}

.stop-ops .ops-inner {
	position: absolute;
	width: 30px;
	height: 30px;
	border: 1px solid #eee;
	background: #fff;
	border-radius: 15px;
}

.start-ops {
	height: 32px;
	width: 90px;
	position: relative;
	border: 1px solid #ddd;
	border-radius: 16px;
	background: #5cc26f;
	margin-top: 10px;
}

.start-ops .ops-inner {
	position: absolute;
	width: 30px;
	height: 30px;
	border: 1px solid #eee;
	background: #fff;
	border-radius: 15px;
	right: 0px;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form action="/scheduletplmgr/listnew" method="get" class="pr">
                    <label for="">按医生姓名：</label>
                    <input type="text" name="doctor_name" value="<?= $doctor_name ?>" />
                    <label>市场负责人：</label>
                    <?= HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),"auditorid_market",$auditorid_market); ?>
                    <input type="submit" value="搜索" />
                </form>
            </div>
            <div class="table-responsive">
                <table class="table border-top-blue table-striped">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>医生姓名</td>
                        <td>所属医院</td>
                        <td>市场负责人</td>
                        <td>出诊时间</td>
                        <td>出诊信息最近更新时间</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($doctors as $a) { ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->hospital->name ?></td>
                        <td><?= $a->marketauditor->name ?></td>
                        <td>
							<?php $_list = $a->getScheduleTpls()?>
 <div>
<?php

foreach ($_list as $_x) {
?>
	<?php if ($_x->status == 1){ ?>
	 <p class="clearfix">
		<?php if ($_x->op_hz == 'temp' && false == $_x->opdateIsPass()){ ?>
		 <span class="fl"><?= $_x->get_hz() ?>(<?= $_x->op_date ?>)<?= $_x->get_wday() ?><?= $_x->get_day_part()?> <?= $_x->get_type() ?>门诊</span>
		<?php } else if ($_x->op_hz == 'interval'){ ?>
		 <span class="fl"><?= $_x->get_hz() ?>(从<?= $_x->op_date ?>起)<?= $_x->get_wday() ?><?= $_x->get_day_part() ?> <?= $_x->get_type() ?>门诊</span>
		<?php } else if ($_x->op_hz == 'weekly'){ ?>
		 <span class="fl"><?= $_x->get_hz() ?><?= $_x->get_wday() ?><?= $_x->get_day_part() ?> <?= $_x->get_type() ?>门诊</span>
		<?php }else{  ?>
		<span class="fl gray">已过期 <?= $_x->get_hz() ?>(<?= $_x->op_date ?>)<?= $_x->get_wday() ?><?= $_x->get_day_part()?> <?= $_x->get_type() ?>门诊</span>
		<?php } ?>
	 </p>
	<?php } ?>
<?php } ?>
 </div>

                        </td>
                        <td class="scheduleTime"><?= $a->lastschedule_updatetime ?></td>
                        <td>
                            <span class="btn btn-primary updateScheduleTime" data-doctorid="<?= $a->id ?>" >完成更新</span>
                            <a href="/scheduletplmgr/listofdoctor?doctorid=<?= $a->id ?>" class="btn btn-success" target="_blank">查看详情</a>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                    <td colspan=7>
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
	</div>
	<div class="clear"></div>
<?php
$footerScript = <<<XXX
	$(function(){
		$(".updateScheduleTime").on("click", function(){
			var me = $(this);
			var doctorid = me.data("doctorid");
			$.ajax({
				url: '/scheduletplmgr/updateScheduleTimeJson',
				type: 'post',
				dataType: 'text',
				data: {doctorid: doctorid}
			})
			.done(function(data) {
				me.parents("tr").find(".scheduleTime").text(data);
				me.text("已更新");
			})
			.fail(function() {
			})
			.always(function() {
			});

		})
	})
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
