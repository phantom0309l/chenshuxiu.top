<?php
$wxpicmsg = $patientpicture->obj;
$sheets = $patientpicture->getPictureDataSheets();
$initdate = $patientpicture->getMainPatientPicture()->thedate;
if(empty($sheets)){
    $initdate = $wxpicmsg->getCreateDay();
}
?>
<form id="getppicbythedate-form" method="post" class="form-horizontal">
    <input type="hidden" name="patientpictureid" value="<?= $patientpicture->id ?>" />
    <div class="push-20">
        <span class="control-label font-w500 pull-left push-10-r"> 检查日期 </span>
        <div class="visible-xs push-10 clearfix"></div>
        <input type="text" name="thedate" class="calendar getppicbythedate-date answer-box form-control push-10-r" style="width:50%;display:inline-block" value="<?= $initdate?>" />
        <span class="getppicbythedate-btn btn btn-success btn-sm"> 查找 </span>
    </div>
</form>
<div class="clearfix"></div>

<div id="ppiclisthtml-Box"></div>
