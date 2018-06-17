<?php

class Export_JobDao extends Dao
{

    const EntityName = "Export_Job";

    const TableName = "export_jobs";

    public static function getActiveJobCntByDoctorid ($doctorid) {
        $sql = "SELECT COUNT(*) FROM " . self::TableName . " WHERE doctorid=:doctorid AND status < " . Export_Job::STATUS_FAILED;
        $bind = [
            ':doctorid' => $doctorid];

        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

    public static function getActiveJobCntByAuditorid ($auditorid) {
        $sql = "SELECT COUNT(*) FROM " . self::TableName . " WHERE auditorid = :auditorid AND status < " . Export_Job::STATUS_FAILED;
        $bind = [
            ':auditorid' => $auditorid];

        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

}
