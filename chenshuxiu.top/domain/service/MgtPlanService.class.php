<?php

// 创建: 20171220 by txj
class MgtPlanService
{
    // 尝试加入六院管理计划 报到时
    public static function tryJoin_pkuh6_when_baodao(Patient $patient, $create_join_optask = false){
        //六院医生
        $doctorid_arr = array(1,2,3,537);
        $doctor = $patient->doctor;
        if(in_array($doctor->id, $doctorid_arr)){
            $isHezuo = $doctor->isHezuo("Lilly");
            if($isHezuo){
                return false;
            }else{
                $mgtPlan = MgtPlanDao::getByEname("pkuh6");
                self::tryJoinImp($patient, $mgtPlan, $create_join_optask, "报到时加入管理计划");
                return true;
            }
        }else{
            return false;
        }
    }

    // 尝试加入六院管理计划 礼来入组任务关闭时
    public static function tryJoin_pkuh6_when_closeSunflowerOptask(Patient $patient, $create_join_optask = false){
        //六院医生
        $doctorid_arr = array(1,2,3,537);
        $doctor = $patient->doctor;
        if(in_array($doctor->id, $doctorid_arr)){
            $isInHezuo = $patient->isInHezuo("Lilly");
            if($isInHezuo){
                return false;
            }else{
                $mgtPlan = MgtPlanDao::getByEname("pkuh6");
                self::tryJoinImp($patient, $mgtPlan, $create_join_optask, "关闭礼来首次电话后加入管理计划");
                return true;
            }
        }else{
            return false;
        }
    }

    private static function tryJoinImp($patient, $mgtPlan, $create_join_optask = false, $remark = ""){
        $mgtplanid = $mgtPlan->id;
        if($mgtplanid == $patient->mgtplanid){
            Debug::warn("patient[{$patient->id}]mgtplan[{$mgtplanid}]不要重复入管理计划");
            return;
        }
        $patient->mgtplanid = $mgtplanid;

        // 管理计划变更日志
        $row = [];
        $row['patientid'] = $patient->id;
        $row['mgtplanid'] = $mgtplanid;
        $row['type'] = MgtPlan::type_join;
        $row['auditorid'] = 1;
        $row['remark'] = $remark;
        Patient_mgtplan_log::createByBiz($row);

        //入管理计划后，创建跟进任务
        if($create_join_optask){
            OpTaskService::tryCreateOpTaskByPatient($patient, 'mgtplan:join');
        }
    }

}
