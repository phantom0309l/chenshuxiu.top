<?php
/**
 * Created by PhpStorm.
 * User: liufei
 * Date: 18-4-3
 * Time: 下午7:09
 */

class OpTaskTpl_chemo_collection extends OpTaskTplBase {
    // 钩子实现: to_unfinish_after
    public static function to_unfinish_after (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 未完成关闭，转其他阶段 #5907
        $patientstage_other = PatientStageDao::getByTitle('其他');
        $optask->patient->patientstageid = $patientstage_other->id;
    }
}