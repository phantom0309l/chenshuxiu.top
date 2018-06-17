<?php
$pagetitle = "备注修改";
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
            <form action="/patientrecordmgr/modifypost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type="hidden" name="patientrecordid" value="<?= $patientrecord->id ?>" />
                    <?php 
                        $type = $patientrecord->type;
                    ?>
                    <tr>
                        <th width=140>日期</th>
                        <td>
                            <input type="text" class="calendar" name="thedate" value="<?= $patientrecord->thedate ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>WBC</th>
                        <td>
                            <input type="text" name="<?= $type ?>[WBC]" value="<?= $patientrecord_data["WBC"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>NEUT#(10^9/L)</th>
                        <td>
                            <input type="text" name="<?= $type ?>[NEUT#]" value="<?= $patientrecord_data["NEUT#"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>NEUT%</th>
                        <td>
                            <input type="text" name="<?= $type ?>[NEUT%]" value="<?= $patientrecord_data["NEUT%"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>LY%</th>
                        <td>
                            <input type="text" name="<?= $type ?>[LY%]" value="<?= $patientrecord_data["LY%"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>PLT</th>
                        <td>
                            <input type="text" name="<?= $type ?>[PLT]" value="<?= $patientrecord_data["PLT"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>HGB</th>
                        <td>
                            <input type="text" name="<?= $type ?>[HGB]" value="<?= $patientrecord_data["HGB"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <textarea name="content" rows="4" cols="40"><?= $patientrecord->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
