<?php
$pagetitle = "医生药品新建";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
                <form action="/doctormedicinerefmgr/add">
                    缩小医生搜索范围
                    <input type="hidden" name="medicineid" value="<?= $medicine->id ?>"/>
                    <input type="text" name="doctorname" value=""/>
                    <input type="submit" class="btn btn-success" value="搜索"/>
                </form>
                <form action="/doctormedicinerefmgr/addpost" method="post">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <tr>
                            <th width=140>医生:</th>
                            <td>
                                <div class="col-xs-2">
                                    <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>药物:</th>
                            <td>
                                <input type="hidden" name="medicineid" value="<?= $medicine->id ?>">
                                <?= $medicine->name ?>
                            </td>
                        </tr>
                        <tr>
                            <th>展示名:</th>
                            <td>
                                <input type="text" name="title" value="<?= $medicine->name ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>序号:</th>
                            <td>
                                <input type="text" name="pos">
                            </td>
                        </tr>
                        <?php if ($diseasemedicineref) { ?>
                            <tr>
                                <th>药物剂量:</th>
                                <td>
                            <textarea name="drug_dose_arr" rows="4"
                                      cols="40"><?= $diseasemedicineref->drug_dose_arr ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>用药频率:</th>
                                <td>
                            <textarea name="drug_frequency_arr" rows="4"
                                      cols="40"><?= $diseasemedicineref->drug_frequency_arr ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>调药规则:</th>
                                <td>
                            <textarea name="drug_change_arr" rows="4"
                                      cols="40"><?= $diseasemedicineref->drug_change_arr ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>用药注意事项:</th>
                                <td>
                            <textarea name="doctorremark" rows="4"
                                      cols="40"><?= $diseasemedicineref->doctorremark ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <input type="submit" class="submit" value="提交"/>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    </div>
                </form>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
