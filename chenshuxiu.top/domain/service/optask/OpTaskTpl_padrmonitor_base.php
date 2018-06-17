<?php

// 不良反应监测任务, 基类
class OpTaskTpl_padrmonitor_base extends OpTaskTplBase
{

    // 钩子实现: to_finish_after, 创建下一次[监测]
    public static function to_finish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        $padrmonitor = $optask->obj;

        $padrmonitor_new = PADRMonitor_AutoService::createByPrevDate($padrmonitor->patient, $padrmonitor->diseaseid, "monitor",
                $padrmonitor->adrmonitorruleitem_ename, $padrmonitor->the_date);
    }

    // 钩子实现: to_unfinish_after , 冯伟说：超时、拒绝、完成，都是一样的
    public static function to_unfinish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_refuse_after , 冯伟说：超时、拒绝、完成，都是一样的
    public static function to_refuse_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_time_out_close_after, 冯伟说：超时、拒绝、完成，都是一样的
    public static function to_time_out_close_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        self::to_finish_after($optask, $opnodeflow, $auditorid, $exArr);
    }

    // 钩子实现: to_visit 关闭任务、通过填写的日期创建[就诊]类型的监测
    public static function to_visit (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        $plan_date = $exArr['next_plantime'];

        $padrmonitor = $optask->obj;
        $padrmonitor_new = PADRMonitor_AutoService::createByPlanDate($padrmonitor->patient, $padrmonitor->diseaseid, $padrmonitor->medicineid, "visit",
                $padrmonitor->adrmonitorruleitem_ename, $plan_date, $padrmonitor->the_date);
    }

    // 钩子实现: to_observe 关闭任务、通过填写的日期创建[观察]类型的监测
    public static function to_observe (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        $plan_date = $exArr['next_plantime'];

        $padrmonitor = $optask->obj;
        $padrmonitor_new = PADRMonitor_AutoService::createByPlanDate($padrmonitor->patient, $padrmonitor->diseaseid, $padrmonitor->medicineid, "observe",
                $padrmonitor->adrmonitorruleitem_ename, $plan_date, $padrmonitor->the_date);
    }

    // 钩子实现: to_second_observe, 关闭任务、通过填写的日期创建[二次观察]类型的监测
    public static function to_second_observe (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        $plan_date = $exArr['next_plantime'];

        $padrmonitor = $optask->obj;
        $padrmonitor_new = PADRMonitor_AutoService::createByPlanDate($padrmonitor->patient, $padrmonitor->diseaseid, $padrmonitor->medicineid, "second_observe",
                $padrmonitor->adrmonitorruleitem_ename, $plan_date, $padrmonitor->the_date);
    }
}
