<?php
$pagetitle = "数据库-完整性(外键id)";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <h2>tablea->xxid 对应的数据 is null</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>tablea</td>
                        <td>field</td>
                        <td>tableb</td>
                        <td>cnt</td>
                        <td>sql</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $i = 0;
            foreach ($table_arr as $tablea => $arr) {
                foreach ($arr as $row) {
                    $i ++;
                    ?>
                    <tr>
                        <td><?=$i; ?></td>
                        <td><?=$tablea ?></td>
                        <td><?=$row['field'] ?></td>
                        <td><?=$row['tableb'] ?></td>
                        <td><?=$row['cnt'] ?></td>
                        <td style="color: gray">
                            <?=nl2br($row['sql']); ?>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
