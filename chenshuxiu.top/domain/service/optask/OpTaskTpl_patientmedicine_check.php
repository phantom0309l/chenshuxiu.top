<?php

// 用药核对【新】（hades: 暂时只用于肺癌，只是暂时，冯伟说以后别疾病核对也用这个）
class OpTaskTpl_patientmedicine_check extends OpTaskTplBase
{

    // 钩子实现: to_finish_after, 关闭任务、根据逻辑创建下一次用药核对
    public static function to_finish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        $row = [];
        $row["patientid"] = $optask->patientid;
        $row["type"] = "targeted_drug";
        $pmCheck = PatientMedicineCheck::createByBiz($row);

        // 生成任务:用药核对【新】, 创建28天后的新任务
        $plantime = date('Y-m-d', strtotime('+2 month', strtotime($optask->first_plantime)));
        return OpTaskService::createPatientOpTask($optask->patient, 'patientmedicine:check', $pmCheck, $plantime, $auditorid);
    }

    public static function to_unfinish_after(OpTask $optask, $opnodeflow, $auditorid = 0, $exArr = []) {
        $row = [];
        $row["patientid"] = $optask->patientid;
        $row["type"] = "targeted_drug";
        $pmCheck = PatientMedicineCheck::createByBiz($row);

        // 重新创建：根结点日期+2月后新的任务 #5907
        $plantime = date('Y-m-d', strtotime('+2 month', strtotime($optask->first_plantime)));
        return OpTaskService::createPatientOpTask($optask->patient, 'patientmedicine:check', $pmCheck, $plantime, $auditorid);
    }

    // 钩子实现: to_refuse_after, 关闭任务、根据逻辑创建下一次用药核对
    public static function to_refuse_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        return self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_time_out_close_after, 关闭任务、根据逻辑创建下一次用药核对
    public static function to_time_out_close_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        return self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }
}
