<?php

// 药物到期提醒
class OpTaskTpl_remind_medicineBreak extends OpTaskTplBase
{

    // 钩子实现: to_finish1, 已购药(关闭)
    public static function to_finish1 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish2, 药量充足，设置任务(关闭)
    public static function to_finish2 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish3, 需复诊，设置任务(关闭)
    public static function to_finish3 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish4, 遵医嘱停药(关闭)
    public static function to_finish4 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish5, 自行停药(关闭)
    public static function to_finish5 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish6, 失联(关闭)
    public static function to_finish6 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }
}
