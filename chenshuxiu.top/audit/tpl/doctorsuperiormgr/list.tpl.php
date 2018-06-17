<?php
$pagetitle = "医生主管列表 Doctors";
$cssFiles = [
    //$img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    //$img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    //$img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
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
                        <td>id</td>
                        <td>创建日期</td>
                        <td>主管医生名</td>
                        <td>医生名</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($doctorSuperiors as $a) {
                ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><a href="/doctorconfigmgr/overview?doctorid=<?=$a->superior_doctorid?>"><?= $a->superior_doctor->name ?></a></td>
                        <td><a href="/doctorconfigmgr/overview?doctorid=<?=$a->doctorid?>"><?= $a->doctor->name; ?></a></td>
                        <td><a class="a-delete" data-id="<?= $a->id?>" href="javascript:">删除</a></td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=12>
<?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>

    <div class="clear"></div>

<?php
$footerScript = <<<XXX
$(function() {
    //App.initHelper('select2');

    $('.a-delete').on('click', function() {
        if (!confirm("确定要删除吗？")) {
            return false;
        }
        var id = $(this).data('id');
        window.location.href="/doctorsuperiormgr/delete?doctor_superiorid=" + id;
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
