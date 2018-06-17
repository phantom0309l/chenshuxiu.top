<?php
$pagetitle = "模板元素列表 FitPageTplItems Of " . $fitpagetpl->code;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
                <div class="searchBar">
                    <a class="btn btn-success" href="/fitpagetplitemmgr/add?fitpagetplid=<?=$fitpagetpl->id ?>">模板元素新建</a>
                    <a class="btn btn-success" href="/fitpagetplmgr/list">返回模板列表</a>
                </div>
                <?php if (empty($fitpagetplitems)) { ?>
                	没有模板元素
                <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>pos</th>
                                <th>code</th>
                                <th>name</th>
                                <th>content</th>
                                <th>remark</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fitpagetplitems as $a ){ ?>
                                <tr>
                                    <td><?=$a->id ?></td>
                                    <td><?=$a->pos ?></td>
                                    <td><?=$a->code ?></td>
                                    <td><?=$a->name ?></td>
                                    <td><?=$a->content ?></td>
                                    <td><?=$a->remark ?></td>
                                    <td>
                                        <a href="/fitpagetplitemmgr/modify?fitpagetplitemid=<?=$a->id ?>">修改</a>
                                        &nbsp;
                                        <a class="delete" data-fitpagetplitemid="<?=$a->id ?>">删除</a>
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
            var fitpagetplitemid = me.data("fitpagetplitemid");

            var tr = $(this).parents("tr");

            $.ajax({
                "type" : "get",
                "data" : {
                    fitpagetplitemid : fitpagetplitemid
                },
                "dataType" : "text",
                "url" : "/fitpagetplitemmgr/deleteJson",
                "success" : function(data) {
                    if(data == "失败"){
                        alert("该元素正在使用中,不能删除");
                    }else if(data == "成功"){
                        alert("成功删除");
                        tr.remove();
                    }
                }
            });

            return false;
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
