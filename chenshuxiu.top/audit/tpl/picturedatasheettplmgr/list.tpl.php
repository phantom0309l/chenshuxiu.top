<?php
$pagetitle = "图片归档模板列表 PictureDataSheetTpl";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/picturedatasheettplmgr/add">图片归档模板新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" style="border-top: 1px solid #ccc; margin-top: 10px;">
            <thead>
            <tr>
                <td>id</td>
                <td>所属疾病</td>
                <td>编码</td>
                <td>模板标题</td>
                <td>问题标题列表</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($pictureDataSheetTpls as $a) { ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->disease->name ?></td>
                    <td><?= $a->ename ?></td>
                    <td><?= $a->title ?></td>
                    <td><?= nl2br($a->questiontitles) ?></td>
                    <td>
                        <a href="/picturedatasheettplmgr/modify?picturedatasheettplid=<?= $a->id ?>">修改</a>
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
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
