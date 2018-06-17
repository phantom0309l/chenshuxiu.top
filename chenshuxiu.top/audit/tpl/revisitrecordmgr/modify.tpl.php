<?php
$pagetitle = "RevisitRecord 修改 ";
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
    		<form method="post" action="/revisitrecordmgr/modifypost">
                <input type="hidden" name="revisitrecordid" value="<?= $revisitrecord->id ?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width="90">患者</th>
                        <td>
                            <?= $revisitrecord->patient->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>医生</th>
                        <td>
                            <?= $revisitrecord->doctor->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>thedate(复诊日期)</th>
                        <td>
                            <input name="thedate" class="calendar" value="<?= $revisitrecord->thedate ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>内容</th>
                        <td>
                            <textarea rows="4" cols="40" name="content"><?= $revisitrecord->getSymptom() ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交">
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
