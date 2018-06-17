<?php
$pagetitle = "疾病分组列表 DiseaseGroup";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/diseasegroupmgr/add">疾病分组新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" style="border-top: 1px solid #ccc; margin-top: 10px;">
            <thead>
                <tr>
                    <td>DiseaseGroupId</td>
                    <td>创建日期</td>
                    <td>疾病组名</td>
                    <td width=200>操作</td>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($diseasegroups as $a) {?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->name ?></td>
                    <td>
                        <a href="/diseasegroupmgr/modify?diseasegroupid=<?= $a->id ?>" class="btn btn-default">修改</a>
                        <span class="deleteBtn btn btn-default" data-diseasegroupid="<?= $a->id ?>">删除</span>
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
        $(".deleteBtn").on("click", function(){
            var me = $(this);
            var diseasegroupid = me.data("diseasegroupid");
            if( confirm("确定要删除吗？") ){
                $.ajax({
                    url: '/diseasegroupmgr/deleteJson',
                    type: 'post',
                    dataType: 'json',
                    data: {diseasegroupid: diseasegroupid}
                })
                .done(function(data) {
                    if (data.errno == 0) {
                        alert("已删除!");
                        window.location.href = window.location.href;
                    } else {
                        alert("删除失败!");
                    }
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
            }
        })
    })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
