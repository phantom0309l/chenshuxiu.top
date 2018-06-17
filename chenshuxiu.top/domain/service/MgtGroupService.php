<?php
/**
 * Created by PhpStorm.
 * User: qiaoxiaojin
 * Date: 18/5/30
 * Time: 下午4:27
 */

class MgtGroupService
{
    // 尝试加入六院管理计划 报到时
    public static function tryJoin_pkuh6_when_baodao(Patient $patient, $create_join_optask = false) {
        //六院医生
        $doctorid_arr = array(1, 2, 3, 537);
        $doctor = $patient->doctor;
        if (in_array($doctor->id, $doctorid_arr)) {
            $isHezuo = $doctor->isHezuo("Lilly");
            if ($isHezuo) {
                return false;
            } else {
                $mgtGroupTpl = MgtGroupTplDao::getByEname("pkuh6");
                self::tryJoinImp($patient, $mgtGroupTpl, $create_join_optask);
                return true;
            }
        } else {
            return false;
        }
    }

    // 尝试加入六院管理计划 礼来入组任务关闭时
    public static function tryJoin_pkuh6_when_closeSunflowerOptask(Patient $patient, $create_join_optask = false) {
        //六院医生
        $doctorid_arr = array(1, 2, 3, 537);
        $doctor = $patient->doctor;
        if (in_array($doctor->id, $doctorid_arr)) {
            $isInHezuo = $patient->isInHezuo("Lilly");
            if ($isInHezuo) {
                return false;
            } else {
                $mgtGroupTpl = MgtGroupTplDao::getByEname("pkuh6");
                self::tryJoinImp($patient, $mgtGroupTpl, $create_join_optask);
                return true;
            }
        } else {
            return false;
        }
    }

    private static function tryJoinImp($patient, $mgtGroupTpl, $create_join_optask = false) {
        $mgtgrouptplid = $mgtGroupTpl->id;
        if ($mgtgrouptplid == $patient->mgtgrouptplid) {
            Debug::warn("patient[{$patient->id}]mgtgrouptplid[{$mgtgrouptplid}]不要重复入管理管理组");
            return;
        }

        //生成mgtgroup
        $row = array();
        $row["wxuserid"] = $patient->createuser->createwxuserid;
        $row["userid"] = $patient->createuserid;
        $row["patientid"] = $patient->id;
        $row["mgtgrouptplid"] = $mgtGroupTpl->id;
        $row["startdate"] = date("Y-m-d");
        $row["status"] = 1;
        $cnt = MgtGroupDao::getCntByPatient($patient);
        $row["pos"] = $cnt + 1;
        MgtGroup::createByBiz($row);

        //加入管理组
        $patient->mgtgrouptplid = $mgtgrouptplid;

        //入管理计划后，创建跟进任务
        if ($create_join_optask) {
            OpTaskService::tryCreateOpTaskByPatient($patient, 'mgtplan:join');
        }
    }
}