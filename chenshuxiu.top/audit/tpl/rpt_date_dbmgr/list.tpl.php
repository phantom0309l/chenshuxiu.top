<?php
$pagetitle = "数据库-统计-每日汇总";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width=15>#</td>
                        <td width=130>日期</td>
                        <td width=80>
                            表数
                            <br />
                            (总)
                        </td>
                        <td width=80>
                            表数
                            <br />
                            (有数据)
                        </td>
                        <td width=80>行数</td>
                        <td width=80>
                            行数
                            <br />
                            (累计)
                        </td>
                        <td>明细</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($rpt_date_dbs as $a) {
                    $i ++;
                    ?>
                    <tr>
                        <td><?=$i ?></td>
                        <td>
                            <a href="/rpt_date_tablemgr/list?thedate=<?= $a->thedate ?>"><?= $a->thedate ?> <?= XDateTime::get_chinese_weekday($a->thedate) ?></a>
                        </td>
                        <td><?= $a->tablecnt ?></td>
                        <td><?= $a->tablecnt_hasdata ?></td>
                        <td><?= $a->rowcnt ?></td>
                        <td><?= $a->total_rowcnt ?></td>
                        <td>
                            <a href="/rpt_date_tablemgr/list?thedate=<?= $a->thedate ?>">明细</a>
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
