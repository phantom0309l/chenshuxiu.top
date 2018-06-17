<?php
/**
 * Created by PhpStorm.
 * User: liufei
 * Date: 18-4-3
 * Time: 下午7:09
 */

class OpTaskTpl_BaseInfo_collection extends OpTaskTplBase {
    // 钩子实现: to_unfinish_after
    public static function to_unfinish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 置未完成关闭，转管理组，创建7天后定期随访任务。 #5907
        $patientgroup_guanli = PatientGroupDao::getByTitle('管理组');
        $optask->patient->patientgroupid = $patientgroup_guanli->id;

        $arr = [];
        $arr["content"] = "基本信息填写任务自动关闭，创建+7天的定期随访任务";
        $plantime = date('Y-m-d', strtotime('+7 day', strtotime($optask->first_plantime)));
        return OpTaskService::createPatientOpTask($optask->patient, 'follow:regular_follow', null, $plantime, $auditorid, $arr);
    }
}