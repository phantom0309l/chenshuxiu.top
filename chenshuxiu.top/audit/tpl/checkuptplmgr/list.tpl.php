<?php
$pagetitle = "医生检查报告模板 CheckupTpls";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>

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
            <div class="searchBar">
                <a class="btn btn-success" href="/checkuptplmgr/add">新建医生检查报告模板</a>
            </div>
            <div class="searchBar">
                <form class="form-horizontal pr" action="/checkuptplmgr/list" method="get">
                    <div class="form-group">
                        <label class="col-xs-12" for="">医生</label>
                        <div class="col-xs-3">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12" for="title">标题</label>
                        <div class="col-xs-3">
                            <input class="form-control" type="text" id="title" name="title" value="<?=$title?>" placeholder="请输入患者姓名">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                        </div>
                    </div>
                </form>
            </div>
            <form action="/checkuptplmgr/posmodifypost" method="post">
                <div class="table-responsive">
                    <table class="table  table-bordered">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>创建日期</td>
                            <td style="width: 40px">序号</td>
                            <td>医生</td>
                            <td>检查分组</td>
                            <td>ename</td>
                            <td>标题</td>
                            <td>问卷</td>
                            <td>答卷/报告数</td>
                            <td>
                                是否显示
                                <br />
                                在约复诊中
                            </td>
                            <td>是否有问卷</td>
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
                            <td><?= $a->doctor->id ?> <?= $a->doctor->name ?></td>
                            <td><?= $a->groupstr ?></td>
                            <td><?= $a->ename ?></td>
                            <td><?= $a->title ?></td>
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
                                <a target="_blank" href="/xanswersheetmgr/list?xquestionsheetid=<?= $a->xquestionsheetid ?>"><?= $a->getCheckupCnt(); ?></a>
                            </td>
                            <td>
                                <?php if($a->is_in_tkt > 0){ echo "是"; }else{ echo "不是"; }?>
                            </td>
                            <td>
                                <?php if($a->is_in_admin > 0){ echo "有"; }else{ echo "没有"; }?>
                            </td>
                            <td>
                                <a target="_blank" href="/checkuptplmgr/modify?checkuptplid=<?= $a->id ?>">修改</a>
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
				"url" : "/checkuptplmgr/deleteJson",
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
