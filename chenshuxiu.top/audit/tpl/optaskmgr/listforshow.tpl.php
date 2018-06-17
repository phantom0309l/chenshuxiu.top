<?php
$pagetitle = '运营系统首页';
$pagetitle = "任务列表 Optasks";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form action="/optaskmgr/listforshow" method="get" class="pr">
                    <div class="mt10">
                        <label>按类型筛选：</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getOptaskTplCtrArray(),"optasktplid",$optasktplid,'f18');?>
                    </div>
                    <div class="mt10">
                        <label>按关闭任务人员筛选：</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getYunyingAuditorCtrArray(),"auditorid_yunying",$auditorid_yunying,'f18');?>
                    </div>
                    <div class="mt10">
                        <label for="">按患者名模糊查找：</label>
                        <input type="text" name="patient_name" value="<?= $patient_name ?>" />
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="组合筛选" />
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>创建日期</td>
                        <td>微信名</td>
                        <td>姓名</td>
                        <td>任务类型</td>
                        <td>内容</td>
						<td>是否已关闭</td>
						<td>责任人</td>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($optasks as $a) {
						?>
						<tr>
							<td><?= $a->getCreateDay() ?></td>
							<td><?= $a->wxuser->nickname ?></td>
							<td><?= $a->patient->name ?></td>
							<td><?= $a->title ?> </td>
							<td><?= $a->content ?></td>
							<td><?= $a->status ? '关闭' : '未关闭'  ?></td>
							<td><?= $a->auditor->name ?></td>
						</tr>
					<?php } ?>
						<tr>
						<td colspan=10>
						<?php include $dtpl . "/pagelink.ctr.php"; ?>
						</td>
						</tr>
				</tbody>
				</table>
            </div>
			</section>
	</div>
	<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
                                                                                                                                   1,1           Top
