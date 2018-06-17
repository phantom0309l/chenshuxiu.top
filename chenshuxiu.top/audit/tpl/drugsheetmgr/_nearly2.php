<div class="mt20">
    <?php $pagetitle = "当前用药(最近两次)";include $tpl . "/_pagetitle.php"; ?>
    <?php foreach($drugsheet_nearly2 as $a){
        $drugitems = $a->getDrugItems();
    ?>
        <div>填写日期：<?= $a->thedate ?></div>
        <?php if($a->remark){ ?>
            <div class="drugsheetRemark">其他：<?= $a->remark ?></div>
        <?php } ?>
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
    <?php } ?>
</div>
