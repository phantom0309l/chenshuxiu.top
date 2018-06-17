<div id="patientbasehtmlBox">
<?php
// 一排按钮
include $tpl . "/patientmgr/_patientbase_buttons.php";
//电话呼叫
include $tpl . "/patientmgr/_patientbase_call.php";
// 患者基本资料区
include $tpl . "/patientmgr/_patientbase_info.php";
//患者最新用药
include $tpl . "/_patient_medicine.php";
if ($patient->diseaseid == 1) { include $tpl . "/_set_medicine_break_date.php"; }

// Optask
include $tpl . "/patientmgr/_patientbase_optask.php";
// 运营备注
include $tpl . "/patientmgr/_patientbase_remarkbox.php";
// 医助代填量表
include $tpl . "/patientmgr/_patientbase_papertpls.php";
// 快捷回复
include $tpl . "/patientmgr/_patientbase_reply.php";
?>
</div>
