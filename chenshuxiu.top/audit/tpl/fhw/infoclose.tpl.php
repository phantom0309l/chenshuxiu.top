<?php
$pagetitle = "运营关闭任务";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                        	<td>姓名</td>
                            <td>总数</td>
                            <?php 
                                foreach ($optasktplids as $id) {
                                    $optasktpl = OpTaskTpl::getById($id);
                                    ?>
                                    	<td><?=$optasktpl->title?></td>
                                    <?php
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
                        foreach ($list as $key => $a) {
                            $auditor = Auditor::getById($key);
                            ?>
                            <tr>
                                <td><?= $auditor->name ?></td>
                                <td><?= $listcnts["{$key}"]['cnt'] ?></td>
                                <?php 
                                    foreach ($optasktplids as $id) {
                                        ?>
                                        	<td style="color: <?= $a["{$id}"] == 0 ? '' : 'red' ?>"><?=$a["{$id}"]?></td>
                                        <?php
                                    }
                                ?>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan=10>
    							<?php include $dtpl . "/pagelink.ctr.php"; ?>
                            </td>
                        </tr>
                    </tbody>
            	</table>
            </div>
        </section>
    </div>

    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
