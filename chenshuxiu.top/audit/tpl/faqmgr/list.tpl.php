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
            <form action="/faqmgr/list" class="form-horizontal">
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="text" class="form-control" name="title" id="input-title" value="<?= $title ?>" placeholder="输入问题模糊搜索"/>
                         <span class="input-group-btn">
                            <button class="btn btn-default" id="button-search" type="button"><i class="fa fa-search"></i> 搜索</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <a class="btn btn-success" href="/faqmgr/add">新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>创建时间</td>
                    <td>问题</td>
                    <td>答案</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($faqs as $i => $a) {
                    ?>
                <tr>
                    <td><a href='/faqlogmgr/list?faqid=<?= $a->id ?>' target="_blank"><?= $a->id ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->title ?></td>
                    <td><?= $a->content ?></td>

                    <td align="center">
                        <a target="_blank" href="/faqmgr/modify?faqid=<?=$a->id ?>">修改</a>
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
$('#button-search').on('click', function (e) {
    var title = $("#input-title");
    window.location.href = "/faqmgr/list?title=" + title.val();
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
