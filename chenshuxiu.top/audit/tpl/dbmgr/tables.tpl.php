<?php
$pagetitle = "数据库各表统计";
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
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>表名</td>
                        <td>行数</td>
                        <td>minid</td>
                        <td>maxid</td>
                        <td>mincreatetime</td>
                        <td>maxcreatetime</td>
                        <td>maxupdatetime</td>
                        <td>每日情况</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $i => $row) { ?>
                    <tr>
                        <td><?=$i+1 ?></td>
                        <td><?=$row['tablename'] ?></td>
                        <td><?=$row['cnt'] ?></td>
                        <td><?=$row['minid'] ?></td>
                        <td><?=$row['maxid'] ?></td>
                        <td><?=$row['mincreatetime'] ?></td>
                        <td><?=$row['maxcreatetime'] ?></td>
                        <td><?=$row['maxupdatetime'] ?></td>
                        <td>
                            <a target="_blank" href="/dbmgr/daysumoftable?tablename=<?=$row['tablename'] ?>"><?=$row['tablename'] ?> 统计</a>
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