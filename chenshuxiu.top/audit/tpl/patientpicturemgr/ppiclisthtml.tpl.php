<?php
if($patientpicture instanceof PatientPicture){
    $sheets = $patientpicture->getPictureDataSheets();
}
$ismodifygroup = 1;
if(empty($sheets)){
    $ismodifygroup = 0;
}
?>
<div class="">
    <h5>
    <?= $thedate ?>当天归档
    <a href="javascript:" class="btn btn-success btn-sm addgroup-btn pull-right" style="margin-top:-10px;">创建新的</a>
    </h5>
</div>
<div id="ppiclisthtml-data" data-thisppid=<?= $patientpicture->id?>  data-ismodifygroup=<?= $ismodifygroup?>></div>
<?php foreach($patientpictures_date as $a){?>
    <div class="btn btn-warning modifygroup-btn" data-targetppid="<?= $a->id ?>"><?= $a->title?></div>
<?php }?>
<div id="sheettpl-Box"></div>
