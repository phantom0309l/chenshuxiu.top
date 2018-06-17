<?php

// 肿瘤-定期随访
class OpTaskTpl_follow_regular_follow extends OpTaskTplBase
{

    // 钩子实现: to_finish_after, 生成任务: 肿瘤定期随访任务 (患者唯一)
    public static function to_finish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        $plantime = date('Y-m-d', strtotime('+2 month', strtotime($optask->first_plantime)));

        OpTaskService::tryCreateOpTaskByPatient($optask->patient, 'follow:regular_follow', $obj = null, $plantime, $auditorid);
    }

    // 钩子实现: to_unfinish_after
    public static function to_unfinish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }
}