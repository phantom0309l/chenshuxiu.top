<?php

// 用药核对
class OpTaskTpl_drug_vouch extends OpTaskTplBase
{

    public static function to_no_medicine (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 任务关闭
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // MARK: - #4727 清空当前医嘱用药
        // 跟冯伟讨论，决定不清空医嘱用药了
        // $pmTargets =
        // PatientMedicineTargetDao::getListByPatientIdAndDoctorId($optask->patientid,
        // $optask->doctorid);
        // foreach ($pmTargets as $pmTarget) {
        // $pmTarget->remove();
        // }

        // MARK: - #4727 关闭【核对用药业务】
        $optask->patient->is_medicine_check = 0;

        // MARK: - #4727 关闭【不良反应监测业务】
        PADRMonitor_AutoService::closeMonitor($optask->patient);
    }
}
