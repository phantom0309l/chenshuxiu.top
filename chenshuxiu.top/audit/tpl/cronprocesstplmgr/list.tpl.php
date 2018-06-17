<?php
$pagetitle = "定时任务模板 CronProcessTpl";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
        "{$img_uri}/static/js/avalon.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .main {
        width: 30%;
        position: relative;
        margin-left: 20%;
        margin-right: auto;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/cronprocesstplmgr/add">添加模板</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <th>模板id</th>
                    <th>定时任务类名</th>
                    <th>标题</th>
                    <th>分组</th>
                    <th>定时任务描述</th>
                    <th>操作</th>
                </thead>
                <tbody>
            <?php foreach ($cronprocesstpls as $cronprocesstpl) {?>
                <tr>
                        <td><?= $cronprocesstpl->id ?></td>
                        <td><?= $cronprocesstpl->tasktype ?></td>
                        <td><?= $cronprocesstpl->title ?></td>
                        <td><?= $cronprocesstpl->groupstr?></td>
                        <td><?= nl2br($cronprocesstpl->content) ?></td>
                        <td>
                        <?php
                if ($myauditor->isHasRole(array('admin'))) {
                    ?>
                            <a href="/cronprocesstplmgr/modify?cronprocesstplid=<?= $cronprocesstpl->id ?>" target='_blank'>修改</a>
                        <?php
                } else {
                    ?>
                        <span class="red">（找老史修改模板！）</span>
                        <?php } ?>
                        &nbsp;
                        <a href="/cronprocessmgr/list?cronprocesstplid=<?= $cronprocesstpl->id?>">查看此类定时任务</a>
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