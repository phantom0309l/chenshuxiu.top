<?php
$pagetitle = "方寸课堂活动列表";
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
            <a class="btn btn-success" href="/wxtasktplmgr/add">活动新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>id</td>
                <td>创建日期</td>
                <td>ename</td>
                <td>标题</td>
                <td>课文数</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($wxtasktpls as $i => $a) { ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $a->getCreateDay() ?></td>
                    <td><?= $a->ename ?></td>
                    <td><?= $a->title ?></td>
                    <td>
                        <a href="/wxtasktplitemmgr/list?wxtasktplid=<?= $a->id ?>">子任务(<?= $a->getWxTaskTplItemCnt() ?>
                            )</a>
                    </td>
                    <td>
                        <a href="/wxtasktplmgr/modify?wxtasktplid=<?= $a->id ?>">修改</a>
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
