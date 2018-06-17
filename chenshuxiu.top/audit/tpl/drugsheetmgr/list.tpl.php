<?php
$pagetitle = "用药历史记录";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .drugsheetRemark{
        border:1px solid #ccc;
        background: #f7f7f7;
        padding: 10px;
        margin: 20px 0px 0px;
    }
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>" id="patientid" />
        <section class="col-md-12">
        <div class="searchBar">
            <p>
                <span>患者：<?= $patient->name ?></span>
                <span>所属医生:<?= $patient->doctor->name?></span>
            </p>
            <div>
                <a class="btn btn-default" href="/drugsheetmgr/add?patientid=<?= $patient->id ?>">创建drugsheet</a>
            </div>
        </div>

        <?php include_once $tpl . "/drugsheetmgr/_nearly2.php"; ?>

        <div class="mt20">
            <?php foreach($drugsheets as $a){
                $drugitems = $a->getDrugItems();
            ?>
                <div>
                    填写日期：<?= $a->thedate ?>
                    <a class="btn btn-primary" href="/drugsheetmgr/updatedrugitems?drugsheetid=<?= $a->id ?>">变更服药信息</a>
                </div>
                <?php if($a->remark){ ?>
                    <div class="drugsheetRemark">其他：<?= $a->remark ?></div>
                <?php } ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>药名</td>
                                <td>剂量</td>
                                <td>频率</td>
                                <td>更新时间</td>
                                <td>操作角色</td>
                            </tr>
                        </thead>
                    <?php
                    foreach ($drugitems as $i => $a) {
                    ?>
                        <tr>
                            <td>
                                <?= $a->medicine->name?>
                            </td>
                            <?php if( 1 == $mydisease->id ){?>
                                <td>
                                    <input style="width: 40px;" value="<?= $a->value ?>" type="text" class="medicinevalue" /> <?= $a->medicine->unit?>
                                </td>
                            <?php }else{?>
                                <td>
                                    <input style="width: 40px;" value="<?= $a->drug_dose ?>" type="text" class="drug_dose" />
                                </td>
                            <?php }?>
                            <td>
                                <?= $a->drug_frequency ?>
                            </td>
                            <td>
                                <?= $a->updatetime ?>
                            </td>
                            <td>
                                <?= $a->auditor->name ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <?php } ?>
        </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
