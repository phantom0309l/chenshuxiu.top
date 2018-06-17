<?php
$pagetitle = "医生检查报告模板 CheckupTpls";
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
            <div class="searchBar">
                <a class="btn btn-success" href="/checkuptplmenumgr/adddisease">创建疾病菜单</a>
            </div>
            <form action="/checkuptplmgr/posmodifypost" method="post">
                <div class="table-responsive">
                    <table class="table  table-bordered">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>创建日期</td>
                            <td>疾病</td>
                            <td width=60%>内容</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($checkupTplMenus as $a) {
                        ?>
                        <tr>
                            <td><?= $a->id ?></td>
                            <td><?= substr($a->createtime,0,10) ?></td>
                            <td><?= $a->disease->name ?></td>
                            <td><?= $a->content ?></td>
                            <td><a href="/checkuptplmenumgr/modifydisease?checkuptplmenuid=<?=$a->id?>">修改</a></td>
                        </tr>
                        <?php
                    }
                    ?>
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
