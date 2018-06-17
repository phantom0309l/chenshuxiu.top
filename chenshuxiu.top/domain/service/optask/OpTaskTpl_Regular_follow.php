<?php

// 肿瘤定期随访任务, 没用着
class OpTaskTpl_Regular_follow extends OpTaskTplBase
{

    // 钩子实现: to_finish_after, 生成任务: 肿瘤定期随访任务 (患者唯一)
    public static function to_finish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 生成任务: 肿瘤定期随访任务 (患者唯一)
        OpTaskService::tryCreateOpTask_Regular_follow($optask->patient, $auditorid);
    }

    // 钩子实现: to_unfinish_after
    public static function to_unfinish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_refuse_after
    public static function to_refuse_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_time_out_close_after
    public static function to_time_out_close_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }
}
