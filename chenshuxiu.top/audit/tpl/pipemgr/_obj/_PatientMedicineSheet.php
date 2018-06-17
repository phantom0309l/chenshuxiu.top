<div>
<?php $pmsheet = $a->obj; if ($pmsheet) {?>
    <?php if ($pmsheet->isCreateByAuditor()) { ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                    <thead>
                        <tr>
                        <th>服药日期</th>
                        <th>药名</th>
                        <th>剂量</th>
                        <th>频次</th>
                        <th>状态</th>
                        <th>备注</th>
                        </tr>
                    </thead>
                <?php
                $pmsitems = PatientMedicineSheetItemDao::getListByPatientmedicinesheetid($pmsheet->id, true);
                foreach ($pmsitems as $pmsitem) {
                ?>
                <tr>
                    <td><?= $pmsitem->drug_date ?></td>
                    <td><?= $pmsitem->medicine->name ?></td>
                    <td><?= $pmsitem->drug_dose ?></td>
                    <td><?= $pmsitem->drug_frequency ?></td>
                    <td><?= $pmsitem->getStatusDesc() ?></td>
                    <td><?= $pmsitem->auditremark ?></td>
                </tr>
            <?php } ?>
            </table>
        </div>
    <?php } else { ?>
        <?= $a->patient->name ?>提交了核对用药
        <?= $pmsheet->auditremark ?>
    <?php } ?>
<?php } ?>
</div>
