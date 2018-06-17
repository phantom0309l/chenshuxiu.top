<?php

/*
 * PADRMonitorDao
 */

class PADRMonitorDao extends Dao
{
    public static function getGroupListByPatientid($patientid) {
        $sql = "SELECT temp.* FROM 
                (SELECT * FROM padrmonitors WHERE patientid = :patientid ORDER BY plan_date DESC) temp
                GROUP BY temp.adrmonitorruleitem_ename";
        $bind = [
            ":patientid" => $patientid,
        ];

        return Dao::loadEntityList("PADRMonitor", $sql, $bind);
    }

    /**
     * 按创建时间排序，获取分组列表
     *
     * @param $patientid
     * @return array
     */
    public static function getLastCreateGroupListByPatientid($patientid) {
        $sql = "SELECT temp.* FROM 
                (SELECT * FROM padrmonitors WHERE patientid = :patientid ORDER BY createtime DESC) temp
                GROUP BY temp.adrmonitorruleitem_ename
                ORDER BY id ASC";
        $bind = [
            ":patientid" => $patientid,
        ];

        return Dao::loadEntityList("PADRMonitor", $sql, $bind);
    }

    /**
     * 按计划时间排序，获取分组列表
     *
     * @param $patientid
     * @return array
     */
    public static function getLastPlanGroupListByPatientid($patientid) {
        $sql = "SELECT temp.* FROM 
                (SELECT * FROM padrmonitors WHERE patientid = :patientid ORDER BY createtime DESC) temp
                GROUP BY temp.adrmonitorruleitem_ename
                ORDER BY plan_date ASC";
        $bind = [
            ":patientid" => $patientid,
        ];

        return Dao::loadEntityList("PADRMonitor", $sql, $bind);
    }

    public static function getListByPatientidAndEname($patientid, $adrmonitorruleitem_ename) {
        $cond = " AND patientid = :patientid AND adrmonitorruleitem_ename = :adrmonitorruleitem_ename ORDER BY plan_date DESC, the_date DESC";
        $bind = [
            ":patientid" => $patientid,
            ":adrmonitorruleitem_ename" => $adrmonitorruleitem_ename,
        ];

        return Dao::getEntityListByCond("PADRMonitor", $cond, $bind);
    }

    public static function getListByPatientid($patientid, $condEx = '') {
        if ($condEx) {
            $cond = " AND patientid = :patientid {$condEx} ORDER BY plan_date DESC";
        } else {
            $cond = " AND patientid = :patientid ORDER BY plan_date DESC";
        }
        $bind = [
            ":patientid" => $patientid,
        ];

        return Dao::getEntityListByCond("PADRMonitor", $cond, $bind);
    }

    // 获取最后创建的
    public static function getLastCreateByPatientidAndEname($patientid, $adrmonitorruleitem_ename) {
        $cond = " AND patientid = :patientid AND adrmonitorruleitem_ename = :adrmonitorruleitem_ename ORDER BY id DESC";
        $bind = [
            ":patientid" => $patientid,
            ":adrmonitorruleitem_ename" => $adrmonitorruleitem_ename,
        ];

        return Dao::getEntityByCond("PADRMonitor", $cond, $bind);
    }

    // 获取最后监测的
    public static function getLastMonitorByPatientidAndEname($patientid, $adrmonitorruleitem_ename) {
        $cond = " AND patientid = :patientid AND adrmonitorruleitem_ename = :adrmonitorruleitem_ename AND the_date != '0000-00-00' ORDER BY the_date DESC";
        $bind = [
            ":patientid" => $patientid,
            ":adrmonitorruleitem_ename" => $adrmonitorruleitem_ename,
        ];

        return Dao::getEntityByCond("PADRMonitor", $cond, $bind);
    }

    // 获取最后计划监测的
    public static function getLastPlanByPatientidAndEname($patientid, $adrmonitorruleitem_ename) {
        $cond = " AND patientid = :patientid AND adrmonitorruleitem_ename = :adrmonitorruleitem_ename ORDER BY plan_date DESC";
        $bind = [
            ":patientid" => $patientid,
            ":adrmonitorruleitem_ename" => $adrmonitorruleitem_ename,
        ];

        return Dao::getEntityByCond("PADRMonitor", $cond, $bind);
    }
}