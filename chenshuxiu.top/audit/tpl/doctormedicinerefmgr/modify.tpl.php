<?php
$pagetitle = "药品-医生定制 修改";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
    <?php
    $medicine = $doctormedicineref->medicine;

    $diseasemedicineref = null;
    if ($mydisease instanceof Disease) {
        $diseasemedicineref = DiseaseMedicineRefDao::getByDiseaseAndMedicine($mydisease, $medicine);
    }
    ?>
        <form action="/doctormedicinerefmgr/modifypost" method="post">
            <input type="hidden" name="doctormedicinerefid" value="<?= $doctormedicineref->id ?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width=140>医生:</th>
                        <td colspan="3">
                            <?php echo $doctormedicineref->doctor->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>药物:</th>
                        <td colspan="3">
                            <?php echo $doctormedicineref->medicine->name ?>
                            <a target="_blank" class="btn btn-primary" href="/medicinemgr/modify?medicineid=<?= $medicine->id ?>">修改药品基本信息</a>
                        </td>
                    </tr>
                    <tr>
                        <th>分组</th>
                        <td colspan="3">
                            <?= $doctormedicineref->medicine->groupstr ?>
                        </td>
                    </tr>
                    <tr>
                        <th>单位</th>
                        <td colspan="3">
                            <?= $doctormedicineref->medicine->unit ?>
                        </td>
                    </tr>
                    <tr>
                        <th>给药途径</th>
                        <td colspan="3">
                            <?= $doctormedicineref->medicine->drug_way_arr ?>
                        </td>
                    </tr>
                    <tr>
                        <th>展示名:</th>
                        <td>
                            <input name="title" value="<?= $doctormedicineref->title ?>">
                        </td>
                        <td width=25%>
                            疾病药品属性
                            <br />
                            当前疾病：
                            <?php
                            if ($mydisease instanceof Disease) {
                                ?>
                            <a target="_blank" href="/diseasemedicinerefmgr/modify?diseasemedicinerefid=<?= $diseasemedicineref->id ?>"><?= $mydisease->name ?></a>
                            <?php
                            } else {
                                echo '<span class="red">未选择疾病<br/>可以在页头选择疾病</span>';
                            }
                            ?>
                        </td>
                        <td width=25%>公共药品属性</td>
                    </tr>
                    <tr>
                        <th>用药时机</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_timespan_arr" value="<?= $doctormedicineref->drug_timespan_arr ?>" />
                            竖线分隔
                        </td>
                        <td><?= $diseasemedicineref->drug_timespan_arr ?></td>
                        <td><?= $medicine->drug_timespan_arr ?></td>
                    </tr>
                    <tr>
                        <th>标准用法</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_std_dosage_arr" value="<?= $doctormedicineref->drug_std_dosage_arr ?>" />
                            竖线分隔
                        </td>
                        <td><?= $diseasemedicineref->drug_std_dosage_arr ?></td>
                        <td><?= $medicine->drug_std_dosage_arr ?></td>
                    </tr>
                    <tr>
                        <th>药物剂量:</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_dose_arr" value="<?= $doctormedicineref->drug_dose_arr ?>" />
                            竖线分隔
                        </td>
                        <td><?= $diseasemedicineref->drug_dose_arr ?></td>
                        <td><?= $medicine->drug_dose_arr ?></td>
                    </tr>
                    <tr>
                        <th>用药频率:</th>
                        <td>
                            <textarea name="drug_frequency_arr" style="width: 80%; height: 100px;"><?= $doctormedicineref->drug_frequency_arr ?></textarea>
                            竖线分隔
                        </td>
                        <td><?= $diseasemedicineref->drug_frequency_arr ?></td>
                        <td><?= $medicine->drug_frequency_arr ?></td>
                    </tr>
                    <tr>
                        <th>调药规则:</th>
                        <td>
                            <textarea name="drug_change_arr" style="width: 80%; height: 100px;"><?= $doctormedicineref->drug_change_arr ?></textarea>
                            竖线分隔
                        </td>
                        <td><?= $diseasemedicineref->drug_change_arr ?></td>
                        <td><?= $medicine->drug_change_arr ?></td>
                    </tr>
                    <tr>
                        <th>
                            药材成分
                            <br />
                            （中药才需要填写）
                        </th>
                        <td>
                            <textarea name="herbjson" style="width: 80%; height: 100px;"><?= $doctormedicineref->herbjson ?></textarea>
                            填写格式 药材名1=用量1|药材名2=用量2
                        </td>
                        <td><?= $diseasemedicineref->herbjson; ?></td>
                        <td><?= $medicine->herbjson; ?></td>
                    </tr>
                    <tr>
                        <th>用药注意事项:</th>
                        <td>
                            <textarea name="doctorremark" style="width: 80%; height: 100px;"><?= $doctormedicineref->doctorremark ?></textarea>
                        </td>
                        <td><?= $diseasemedicineref->doctorremark ?></td>
                        <td><?= $medicine->doctorremark ?></td>
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
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
