<?php
$pagetitle = "模板列表 FitPageTpl";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/fitpagetplmgr/add">模板新建</a>
            </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>code</td>
                        <td>name</td>
                        <td>实例数</td>
                        <td>元素数</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                	<?php foreach ($fitpagetpls as $a ){ ?>
                    <tr>
                                <td><?=$a->id ?></td>
                                <td><?=$a->code ?></td>
                                <td><?=$a->name ?></td>
                                <td>
                                    <a href="/fitpagemgr/list?fitpagetplid=<?=$a->id ?>"><?=$a->getFitPageCnt(); ?></a>
                                </td>
                                <td>
                                    <a href="/fitpagetplitemmgr/list?fitpagetplid=<?=$a->id ?>"><?=$a->getFitPageTplItemCnt(); ?></a>
                                </td>
                                <td>
                                    <a href="/fitpagetplmgr/modify?fitpagetplid=<?=$a->id ?>">修改</a>
                                    &nbsp;
                                    <a class="delete" data-fitpagetplid="<?=$a->id ?>">删除</a>
                                </td>
                            </tr>
                  <?php } ?>
            	</tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
    	$(".delete").on("click",function(){
    		var me = $(this);
    		var fitpagetplid = me.data("fitpagetplid");

    		var tr = $(this).parents("tr");

    		$.ajax({
    			"type" : "get",
    			"data" : {
    				fitpagetplid : fitpagetplid
    			},
    			"dataType" : "text",
    			"url" : "/fitpagetplmgr/deleteJson",
    			"success" : function(data) {
    				if(data == "success"){
    					alert("成功删除");
    					tr.remove();
    				}else if(data == "fail"){
    					alert("必须先删除实例和元素库中的元素，才能删除模板");
    				}else {
    					alert("未知错误,删除失败");
    				}
    			}
    		});

    		return false;
    	});
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
