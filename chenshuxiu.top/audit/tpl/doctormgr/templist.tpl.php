<?php
$pagetitle = "医生列表 Doctors";
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
                    <td>姓名</td>
                    <td>所属医院</td>
                    <td>开通时间</td>
                    <td>一个月内</td>
                    <td>总计</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($doctors as $doctor) {
                    ?>
                    <tr>
                        <td><?= $doctor->name ?></td>
                        <td><?= $doctor->hospital->shortname ?></td>
                        <td><?= $doctor->getCreateDay() ?></td>
                        <td><?= $doctor->getPaitentCnt_lastmonths(true) ?></td>
                        <td><?= $doctor->getPatientCnt(true) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        </div>
    </section>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
