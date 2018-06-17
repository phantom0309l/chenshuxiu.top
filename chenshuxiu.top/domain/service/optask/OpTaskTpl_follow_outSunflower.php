<?php

// 跟进[取关退出sunflower]
class OpTaskTpl_follow_outSunflower extends OpTaskTplBase
{

    // 钩子实现: to_finish1, 不用平台，没用(关闭)
    public static function to_finish1 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish2, 误操作(关闭)
    public static function to_finish2 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish3, 换诊断(关闭)
    public static function to_finish3 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: to_finish4, 停换停药(关闭)
    public static function to_finish4 (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }
}
