<?php
$pagetitle = "列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/pipetplmgr/add">新建</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>題目</td>
                        <td>objtype</td>
                        <td>objcode</td>
                        <td>是否在医生端显示</td>
                        <td>内容</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($pipetpls as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $a->title ?></td>
                        <td><?= $a->objtype ?></td>
                        <td><?= $a->objcode ?></td>
                        <td><?= $a->show_in_doctor ?></td>
                        <td><?= $a->content?></td>
                        <td>
                            <a href="/pipetplmgr/modify?pipetplid=<?= $a->id ?>" class="btn btn-default">修改</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
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
                var dealwithtplid = me.data("dealwithtplid");
                if( confirm("确定要删除吗？") ){
                    $.ajax({
                        url: '/pipetplmgr/deleteJson',
                        type: 'post',
                        dataType: 'text',
                        data: {dealwithtplid: dealwithtplid}
                    })
                    .done(function() {
                        alert("已删除!");
                        window.location.href = window.location.href;
                        console.log("success");
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
