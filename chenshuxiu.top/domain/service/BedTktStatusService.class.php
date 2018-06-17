<?php
class BedTktStatusService
{

    public static function setBedTktStatus (BedTkt $bedtkt, $status) {
        $beforestatus = $bedtkt->status;
        switch ($status) {
            case BedTkt::WILL_AUDITOR_STATUS:
                // 患者提交预约
                DBC::requireTrue($bedtkt->status == BedTkt::DRAFT_STATUS, '必须从草搞状态才能到待运营审核状态');
                break;
            case BedTkt::PATIENT_CANCEL_STATUS:
                // 患者取消
                DBC::requireTrue($bedtkt->status == BedTkt::WILL_AUDITOR_STATUS || $bedtkt->status == BedTkt::AUDITOR_PASS_STATUS, '患者只有在待运营审核状态或运营通过状态才能取消');
                break;
            case BedTkt::AUDITOR_PASS_STATUS:
                // 运营通过
                DBC::requireTrue($bedtkt->status == BedTkt::WILL_AUDITOR_STATUS, '必须从待运营审核状态才能到运营通过状态');
                break;
            case BedTkt::AUDITOR_REFUSE_STATUS:
                // 运营拒绝
                DBC::requireTrue($bedtkt->status == BedTkt::WILL_AUDITOR_STATUS, '必须从待运营审核状态才能到运营拒绝状态');
                break;
            case BedTkt::DOCTOR_PASS_STATUS:
                // 医生通过
                DBC::requireTrue($bedtkt->status == BedTkt::AUDITOR_PASS_STATUS, '必须从运营通过状态才能到医生通过状态');
                break;
            case BedTkt::DOCTOR_REFUSE_STATUS:
                // 医生拒绝
                DBC::requireTrue($bedtkt->status == BedTkt::AUDITOR_PASS_STATUS, '必须从运营通过状态才能到医生拒绝状态');
                break;
            default:
                DBC::requireTrue(true, '未知状态，不允许改变');
                break;
        }

        $bedtkt->status = $status;
        $typestr_status = BedTkt::TYPESTR_STATUS;

        $bedtkt->saveLog('status_change',"{$typestr_status[$status]}：status from {$beforestatus} to {$status} ");
    }

    public static function setBedTktPatientStatus (BedTkt $bedtkt, $status_by_patient) {
        $bedtkt->status_by_patient = $status_by_patient;
    }
}
