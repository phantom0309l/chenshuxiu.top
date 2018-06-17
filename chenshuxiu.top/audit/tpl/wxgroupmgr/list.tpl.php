<?php
$pagetitle = "列表 wxgroups";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/wxgroupmgr/add">新建</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>创建日期</td>
                        <td>wxshopid</td>
                        <td>groupid</td>
                        <td>ename</td>
                        <td>name</td>
                        <td>content</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($wxgroups as $a) { ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->wxshopid ?></td>
                        <td><?= $a->groupid ?></td>
                        <td><?= $a->ename ?></td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->content ?></td>
                        <td>
                            <a href="/wxgroupmgr/modify?wxgroupid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>