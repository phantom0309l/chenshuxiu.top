<?php
$pagetitle = "药品-疾病定制 添加";
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
        <a class="btn btn-warning" href="/diseasemedicinerefmgr/copylist">快速拷贝</a>
        <form action="/diseasemedicinerefmgr/addpost" method="post">
            <?php if ($medicine) { ?>
                <input type="hidden" name="medicineid" value="<?= $medicine->id ?>"/>
            <?php } ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td colspan="2" class="text-primary"><h4>药品基本信息</h4></td>
                </tr>
                <tr>
                    <th width='140'>商品名</th>
                    <td>
                        <?php if ($medicine) { ?>
                            <?= $medicine->name ?>
                        <?php } else { ?>
                            <input type="text" name="name" value=""/>
                            <span class="gray">主要用这个</span>
                        <?php } ?>

                    </td>
                </tr>
                <tr>
                    <th>学名</th>
                    <td>
                        <?php if ($medicine) { ?>
                            <?= $medicine->scientificname ?>
                        <?php } else { ?>
                            <input type="text" name="scientificname" value=""/>
                        <?php } ?>

                    </td>
                </tr>
                <tr>
                    <th>分组</th>
                    <td>
                        <?php if ($medicine) { ?>
                            <?= $medicine->groupstr ?>
                        <?php } else { ?>
                            <input type="text" name="groupstr" class="groupstr-input"/>
                            <span class="btn btn-primary groupstr-panel-on">现有分组</span>
                            <div class="groupstr-panel none">
                                <?php
                                $groupstrs = Medicine::getGroupstrArr(0);
                                foreach ($groupstrs as $groupstr) {
                                    ?>
                                    <div class="btn btn-default groupstr-btn"
                                         data-groupstr="<?= $groupstr ?>"><?= $groupstr ?></div>
                                    <?php
                                } ?>
                            </div>
                        <?php } ?>

                    </td>
                </tr>
                <tr>
                    <th>是否是中药</th>
                    <td>
                        <?= HtmlCtr::getRadioCtrImp(array('0' => '不是', '1' => '是'), 'ischinese', 0, '') ?>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-primary"><h4>疾病药品信息</h4></td>
                </tr>
                <tr>
                    <th>疾病</th>
                    <td>
                        <?= $mydisease->name ?>
                    </td>
                </tr>
                <tr>
                    <th>剂量</th>
                    <td>
                        <input type="text" name="drug_dose_arr" value=""/>
                    </td>
                </tr>
                <tr>
                    <th>频率</th>
                    <td>
                        <input type="text" name="drug_frequency_arr" value=""/>
                    </td>
                </tr>
                <tr>
                    <th>调药规则</th>
                    <td>
                        <input type="text" name="drug_change_arr" value=""/>
                    </td>
                </tr>
                <tr>
                    <th>药材成分<br/>（中药才需要填写）</th>
                    <td>
                        <textarea name="herbjson" style="width: 80%; height: 100px;"></textarea>
                        填写格式 药材名1=用量1|药材名2=用量2
                    </td>
                </tr>
                <tr>
                    <th>用药注意事项</th>
                    <td>
                        <textarea name="doctorremark" style="width: 50%; height: 200px;"></textarea>
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
