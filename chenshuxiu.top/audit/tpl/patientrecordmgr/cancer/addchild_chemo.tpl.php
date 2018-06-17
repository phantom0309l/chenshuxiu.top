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
            <form>
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
                        <th>医院</th>
                        <td>
                            <input type="text" name="<?=$type?>[hospital_name]" value="<?= $patientrecord_data["hospital_name"] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>方案</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_protocol'),"{$type}[protocol]",$patientrecord_data["protocol"],'f18'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>化疗周期</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_cycle'),"{$type}[cycle]",$patientrecord_data["cycle"],'f18'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>性质</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_property'),"{$type}[property]",$patientrecord_data["property"],'f18'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>疗程</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('chemo_period'),"{$type}[period]",$patientrecord_data["period"],'f18'); ?>
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
