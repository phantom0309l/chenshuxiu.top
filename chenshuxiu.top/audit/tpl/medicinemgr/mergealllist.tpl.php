<?php
$pagetitle = "重复药品列表 Medicines （{$count}）";
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
                <a class="btn btn-success" href="mergeonelist">特殊药品合并</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>药名</td>
                        <td>数量</td>
                        <td>合并</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if (empty($repetitionmedicines)) {
                        echo "没有重复的药品";
                    }
                    foreach ($repetitionmedicines as $a) {
                    ?>
                        <tr>
                            <td><?= $a['ids'] ?></td>
                            <td><?= $a['name'] ?></td>
                            <td><?= $a['cnt'] ?></td>
                            <td><a href="mergeonelist?ids=<?=$a['ids'] ?>">合并药品</a></td>
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
