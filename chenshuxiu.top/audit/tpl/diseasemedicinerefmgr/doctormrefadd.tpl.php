<?php
$pagetitle = "{$diseasemedicineref->disease->name}下药品（{$diseasemedicineref->medicine->name}）的医生关联";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
                <div class="col-md-5">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>未关联医生</th>
                            <th>添加关联</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($doctors_notselect as $doctor_notselect) { ?>
                            <tr>
                                <td><?= $doctor_notselect->id ?> <?= $doctor_notselect->name ?></td>
                                <td>
                                    <a href="/diseasemedicinerefmgr/doctormrefaddpost?diseasemedicinerefid=<?= $diseasemedicineref->id ?>&doctorid=<?= $doctor_notselect->id ?>"
                                       class="btn btn-primary">-&gt;</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <thead>
                        <th>已关联医生</th>
                        <th>标准用法</th>
                        <th>用药时机</th>
                        <th>剂量</th>
                        <th>频率</th>
                        <th>调药规则</th>
                        <th>药材成分</th>
                        <th>操作</th>
                        </thead>
                        <tbody>
                        <?php foreach ($doctormedicinerefs as $doctormedicineref) { ?>
                            <tr>
                                <td><?= $doctormedicineref->doctorid ?> <?= $doctormedicineref->doctor->name ?></td>
                                <td><?= $doctormedicineref->drug_std_dosage_arr ?></td>
                                <td><?= implode('<br/>', $doctormedicineref->getArrDrug_timespan()) ?></td>
                                <td><?= implode('<br/>', $doctormedicineref->getArrDrug_dose()) ?></td>
                                <td><?= implode('<br/>', $doctormedicineref->getArrDrug_frequency()) ?></td>
                                <td><?= implode('<br/>', $doctormedicineref->getArrDrug_change()) ?> </td>
                                <td><?= implode('<br/>', explode('|', $doctormedicineref->herbjson)) ?> </td>
                                <td><a target="_blank"
                                       href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?= $doctormedicineref->id ?>"
                                       class="btn btn-primary">修改</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    </div>
                </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
