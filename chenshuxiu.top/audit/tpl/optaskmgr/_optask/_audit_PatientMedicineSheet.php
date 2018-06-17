<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    include $tpl . "/_pagetitle.php"; ?>
    <div class="optaskContent">
        <div style="padding: 20px">
            <?php
                $patientmedicinesheet = $optask->obj;
                $patientmedicinesheetitems = PatientMedicineSheetItemDao::getListByPatientmedicinesheetid($patientmedicinesheet->id);
                ?>
                <h4 style="display: inline-block;"><?=$patientmedicinesheet->thedate?></h4>
                <table class="table table-bordered table-striped ">
                    <thead>
                    <tr>
                        <th>用药名称</th>
                        <th>用药剂量</th>
                        <th>频次</th>
                        <th>医嘱用药量</th>
                        <th>医嘱频次</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($patientmedicinesheetitems as $a) {
                        $dose_color = '';
                        $frequency_color = '';
                        $o_dose = $a->drug_dose;
                        $o_frequency = $a->drug_frequency;
                        $t_dose = $a->target_drug_dose;
                        $t_frequency = $a->target_drug_frequency;
                        if( $o_dose != $t_dose ){
                            $dose_color = " style='background:#eca4a4' ";
                        }
                        if( $o_frequency != $t_frequency ){
                            $frequency_color = " style='background:#eca4a4' ";
                        }
                        ?>
                        <tr>
                            <td><?= $a->medicine->name ?></td>
                            <td <?= $dose_color ?>><?= $o_dose ?> </td>
                            <td <?= $frequency_color?>><?= $o_frequency?></td>
                            <td><?= $t_dose?></td>
                            <td><?= $t_frequency ?></td>
                        </tr>
                        <?php
                    }
                    ?>

                    </tbody>
                </table>
                <div class="mt10 pull-right">
                    <a target="_blank" href="/patientmedicinetargetmgr/detailofpatient?patientid=<?=$patient->id?>" class="btn btn-success btn-sm"><i class="fa fa-hand-o-right"></i> 核对用药</a>
                </div>
        </div>
    </div>
</div>
