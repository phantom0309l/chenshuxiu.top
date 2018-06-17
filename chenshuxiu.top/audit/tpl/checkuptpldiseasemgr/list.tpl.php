<?php
$pagetitle = "疾病检查报告模板 CheckupTpls";
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
            <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                    <a class="btn btn-sm btn-primary" target="_blank" href="/checkuptpldiseasemgr/add">
                        <i class="fa fa-plus push-5-r"></i>疾病检查报告模板新建
                    </a>
                </div>

                <div class="col-sm-11 col-xs-9">
                    <div class="col-sm-3" style="float: right; padding-right: 0px;">
                        <form class="form-horizontal push-5-t" action="/checkuptpldiseasemgr/list" method="get">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索标题" name="title" class="input-search form-inline form-control" value="<?=$title?>">
                                    <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                        <button type="submit" class="btn btn-primary">
                                            <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search">
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clear">

                </div>
            </div>
            <div class="col-md-12">
                <form action="/checkuptpldiseasemgr/posmodifypost" method="post">
                <div class="table-responsive">
                    <table class="table  table-bordered">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>创建日期</td>
                            <td style="width: 40px">序号</td>
                            <td>检查分组</td>
                            <td>标题</td>
                            <td>ename</td>
                            <td>问卷</td>
                            <td>答卷</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($checkuptpls as $a) {
                        ?>
                        <tr>
                            <td><?= $a->id ?></td>
                            <td><?= substr($a->createtime,0,10) ?></td>
                            <td>
                                <input type="text" name='pos[<?=$a->id ?>]' value="<?= $a->pos ?>" style="width: 40px" />
                            </td>
                            <td><?= $a->groupstr ?></td>
                            <td><?= $a->title ?></td>
                            <td><?= $a->ename ?></td>
                            <td>
                                <?php
                        if ($a->xquestionsheetid > 0) {
                            $_url = "/xquestionsheetmgr/one?xquestionsheetid={$a->xquestionsheetid}";
                            $_name = "{$a->xquestionsheet->title}({$a->xquestionsheet->getQuestionCnt() }个问题)";
                        } else {
                            $_url = "/xquestionsheetmgr/add?objtype=CheckupTpl&objid={$a->id}&sn={$a->ename}&title={$a->title}";
                            $_name = "添加问题";
                        }
                        ?>
                                <a target="_blank" href="<?= $_url?>"><?=$_name ?></a>
                            </td>
                            <td>
                                <?php if($a->xquestionsheetid > 0){ ?>
                                    <a href="/xanswersheetmgr/list?xquestionsheetid=<?= $a->xquestionsheetid ?>"><?= $a->xquestionsheet->getAnswerSheetCnt() ?>份答卷</a>
                                <?php }?>
                            </td>
                            <td>
                                <a target="_blank" href="/checkuptpldiseasemgr/modify?checkuptplid=<?= $a->id ?>">修改</a>
                                <a class="delete" data-checkuptplid="<?=$a->id ?>">删除</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                            <td colspan=20 align=right>
                                <input type="submit" value="保存序号修改" />
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </form>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
	$(function(){
		$(document).on("click",".delete",function(){
			var checkuptplid = $(this).data('checkuptplid');

			var tr = $(this).parents("tr");
			if(false == confirm("确定删除吗？")){
				return false;
			}
			$.ajax({
				"type" : "get",
				"data" : {
					checkuptplid : checkuptplid
				},
				"dataType" : "html",
				"url" : "/checkuptpldiseasemgr/deleteJson",
				"success" : function(data) {
					if (data == 'success') {
						alert("删除成功");
						tr.remove();
					}else {
						alert("未知错误");
					}
				}
			});
		});
	});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
