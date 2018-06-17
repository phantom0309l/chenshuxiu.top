<?php
$pagetitle = "药品不良反应监测规则";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12 mb20">
            <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                <a class="btn btn-sm btn-primary" href="/adrmonitorrulemgr/add">
                    <i class="fa fa-plus push-5-r"></i>新建药品不良反应监测规则
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="js-table-sections table table-hover">
                    <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>药品通用名称</th>
                        <th>药品名称</th>
                        <th>疾病</th>
                        <th class="tc">周区间</th>
                        <th class="tc">间隔周期</th>
                        <th>监测项目</th>
                        <th width="70px" class="tc">操作</th>
                    </tr>
                    </thead>
                    <?php foreach ($adrmonitorrules as $adrmr) { ?>
                        <tbody class="js-table-sections-header open">
                        <tr>
                            <td class="tc">
                                <i class="fa fa-angle-right"></i>
                            </td>
                            <td><?= $adrmr->medicine_common_name ?></td>
                            <td><?= $adrmr->medicine->name ?></td>
                            <td><?= $adrmr->disease->name ?></td>
                            <td colspan="3"></td>
                            <td width="70px" class="tc">
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-default J_modify" type="button" data-toggle="tooltip"
                                            title="" data-original-title="修改"
                                            data-adrmrid="<?= $adrmr->id ?>"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-xs btn-default J_delete" type="button" data-toggle="tooltip"
                                            title="" data-original-title="删除"
                                            data-adrmrid="<?= $adrmr->id ?>"><i class="fa fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tbody>
                        <?php foreach ($adrmr->getItems() as $item) { ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>

                                <td></td>
                                <td class="tc">[ <?= $item->week_from ?>
                                    , <?= $item->week_to == 99999 ? '∞' : $item->week_to ?> )
                                </td>
                                <td class="tc"><?= $item->week_interval ?></td>
                                <td><?= ADRMonitorRuleItem::getItemStr($item->ename) ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    <?php } ?>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
	$(function(){

	    $('.J_modify').on('click', function() {
	        var adrmrid = $(this).data('adrmrid');
	        window.location.href = "/adrmonitorrulemgr/modify?adrmonitorruleid=" + adrmrid;
	    })

		$(document).on("click",".J_delete",function(){
			var adrmrid = $(this).data('adrmrid');

			if(false == confirm("确定删除吗？")){
				return false;
			}
			$.ajax({
				"type" : "post",
				"data" : {
					adrmonitorruleid : adrmrid
				},
				"dataType" : "json",
				"url" : "/adrmonitorrulemgr/ajaxdeletepost",
				"success" : function(d) {
                    if (d.errno == 0) {
                        alert("删除成功");
						window.location.reload();
                    } else {
                        alert(d.errmsg);
                    }
				}
			});
		});
	});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
