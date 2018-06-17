<?php
$pagetitle = "运营创建任务";
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
            	<a class="btn btn-success" href="">08.01-08.07</a>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                        	<td>完成时间</td>
                        	<td>患者姓名</td>
                            <td>主治医生</td>
                            <td>(肿瘤)分组</td>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
                        foreach ($patients_one as $a) {
                            ?>
                            <tr>
                                <td><?= substr($a->donetime, 0, 10) ?></td>
                                <td><?= $a->patient->name ?></td>
                                <td><?= $a->patient->doctor->name ?></td>
                            	<td><?= $a->patient->doctor->doctorgroup->title?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
            	</table>

            	<a class="btn btn-success" href="">08.08-08.14</a>
            	<table class="table table-bordered">
                    <thead>
                        <tr>
                        	<td>完成时间</td>
                        	<td>患者姓名</td>
                            <td>主治医生</td>
                            <td>(肿瘤)分组</td>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
                        foreach ($patients_two as $a) {
                            ?>
                            <tr>
                                <td><?= substr($a->donetime, 0, 10) ?></td>
                                <td><?= $a->patient->name ?></td>
                                <td><?= $a->patient->doctor->name ?></td>
                            	<td><?= $a->patient->doctor->doctorgroup->title?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
            	</table>

            	<a class="btn btn-success" href="">08.15-08.21</a>
            	<table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>完成时间</td>
                        	<td>患者姓名</td>
                            <td>主治医生</td>
                            <td>(肿瘤)分组</td>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
                        foreach ($patients_three as $a) {
                            ?>
                            <tr>
                                <td><?= substr($a->donetime, 0, 10) ?></td>
                                <td><?= $a->patient->name ?></td>
                                <td><?= $a->patient->doctor->name ?></td>
                            	<td><?= $a->patient->doctor->doctorgroup->title?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
            	</table>

            	<a class="btn btn-success" href="">08.22-08.31</a>
            	<table class="table table-bordered">
                    <thead>
                        <tr>
                        	<td>完成时间</td>
                        	<td>患者姓名</td>
                            <td>主治医生</td>
                            <td>(肿瘤)分组</td>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
                        foreach ($patients_four as $a) {
                            ?>
                            <tr>
                                <td><?= substr($a->donetime, 0, 10) ?></td>
                                <td><?= $a->patient->name ?></td>
                                <td><?= $a->patient->doctor->name ?></td>
                            	<td><?= $a->patient->doctor->doctorgroup->title?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
            	</table>
            </div>
        </section>
    </div>

    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
