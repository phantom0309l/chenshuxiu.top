<?php
$liverpicture = $patientpicture->obj;
$sheets = $patientpicture->getPictureDataSheets();
$initdate = $patientpicture->getMainPatientPicture()->thedate;
if(empty($sheets)){
    $initdate = $liverpicture->getCreateDay();
}
?>
<div class="panel-base">
    <form id="getppicbythedate-form" method="post">
        <input type="hidden" name="patientpictureid" value="<?= $patientpicture->id ?>" />
        <div style="margin-bottom: 20px;">
            <div class="triangle-blue"></div>
            <span class="question-title"> 检查日期 </span>
            <input type="text" name="thedate" class="calendar getppicbythedate-date answer-box" readonly value="<?= $initdate?>" />
            <span class="getppicbythedate-btn btn btn-success"> 查找 </span>
        </div>
    </form>
</div>

<div id="ppiclisthtml-Box"></div>
