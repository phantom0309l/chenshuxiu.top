<?php
$pagetitle = "服务商品列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/serviceproductmgr/add">新增服务商品</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                <thead>
                <tr>
                    <td>ID</td>
                    <td>创建时间</td>
                    <td>类型</td>
                    <td>标题</td>
                    <td>短标题</td>
                    <td>总价</td>
                    <td>服务项数量</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($serviceproducts as $serviceproduct) {
                    ?>
                    <tr>
                        <td><?= $serviceproduct->id ?></td>
                        <td><?= $serviceproduct->createtime ?></td>
                        <td><?= $serviceproduct->getTypeStr() ?></td>
                        <td><?= $serviceproduct->title ?></td>
                        <td><?= $serviceproduct->short_title ?></td>
                        <td>¥<?= $serviceproduct->getPrice_yuan() ?></td>
                        <td><?= $serviceproduct->item_cnt ?></td>
                        <td>
                            <?php
                            $class = '';
                            if ($serviceproduct->status == 1) {
                                $class = "text-success";
                            } else {
                                $class = 'text-danger';
                            }
                            ?>
                            <span class="<?= $class ?>"><?= $serviceproduct->getStatusStr() ?></span></td>
                        <td>
                            <a target="_blank" class="btn btn-sm btn-default J_delete"
                               data-serviceproductid="<?= $serviceproduct->id ?>">删除</a>
                            <a target="_blank" class="btn btn-sm btn-default"
                               href="/serviceproductmgr/modify?serviceproductid=<?= $serviceproduct->id ?>">
                                修改
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan=100 class="pagelink">
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function() {
    $('.J_delete').on('click', function() {
        if (confirm('确定删除吗？')) {
            var serviceproductid = $(this).data('serviceproductid');
            $.ajax({
                "type": "post",
                "url": "/serviceproductmgr/deletejson",
                dataType: "json",
                data: {serviceproductid: serviceproductid},
                "success": function (res) {
                    if (res.errno === '0') {
                        window.location.reload();
                    } else {
                        alert(res.errmsg);
                    }
                },
                "error": function () {
                    alert('删除失败');
                }
            });
        }
    })
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
