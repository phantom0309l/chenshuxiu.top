<?php

class OpTaskStatusService
{

    // 改变任务状态
    public static function changeStatus (OpTask $optask, $status, $auditorid = 0) {
        if (! in_array($status, [0, 1, 2])) {
            return;
        }

        if ($optask->status == $status) {
            return;
        }

        if ($status == 1) {
            $optask->donetime = date('Y-m-d H:i:s');
            if ($auditorid > 1) {
                $optask->set4lock("auditorid", $auditorid);
            }

            // 6012 任务归属阶段定义：按照任务关闭时所在阶段计算
            $optask->patientstageid = $optask->patient->patientstageid;

            #5761 如果任务关闭且该任务还有未发送的定时消息，则删除定时消息
            if ($optask->optasktpl->is_auto_send == 1) {
                $unsend_plan_txtmsgs = Plan_txtMsgDao::getUnsentListByObj($optask);
                foreach ($unsend_plan_txtmsgs as $unsend_plan_txtmsg) {
                    $unsend_plan_txtmsg->remove();
                }
            }
        }

        // 任务日志
        $allStatus = OpTask::getAllStatus();
        OpTaskService::addOptLog($optask, "[状态变更] {$allStatus[$optask->status]} => {$allStatus[$status]}", $auditorid);

        $optask->status = $status;

        // 主动提交一次
        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofwork->commitAndInit();
    }

    // 1个月内患者消息数量（文本、图片、语音、量表、核对用药）
    private static function getPatientOneMonthPipeCnt (Patient $patient) {
        $one_month_date = date('Y-m-d', time() - 3600 * 24 * 30);
        $sql = "select count(*)
            from pipes
            where patientid = {$patient->id} and objtype in ('WxTxtMsg', 'WxPicMsg', 'WxVoiceMsg', 'Paper', 'PatientMedicineSheet')
            and createtime >= '{$one_month_date}' ";
        return Dao::queryValue($sql);
    }

    private static function isThreeTime_out_close (OpTask $optask) {
        // 获取该患者前三次（连续）的任务，然后判断是否全是超时关闭
        $cond = " and patientid = :patientid and status = 1
                order by donetime desc
                limit 3 ";
        $bind[':patientid'] = $optask->patientid;
        $three_optasks = Dao::getEntityListByCond('OpTask', $cond, $bind);

        $close_cnt = 0;
        foreach ($three_optasks as $a) {
            if ($a->opnode->code == 'time_out_close') {
                $close_cnt ++;
            }
        }

        // 如果连续三次都是超时关闭，则进入失活组
        if ($close_cnt == 3) {
            return true;
        } else {
            return false;
        }
    }

    private static function isThreeRefuse (OpTask $optask) {
        // 获取该患者前三次（连续）的任务，然后判断是否全是超时关闭
        $cond = " and patientid = :patientid and status = 1
                order by donetime desc
                limit 3 ";
        $bind[':patientid'] = $optask->patientid;
        $three_optasks = Dao::getEntityListByCond('OpTask', $cond, $bind);

        $close_cnt = 0;
        foreach ($three_optasks as $a) {
            if ($a->opnode->code == 'refuse') {
                $close_cnt ++;
            }
        }

        Debug::trace("++++++++++++++ close_cnt = {$close_cnt} ++++++++++++");

        // 如果连续三次都是超时关闭，则进入失活组
        if ($close_cnt == 3) {
            return true;
        } else {
            return false;
        }
    }
}