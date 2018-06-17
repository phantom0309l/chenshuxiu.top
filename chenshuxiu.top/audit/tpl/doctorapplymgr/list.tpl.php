<?php
$pagetitle = "医生申请列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>createtime</td>
                    <td>医生姓名</td>
                    <td>医院</td>
                    <td>科室</td>
                    <td>电话</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($doctorApplys as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->name ?></td>
                    <td><?= $a->hospital_name ?></td>
                    <td><?= $a->department ?></td>
                    <td><?= $a->mobile ?></td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
