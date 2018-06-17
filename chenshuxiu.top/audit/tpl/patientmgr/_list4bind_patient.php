<!-- ========================================================Patient======================================================== -->
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center">
    <a class="<?= ($thePatient->id == $a->id)?'red':''; ?>" href="/patientmgr/list4bind?patientid=<?=$a->id ?>"><?=$a->id ?></a>
</td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center" class="col-md-1">
    <?php
    $revisitrecords = array();
    $revisitrecords = RevisitRecordDao::getListByPatientidDoctorid($a->id, $a->doctorid);
    foreach ($revisitrecords as $revisitrecord) {
        ?>
                <span class="thedate"><?=$revisitrecord->thedate?></span>
    <br />
            <?php
    }
    ?>
</td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?>>
<?php
$pcards = $a->getPcards();
foreach ($pcards as $_pcard) {
    ?>
    <span><?=$_pcard->create_patientid?>, <?=$_pcard->doctor->name?>, <?=$_pcard->disease->name?>(<a target="_blank" href="/patientmgr/modifydisease?pcardid=<?=$_pcard->id?>">修改疾病<i class="fa fa-pencil"></i></a>),<?=$_pcard->getYuanNeiStr()?></span>;
    <?php
}
?>
</td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center"><?=$a->first_doctor->name ?></td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center"><?=$a->createtime ?></td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center"><?=$a->name?>
<?= $a->mother_name ? "（".$a->mother_name."）": ""?>
</td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center"><?=$a->birthday ?></td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center"><?=XConst::status_withcolor($a->status); ?></td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center"><?=XConst::auditStatus($a->auditstatus); ?></td>
<td <?=$cnt > 0 ? 'rowspan='.$cnt : ''?> style="text-align: center">
    <?php
    echo $a->getStatusStr() . "<br>";

    if ($a->is_live == 0) {
        echo '[联系技术]';
    } elseif ($a->status == 0) {
        echo "<a href='/patientmgr/onlinepost?patientid={$a->id}'>手工上线</a>";
    } else {
        echo "<a href='/patientmgr/offlinepost?patientid={$a->id}'>手工下线</a>";
    }
    ?>
</td>
