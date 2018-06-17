<?php
$sheets = array();
if($patientpicture instanceof PatientPicture){
    $sheets = $patientpicture->getPictureDataSheets();
}
$targetpp = PatientPicture::getById($targetppid);
$title = '';
if( $targetpp instanceof PatientPicture ){
    $sheets = $targetpp->getPictureDataSheets();
    $title = $targetpp->title;
}

$sheetidarr = array();

foreach ($sheets as $sheet) {
    $sheetidarr[] = $sheet->id;
}

$sheetids = implode(',',$sheetidarr)
?>
<div class="push-20-t">
    <span for="thistitle" class="push-10-r">图片标题</span>
    <input class="form-control" style="display:inline-block;width:50%" type="text" id="thistitle" name="title" value="<?= $patientpicture->title ?>">
    <input type="hidden" id="thisppid" name="thisppid" value="<?= $patientpicture->id ?>"/>
    <input type="hidden" id="targetppid" name="targetppid" value="<?= $targetppid ?>">
    <input type="hidden" id="sheetids" name="sheetids" value="<?= $sheetids ?>">
    <?php foreach($picturedatasheettpls as $a){?>
        <div class="btn btn-primary addsheet-btn" data-picturedatasheettplid="<?= $a->id ?>"
            data-title="<?= $a->title ?>"><?= $a->title?></div>
    <?php }?>
</div>
<div class="push-10-t">
    <div id="sheet-Box"></div>
    <textarea class="form-control" id="onesheetcontent" rows="3" style="width:100%;margin-top:10px;"><?= $patientpicture->content ?></textarea>
</div>
