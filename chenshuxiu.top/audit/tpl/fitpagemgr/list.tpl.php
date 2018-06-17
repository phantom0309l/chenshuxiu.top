<?php
$pagetitle = "实例列表 FitPage of " . $fitpagetpl->name;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/fitpagemgr/add?fitpagetplid=<?=$fitpagetpl->id ?>">实例新建</a>
            <a class="btn btn-success" href="/fitpagetplmgr/list">返回模板列表</a>
        </div>
        <?php if (empty($fitpages)) { ?>
            	没有实例
        <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
            <thead>
                <tr>
                    <th>id</th>
                    <th>fitpagetpl</th>
                    <th>code</th>
                    <th>元素</th>
                    <th>disease</th>
                    <th>doctor</th>
                    <th>remark</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fitpages as $a ){ ?>
                    <tr>
                            <td><?=$a->id ?></td>
                            <td><?=$a->fitpagetplid ?></td>
                            <td><?=$a->code?></td>
                        <td>
                            <a href="/fitpageitemmgr/list?fitpagetplid=<?=$fitpagetpl->id ?>&fitpageid=<?=$a->id ?>"><?=$a->getFitPageItemCnt(); ?></a>
                        </td>
                        <td><?=$a->disease->name ?></td>
                        <td><?=$a->doctor->name ?></td>
                        <td><?=$a->remark ?></td>
                        <td>
                            <a class="delete" data-fitpageid="<?=$a->id ?>">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
        <?php } ?>

    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
    	$(".delete").on("click",function(){
    		var me = $(this);
    		var fitpageid = me.data("fitpageid");

    		var tr = me.parents("tr");

    		$.ajax({
    			"type" : "get",
    			"data" : {
    				fitpageid : fitpageid
    			},
    			"dataType" : "text",
    			"url" : "/fitpagemgr/deleteJson",
    			"success" : function(data){
    				if(data == "失败"){
    					alert("必须先将实例中的元素删除，才能删除");
    				}else if(data == "成功"){
    					alert("成功删除");
    					tr.remove()
    				}
    			}
    		});

    		return false;
    	});
    });
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
