<?php

// 患者消息任务
class OpTaskTpl_PatientMsg_message extends OpTaskTplBase
{

    /**
     * 根任务=>挂起再跟进
     * 根任务挂起超时之后，本任务关闭
     */
    public static function flow_root_to_again_follow (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 任务关闭
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);
    }
}
