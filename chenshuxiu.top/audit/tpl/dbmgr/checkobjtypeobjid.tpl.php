<?php
$pagetitle = "数据库-完整性(objtype,objid)";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <h2>删除测试用户的null patient的流</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>删除行数</td>
                        <td>sql</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?=$result['deletePipesOfNullPaitentOfAuditors']['cnt'] ?></td>
                        <td>
                            <pre><?=$result['deletePipesOfNullPaitentOfAuditors']['sql'] ?></pre>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h2>objtype对应的表为空或不存在</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>table</td>
                        <td>objtype</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $arrForNullTable = $result['arrForNullTable'];
            foreach ($arrForNullTable as $i => $row) {
                ?>
                    <tr>
                        <td><?=$i + 1; ?></td>
                        <td><?=$row['table'] ?></td>
                        <td><?=$row['objtype'] ?></td>
                    </tr>
            <?php } ?>
                </tbody>
            </table>
        </div>
        <h2>objtype,objid 对应的数据 is null</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>table</td>
                        <td>objtype</td>
                        <td>cnt</td>
                        <td>sql</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $arrForNullObjtype = $result['arrForNullObjtype'];
            foreach ($arrForNullObjtype as $i => $row) {
                ?>
                    <tr>
                        <td><?=$i + 1; ?></td>
                        <td><?=$row['table'] ?></td>
                        <td><?=$row['objtype'] ?></td>
                        <td><?=$row['cnt'] ?></td>
                        <td><?=nl2br($row['sql']); ?></td>
                    </tr>
            <?php } ?>
                </tbody>
            </table>
            <br />
            <span class="f16 red">
             暂时跳过的表: <?= implode(',', $result['jumpTables_nocheck']); ?>
            </span>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
