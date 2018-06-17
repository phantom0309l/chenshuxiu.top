<?php
$pagetitle = "数据库-完整性(外键ids)";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <h2>tablea->xxids 对应的数据 is null</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>tablea</td>
                        <td>field</td>
                        <td>tableb</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $i = 0;
            foreach ($result['table_ids_arr'] as $tablea => $arr) {
                foreach ($arr as $row) {
                    $i ++;
                    ?>
                    <tr>
                        <td><?=$i; ?></td>
                        <td><?=$tablea ?></td>
                        <td><?=$row['field'] ?></td>
                        <td><?=$row['tableb'] ?></td>
                    </tr>
            <?php
                }
            }
            ?>
                </tbody>
            </table>
            </div>

            <?php
            $table_field_rows = $result['table_field_rows'];
            foreach ($table_field_rows as $tablea => $field_rows) {
                foreach ($field_rows as $field => $arr) {
                    $tableb = $arr['tableb'];
                    $sql = $arr['sql'];
                    $cnt = $arr['cnt'];
                    $rows = $arr['needfix'];
                    ?>
            <h3> <?=$tablea ?>-><?=$field ?> => <?=$tableb ?> 行数 <?=$cnt ?> </h3>
            <pre>
<?=$sql?>
            </pre>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>tablea</td>
                        <td>field</td>
                        <td>tableb</td>
                        <td>ida</td>
                        <td>str</td>
                        <td>idb</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($rows as $row) {
                        $i ++;
                        ?>
                    <tr>
                        <td><?=$i; ?></td>
                        <td><?=$tablea ?></td>
                        <td><?=$field ?></td>
                        <td><?=$tableb ?></td>
                        <td><?=$row['ida'] ?></td>
                        <td><?=$row['str'] ?></td>
                        <td><?=$row['idb'] ?></td>
                    </tr>
            <?php } ?>
            </tbody>
            </table>
            </div>
            <?php }}?>

        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
