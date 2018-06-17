<?php
$pagetitle = '变更医生';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/patientmgr/changedoctorpost" method="post">
                <input type="hidden" id="patientid" name="patientid" value="<?= $patient->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>当前所属医生</th>
                        <td>
                            <?= $patient->doctor->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>变更医生为：</th>
                        <td>
                            <input id="to_doctorid" type="text" name="to_doctorid" style="width: 50%;" />
                            <span>输入要变更医生的id。<a href="/doctormgr/list" target="_blank">去找医生id</a></span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="变更" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include $tpl . "/_footer.new.tpl.php"; ?>
