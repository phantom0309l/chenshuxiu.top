<?php

// 下单跟进任务
class OpTaskTpl_shoporder_normalfollow extends OpTaskTplBase
{

    // 钩子实现: to_finish_and_set_follow, 设置复诊跟进任务(关闭)
    public static function to_finish_and_set_follow (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: finish_and_set_drugdate, 设置剩余药量日期(关闭)
    public static function to_finish_and_set_drugdate (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: fail_and_set_drugdate, 失联，预估剩余药量日期(关闭)
    public static function to_fail_and_set_drugdate (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }

    // 钩子实现: refunddrug_and_giveup, 已退药，放弃跟进(关闭)
    public static function to_refunddrug_and_giveup (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }
}
