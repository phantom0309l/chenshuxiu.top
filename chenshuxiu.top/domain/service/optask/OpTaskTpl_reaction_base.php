<?php

// 肿瘤不良反应收集任务, 基类
class OpTaskTpl_reaction_base extends OpTaskTplBase
{

    // 钩子实现: to_reaction_treat => 不良反应治疗
    public static function to_reaction_treat (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // 生成新治疗任务
        // 创建开始于约定治疗日期的新的[肿瘤不良反应治疗]任务
        OpTaskService::tryCreateOpTask_reaction_treat($optask->patient, $optask->obj, $auditorid);
    }

    // 钩子实现: to_reaction_observe => 不良反应观察
    public static function to_reaction_observe (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭当前任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // 生成新治疗任务
        // 创建7天后的[肿瘤不良反应观察]任务
        OpTaskService::tryCreateOpTask_reaction_observe($optask->patient, $optask->obj, $auditorid);
    }
}
