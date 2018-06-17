<?php
$pagetitle = "数据库-统计 SumOfTables ";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a href="/rpt_date_tablemgr/sumoftables?orderby=max_thedate" class="btn <?= ($orderby == 'max_thedate')?'btn-success':'btn-default' ?>">最新数据日期倒序</a>
                <a href="/rpt_date_tablemgr/sumoftables?orderby=min_thedate" class="btn <?= ($orderby == 'min_thedate')?'btn-success':'btn-default' ?>">最新数据日期正序</a>
                <a href="/rpt_date_tablemgr/sumoftables?orderby=tablename" class="btn <?= ($orderby == 'tablename')?'btn-success':'btn-default' ?>">表名正序</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width=40>#</td>
                        <td width=200>表名</td>
                        <td width=120>
                            日期
                            <br />
                            min
                        </td>
                        <td width=120>
                            日期
                            <br />
                            max
                        </td>
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
                foreach ($rows as $a) {
                    $i ++;
                    ?>
                    <tr>
                        <td><?=$i ?></td>
                        <td>
                            <a href="/rpt_date_tablemgr/list?tablename=<?= $a['tablename'] ?>"><?= $a['tablename'] ?> </a>
                        </td>
                        <td><?= $a['min_thedate'] ?></td>
                        <td><?= $a['max_thedate'] ?></td>
                        <td><?= $a['total_rowcnt'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            <h4>表已经不存在了</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width=40>#</td>
                        <td width=200>表名</td>
                        <td>-</td>
                    </tr>
                </thead>
                <tbody>
             <?php
            $i = 0;
            foreach ($diff1 as $a) {
                $i ++;
                ?>
                    <tr>
                        <td><?=$i ?></td>
                        <td><?=$a ?></td>
                        <td>-</td>
                    </tr>
               <?php
            }
            ?>
                </tbody>
            </table>
            </div>
            <h4>没有统计的表</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width=40>#</td>
                        <td width=200>表名</td>
                        <td>行数</td>
                    </tr>
                </thead>
                <tbody>
             <?php
            $i = 0;
            foreach ($diff2 as $a) {
                $i ++;
                ?>
                    <tr>
                        <td><?=$i ?></td>
                        <td><?=$a ?></td>
                        <td><?= Dao::queryValue("select count(*) from {$a}"); ?></td>
                    </tr>
               <?php
            }
            ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
