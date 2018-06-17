<?php
$pagetitle = "{$patient->name}药物副反应检测报告新增";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/pmsideeffectmgr/addpost" method="post">
            <input type="hidden" name="patientid" value="<?=$patient->id ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width=140>患者</th>
                        <td>
                            <?=$patient->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td>
                            <?=$patient->doctor->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>所针对药物</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toMedicineCtrArrayForPmSideEffect(PmSideEffect::getPMMedicines()), 'medicineid', 0 ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>任务推送提醒时间</th>
                        <td>
                            <input type="text" class="calendar" name="plantime" />
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
