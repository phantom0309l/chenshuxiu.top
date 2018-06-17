<?php

/*
 * ADRMonitorRuleDao
 */

class ADRMonitorRuleItemDao extends Dao
{
    public static function getListByADRMRid($adrmonitorruleid) {
        $cond = " AND adrmonitorruleid = :adrmonitorruleid";
        $bind = [
            ':adrmonitorruleid' => $adrmonitorruleid
        ];
        return Dao::getEntityListByCond("ADRMonitorRuleItem", $cond, $bind);
    }

    // 按监测项目去重
    public static function getGroupListByMedicineidAndDiseaseid($medicineid, $diseaseid) {
        $sql = "SELECT a.*
                FROM adrmonitorruleitems a
                LEFT JOIN adrmonitorrules b ON b.id = a.adrmonitorruleid
                WHERE b.medicineid = :medicineid 
                AND b.diseaseid = :diseaseid 
                GROUP BY a.ename";
        $bind = [
            ":medicineid" => $medicineid,
            ":diseaseid" => $diseaseid,
        ];
        return Dao::loadEntityList("ADRMonitorRuleItem", $sql, $bind);
    }

    public static function getByMedicineidAndDiseaseidAndWeekAndEname($medicineid, $diseaseid, $week, $ename) {
        $sql = "SELECT a.*
                FROM adrmonitorruleitems a
                LEFT JOIN adrmonitorrules b ON b.id = a.adrmonitorruleid
                WHERE b.medicineid = :medicineid 
                AND b.diseaseid = :diseaseid 
                AND a.week_from <= :week 
                AND a.week_to > :week 
                AND a.ename = :ename";
        $bind = [
            ":medicineid" => $medicineid,
            ":diseaseid" => $diseaseid,
            ":week" => $week,
            ":ename" => $ename,
        ];
        return Dao::loadEntity("ADRMonitorRuleItem", $sql, $bind);
    }

    public static function getByADRMonitorRuleidAndSectionAndIntervalAndEname($adrmonitorruleid, $week_from, $week_to, $week_interval, $ename) {
        $cond = " AND adrmonitorruleid = :adrmonitorruleid AND week_from = :week_from AND week_to = :week_to AND week_interval = :week_interval AND ename = :ename ";
        $bind = [
            ":adrmonitorruleid" => $adrmonitorruleid,
            ":week_from" => $week_from,
            ":week_to" => $week_to,
            ":week_interval" => $week_interval,
            ":ename" => $ename,
        ];

        return Dao::getEntityByCond("ADRMonitorRuleItem", $cond, $bind);
    }
}