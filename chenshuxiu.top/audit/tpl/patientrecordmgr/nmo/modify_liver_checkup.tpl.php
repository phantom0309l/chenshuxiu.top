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
                        <th width=140>ALT</th>
                        <td>
                            <input type="text" name="<?= $type ?>[ALT]" value="<?= $patientrecord_data["ALT"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>AST</th>
                        <td>
                            <input type="text" name="<?= $type ?>[AST]" value="<?= $patientrecord_data["AST"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>DBIL</th>
                        <td>
                            <input type="text" name="<?= $type ?>[DBIL]" value="<?= $patientrecord_data["DBIL"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>TBIL</th>
                        <td>
                            <input type="text" name="<?= $type ?>[TBIL]" value="<?= $patientrecord_data["TBIL"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>GGT</th>
                        <td>
                            <input type="text" name="<?= $type ?>[GGT]" value="<?= $patientrecord_data["GGT"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>ALP</th>
                        <td>
                            <input type="text" name="<?= $type ?>[ALP]" value="<?= $patientrecord_data["ALP"] ?>">
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
