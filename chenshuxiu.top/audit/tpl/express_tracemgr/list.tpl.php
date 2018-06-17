<?php
$pagetitle = "列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
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
            <form action="/express_tracemgr/list" class="form-horizontal">
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>
            </form>
        </div>
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <a class="btn btn-success" href="/express_tracemgr/add">新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>createtime</td>
                    <td>express_no</td>
                    <td>shoporderid</td>
                    <td>sub_time</td>
                    <td>sub_status</td>
                    <td>sub_reason</td>
                    <td>start_time</td>
                    <td>end_time</td>
                    <td>state</td>
                    <td>traces</td>
                    <td>remark</td>
                    
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($express_traces as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->express_no ?></td>
                    <td><?= $a->shoporderid ?></td>
                    <td><?= $a->sub_time ?></td>
                    <td><?= $a->sub_status ?></td>
                    <td><?= $a->sub_reason ?></td>
                    <td><?= $a->start_time ?></td>
                    <td><?= $a->end_time ?></td>
                    <td><?= $a->state ?></td>
                    <td><?= $a->traces ?></td>
                    <td><?= $a->remark ?></td>
                    
                    <td align="center">
                        <a target="_blank" href="/express_tracemgr/modify?express_traceid=<?=$a->id ?>">修改</a>
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
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
