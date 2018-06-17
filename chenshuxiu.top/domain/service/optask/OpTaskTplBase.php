<?php

// 任务处理类-基类
class OpTaskTplBase
{

    // 默认处理: to_finish
    public static function to_finish (OpTask $optask, $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 默认处理: to_unfinish
    public static function to_unfinish (OpTask $optask, $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 默认处理: to_fail
    public static function to_fail (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 默认处理: to_refuse
    public static function to_refuse (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 默认处理: to_time_out_close
    public static function to_time_out_close (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 默认处理: to_other_close
    public static function to_other_close (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }
}
