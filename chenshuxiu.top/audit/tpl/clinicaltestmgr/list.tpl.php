<?php
$pagetitle = "临床试验列表";
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
                <a class="btn btn-sm btn-primary" href="/clinicaltestmgr/add">
                    <i class="fa fa-plus push-5-r"></i>新建临床试验
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>创建时间</th>
                        <th>标题</th>
                        <th>列表标题</th>
                        <th>简介</th>
                        <th>状态</th>
                        <th width="90px" class="tc">操作</th>
                    </tr>
                    </thead>
                    <tbody class="">
                    <?php foreach ($clinicaltests as $clinicaltest) { ?>
                        <tr>
                            <td><?= $clinicaltest->id ?></td>
                            <td><?= $clinicaltest->createtime ?></td>
                            <td><?= $clinicaltest->title ?></td>
                            <td><?= $clinicaltest->list_title ?></td>
                            <td><?= $clinicaltest->brief ?></td>
                            <td>
                                <?php $status_class = $clinicaltest->status ? 'success' : 'danger'; ?>
                                <span class="label label-<?= $status_class ?>"><?= $clinicaltest->getStatusStr() ?></span>
                            </td>
                            <td width="90px" class="tc">
                                <div class="btn-group">
                                    <a class="btn btn-xs btn-default" type="button" data-toggle="tooltip"
                                       title="" data-original-title="查看"
                                       target="_blank"
                                       href="<?= $wx_uri ?>/clinicaltest/one?clinicaltestid=<?= $clinicaltest->id ?>">
                                        <i class="fa fa-search"></i>
                                    </a>
                                    <a class="btn btn-xs btn-default" type="button" data-toggle="tooltip"
                                       title="" data-original-title="修改"
                                       target="_blank"
                                       href="/clinicaltestmgr/modify?clinicaltestid=<?= $clinicaltest->id ?>">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <button class="btn btn-xs btn-default J_delete" type="button" data-toggle="tooltip"
                                            title="" data-original-title="删除"
                                            data-clinicaltestid="<?= $clinicaltest->id ?>"><i class="fa fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
	$(function(){
		$(document).on("click",".J_delete",function(){
			if(false == confirm("确定删除吗？")){
				return false;
			}
			var clinicaltestid = $(this).data('clinicaltestid');
			$.ajax({
				"type" : "post",
				"data" : {
					clinicaltestid : clinicaltestid
				},
				"dataType" : "json",
				"url" : "/clinicaltestmgr/ajaxdeletepost",
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
