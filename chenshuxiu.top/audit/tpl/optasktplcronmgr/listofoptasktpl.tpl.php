<?php
$pagetitle = "[{$optasktpl->title}]任务模版的定时事件　OpTaskTplCron 列表";
$cssFiles = [
//    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
//    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
//    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <a target="_blank" class="btn btn-success" href="/optasktplcronmgr/add?optasktplid=<?=$optasktpl->id?>">新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>步骤</td>
                    <td>话术</td>
                    <td>处理方式</td>
                    <td width="120">约定跟进天数</td>
                    <td>备注</td>
                    
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($optasktplcrons as $i => $a) {
                    ?>
                <tr id="optasktplcron-<?=$a->id?>">
                    <td><?= $a->id ?></td>
                    <td><?= $a->step ?></td>
                    <td><?= $a->send_content ?></td>
                    <td><span class="label label-primary"><?= $a->getDealwith_typeStr() ?></span></td>
                    <td class="text-center"><span class="label label-success"><?= $a->follow_daycnt > 0 ? $a->follow_daycnt : '';  ?></span></td>
                    <td><?= $a->remark ?></td>

                    <td class="text-center">
                        <div class="btn-group">
                            <a target="_blank" href="/optasktplcronmgr/modify?optasktplcronid=<?=$a->id?>" class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="" data-original-title="修改"><i class="fa fa-pencil"></i></a>
                            <button class="btn btn-xs btn-default delete" data-optasktplcronid="<?=$a->id?>" type="button" data-toggle="tooltip" title="" data-original-title="删除"><i class="fa fa-times"></i></button>
                        </div>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $(".delete").on('click', function () {
            var optasktplcronid = $(this).data('optasktplcronid');

            if (! confirm("确定删除吗?")) {
                return false;
            }

            $.ajax({
                url : '/optasktplcronmgr/deletejson',
                type : 'post',
                data : {
                    optasktplcronid : optasktplcronid
                },
                dataType : 'text',
                success : function (data) {
                    if (data == 'success') {
                        $("#optasktplcron-" + optasktplcronid).remove();
                    } else {
                        alert("删除失败!");
                    }
                }
            });
        });
    });
</script>

<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
