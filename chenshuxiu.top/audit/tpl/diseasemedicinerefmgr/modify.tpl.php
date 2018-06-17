<?php
$pagetitle = "药品-疾病定制（{$diseasemedicineref->disease->name}-{$diseasemedicineref->medicine->name}）";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v5/page/audit/diseasemedicinerefmgr/add/add.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
                <form action="/diseasemedicinerefmgr/modifypost" method="post">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <input type="hidden" name="diseasemedicinerefid" value="<?= $diseasemedicineref->id ?>"/>
                        <tr>
                            <td colspan="3" class="text-primary">
                                <h5>药品基本信息</h5>
                            </td>
                        </tr>
                        <tr>
                            <th width='140'>商品名</th>
                            <td width="50%">
                                <?= $diseasemedicineref->medicine->name ?>
                                <a target="_blank" class="btn btn-primary"
                                   href="/medicinemgr/modify?medicineid=<?= $diseasemedicineref->medicineid ?>">修改药品基本信息</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>学名</th>
                            <td>
                                <?= $diseasemedicineref->medicine->scientificname ?>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>分组</th>
                            <td>
                                <?= $diseasemedicineref->medicine->groupstr ?>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>单位</th>
                            <td>
                                <?= $diseasemedicineref->medicine->unit ?>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>给药途径</th>
                            <td>
                                <?= $diseasemedicineref->medicine->drug_way_arr ?>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-primary">
                                <h5>疾病药品定制信息</h5>
                            </td>
                        </tr>
                        <tr>
                            <th>疾病</th>
                            <td>
                                <?= $diseasemedicineref->disease->name ?>
                            </td>
                        </tr>
                        <tr>
                            <th>等级</th>
                            <td>
                                <input type="text" name="level" value="<?= $diseasemedicineref->level ?>"/>

                                等级 =9 运营关注的药
                            </td>
                            <td>公共药品属性对比</td>
                        </tr>
                        <tr>
                            <th>用药时机</th>
                            <td>
                                <input type="text" style="width: 85%;" name="drug_timespan_arr"
                                       value="<?= $diseasemedicineref->drug_timespan_arr ?>"/>
                                竖线分隔
                            </td>
                            <td>
                                <?= $diseasemedicineref->medicine->drug_timespan_arr; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>标准用法</th>
                            <td>
                                <input type="text" style="width: 85%;" name="drug_std_dosage_arr"
                                       value="<?= $diseasemedicineref->drug_std_dosage_arr ?>"/>
                                竖线分隔
                            </td>
                            <td>
                                <?= $diseasemedicineref->medicine->drug_std_dosage_arr ?>
                            </td>
                        </tr>
                        <tr>
                            <th>剂量</th>
                            <td>
                                <input type="text" style="width: 85%;" name="drug_dose_arr"
                                       value="<?= $diseasemedicineref->drug_dose_arr ?>"/>
                                竖线分隔
                            </td>
                            <td>
                                <?= $diseasemedicineref->medicine->drug_dose_arr; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>频率</th>
                            <td>
                                <input type="text" style="width: 85%;" name="drug_frequency_arr"
                                       value="<?= $diseasemedicineref->drug_frequency_arr ?>"/>
                                竖线分隔
                            </td>
                            <td>
                                <?= $diseasemedicineref->medicine->drug_frequency_arr; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>调药规则</th>
                            <td>
                        <textarea name="drug_change_arr"
                                  style="width: 85%; height: 100px;"><?= $diseasemedicineref->drug_change_arr ?></textarea>
                                竖线分隔
                            </td>
                            <td>
                                <?= $diseasemedicineref->medicine->drug_change_arr; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>药材成分<br/>（中药才需要填写）</th>
                            <td>
                        <textarea name="herbjson"
                                  style="width: 80%; height: 100px;"><?= $diseasemedicineref->herbjson ?></textarea>
                                填写格式 药材名1=用量1|药材名2=用量2
                            </td>
                            <td>
                                <?= $diseasemedicineref->medicine->herbjson; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>用药注意事项</th>
                            <td>
                        <textarea name="doctorremark"
                                  style="width: 85%; height: 200px;"><?= $diseasemedicineref->doctorremark ?></textarea>
                            </td>
                            <td>
                                <?= $diseasemedicineref->medicine->doctorremark ?>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="submit" class="btn btn-success" value="提交"/>
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
