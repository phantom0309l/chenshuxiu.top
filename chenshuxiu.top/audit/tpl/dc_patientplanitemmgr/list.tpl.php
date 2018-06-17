<?php
$pagetitle = "[{$dc_patientplan->dc_doctorproject->dc_project->title}]患者收集计划详情列表 Dc_patientPlanItems";
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
                        <th>患者</th>
                        <th>计划日期</th>
                        <th>填写状况</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dc_patientplanitems as $a) { ?>
                        <tr>
                            <td><?=$a->id?></td>
                            <td><?=$a->patient->name?></td>
                            <td><?=$a->plan_date?></td>
                            <td><?=$a->getPaperTplToPaperStr()?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

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
