<?php $patientmedicinepkg = $a->obj;
$patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($patientmedicinepkg->id);
?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
        <button class="TriggerBtn btn btn-default btn-sm">展开</button>
    </div>
    <div class="TriggerContent colorBox none">
        <table class="table tdcenter">
            <thead>
                <tr>
                    <th>药名</th>
                    <th>剂量</th>
                    <th>频率</th>
                    <th>调药方案</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
        		<?php
        foreach ($patientmedicinepkgitems as $patientmedicinepkgitem) {
        ?>
            	<tr>
                    <td><?=$patientmedicinepkgitem->medicine->name ?></td>
                    <td><?=$patientmedicinepkgitem->drug_dose ?></td>
                    <td><?=$patientmedicinepkgitem->getDrug_frequencyStr(); ?></td>
                    <td><?=$patientmedicinepkgitem->drug_change ?></td>
                    <td>
                        <a href="/patientmedicinepkgitemmgr/modify?patientmedicinepkgitemid=<?=$patientmedicinepkgitem->id ?>">修改</a>
                    </td>
                </tr>
                    <?php
        }
        ?>
        	</tbody>
        </table>
    </div>
</div>
