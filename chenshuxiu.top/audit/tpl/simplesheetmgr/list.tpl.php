<?php
$pagetitle = "精简版答卷列表 SimpleSheets";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12"  style="overflow-x:auto">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:100px">ID</th>
                            <th style="width:160px">创建时间</th>
                            <th>患者</th>
                            <th>答卷</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($simplesheets as $a) { ?>
                            <tr>
                                <td><?=$a->id?></td>
                                <td><?=$a->createtime?></td>
                                <td><?=$a->patient->name?></td>
                                <td><a target="_blank" href="/simplesheetmgr/oneshow?simplesheetid=<?=$a->id?>">查看答卷</a></td>
                                <td>todo</td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan=100 class="pagelink">
                                <?php include $dtpl . "/pagelink.ctr.php"; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="clear"></div>

<script>

</script>

<?php
$footerScript = <<<XXX
$(function() {

});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
