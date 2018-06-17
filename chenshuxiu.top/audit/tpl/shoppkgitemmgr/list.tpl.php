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
            <form action="/shoppkgitemmgr/list" class="form-horizontal">
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>创建时间</td>
                    <td>商品名</td>
                    <td>单价</td>
                    <td>数量</td>

                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($shopPkgItems as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->createtime ?></td>
                        <td><?= $a->sendpkgid ?></td>
                        <td><?= $a->shopproduct->title ?></td>
                        <td><?= $a->getPrice_yuan() ?></td>
                        <td><?= $a->cnt ?></td>

                        <td align="center">
                            <a target="_blank" href="/shoppkgitemmgr/one?shoppkgitemid=<?= $a->id ?>">查看详情</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php if ($pagelink) {
            include $dtpl . "/pagelink.ctr.php";
        } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
