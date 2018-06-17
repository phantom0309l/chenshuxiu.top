<?php
$pagetitle = "数据库-统计 rpt_date_table 列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">thedate=<?=$thedate ?>  tablename=<?=$tablename ?></div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width=15>#</td>
                        <td width=130>日期</td>
                        <td width=150>表名</td>
                        <td width=80>行数</td>
                        <td>
                            行数
                            <br />
                            (累计)
                        </td>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($rpt_date_tables as $a) {
                    $i ++;
                    ?>
                    <tr>
                        <td><?=$i ?></td>
                        <td>
                            <a href="/rpt_date_tablemgr/list?thedate=<?= $a->thedate ?>"><?= $a->thedate ?> <?= XDateTime::get_chinese_weekday($a->thedate) ?></a>
                        </td>
                        <td>
                            <a href="/rpt_date_tablemgr/list?tablename=<?= $a->tablename ?>"><?= $a->tablename ?></a>
                        </td>
                        <td><?= $a->rowcnt ?></td>
                        <td><?= $a->total_rowcnt ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
