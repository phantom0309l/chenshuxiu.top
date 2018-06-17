<?php
/*
 * Rpt_date_patientDao
 */
class Rpt_date_patientDao extends Dao
{
    // 名称: getByThedate
    // 备注:某日的报表对象
    // 创建:
    // 修改:
    protected static $_database = 'statdb';
    public static function getByThedate ($thedate) {
        $cond = "AND thedate = :thedate ";

        $bind = [];
        $bind[':thedate'] = $thedate;

        return Dao::getEntityByCond("Rpt_date_patient", $cond, $bind, self::$_database);
    }
}
