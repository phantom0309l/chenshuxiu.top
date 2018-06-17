<?php
$pagetitle = "数据一致性和完整性检查(完善中,需要吗?)";
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
                        <td>序号</td>
                        <td>标题</td>
                        <td>结果</td>
                        <td>期望结果</td>
                        <td>sql</td>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($rows as $i => $row) { ?>
                    <tr>
                        <td><?=$i + 1; ?></td>
                        <td><?=$row['title'] ?></td>
                        <td><?=$row['cnt'] ?></td>
                        <td><?=$row['expectcnt'] ?></td>
                        <td><?=nl2br($row['sql']); ?></td>
                    </tr>
            <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
