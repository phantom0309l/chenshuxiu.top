<?php

// 血常规收集任务, 基类
class OpTaskTpl_wbc_base extends OpTaskTplBase
{

    // 钩子实现: to_finish_after, 尝试生成新任务[血常规收集任务]
    public static function to_finish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 尝试生成新任务[血常规收集任务]
        OpTaskService::tryCreateOpTask_wbc_collection($optask->patient, $optask->obj, $auditorid);
    }

    // 钩子实现: to_unfinish_after, 尝试生成新任务[血常规收集任务]
    public static function to_unfinish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        if ($optask->optasktpl->subcode == 'observe') {
            // 生成任务: 选择日期3天后的[血常规收集(观察)]任务
            OpTaskService::tryCreateOpTask_wbc_observe_after_3days($optask->patient, $optask->obj, $auditorid);
        } elseif ($optask->optasktpl->subcode == 'treat') {
            // 生成任务: 选择日期3天后的[血常规收集(治疗)]任务
            OpTaskService::tryCreateOpTask_wbc_treat($optask->patient, $optask->obj, $auditorid);
        } else {
            // 尝试生成新任务[血常规收集任务]
            OpTaskService::tryCreateOpTask_wbc_collection($optask->patient, $optask->obj, $auditorid);
        }
    }

    // 钩子实现: to_refuse_after, 尝试生成新任务[血常规收集任务]
    public static function to_refuse_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_unfinish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_time_out_close_after, 尝试生成新任务[血常规收集任务]
    public static function to_time_out_close_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_unfinish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_wbc_treat
    public static function to_wbc_treat (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // 转到完成节点
        $opnode_finish = OpNodeDao::getByCodeOpTaskTplId('finish', $optask->optasktplid);
        $optask->opnodeid = $opnode_finish->id;

        // 生成新治疗任务
        OpTaskService::tryCreateOpTask_wbc_treat($optask->patient, $optask->obj, $auditorid);
    }

    // 钩子实现: to_wbc_observe
    public static function to_wbc_observe (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // 转到完成节点
        $opnode_finish = OpNodeDao::getByCodeOpTaskTplId('finish', $optask->optasktplid);
        $optask->opnodeid = $opnode_finish->id;

        // 生成任务: 血常规检查日期3天后的[血常规观察任务]任务
        OpTaskService::tryCreateOpTask_wbc_observe_after_3days($optask->patient, $optask->obj, $auditorid);
    }
}
