<?php
$pagetitle = "药品({$medicine->name})的疾病关系";
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
        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>未关联疾病</th>
                            <th>添加关联</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $diseases_notselect as $disease_notselect){?>
                        <tr>
                            <td><?= $disease_notselect->name ?></td>
                            <td>
                                <a href="/medicinemgr/dmrefaddpost?medicineid=<?=$medicine->id?>&diseaseid=<?=$disease_notselect->id?>" class="btn btn-primary">-&gt;</a>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-9">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>已关联疾病</th>
                            <th>标准用法</th>
                            <th>用药时机</th>
                            <th>剂量</th>
                            <th>频率</th>
                            <th>调药规则</th>
                            <th>药材成分</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($diseasemedicinerefs as $diseasemedicineref){
                        ?>
                        <tr>
                            <td><?= $diseasemedicineref->disease->name ?></td>
                            <td><?= implode('<br/>', $diseasemedicineref->getArrDrug_std_dosage()) ?></td>
                            <td><?= implode('<br/>', $diseasemedicineref->getArrDrug_timespan()) ?></td>
                            <td><?= implode('<br/>', $diseasemedicineref->getArrDrug_dose()) ?></td>
                            <td><?= implode('<br/>', $diseasemedicineref->getArrDrug_frequency()) ?></td>
                            <td><?= implode('<br/>', $diseasemedicineref->getArrDrug_change()) ?> </td>
                            <td><?= implode('<br/>', explode('|',$diseasemedicineref->herbjson)) ?> </td>
                            <td>
                                <a target="_blank" href="/diseasemedicinerefmgr/modify?diseasemedicinerefid=<?=$diseasemedicineref->id?>" class="btn btn-primary">修改</a>
                                <a target="_blank" href="/diseasemedicinerefmgr/doctormrefadd?diseasemedicinerefid=<?=$diseasemedicineref->id ?>" class="btn btn-success">关联医生</a>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
