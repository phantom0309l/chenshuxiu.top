<?php
/*
 * Rpt_date_patientpgroupDao
 */
class Rpt_date_patientpgroupDao extends Dao
{
    // 名称: getOneByThedate
    // 备注:
    // 创建:
    // 修改:
    protected static $_database = 'statdb';
    public static function getOneByThedate ($thedate) {
        $cond = "AND thedate = :thedate";

        $bind = [];
        $bind[':thedate'] = $thedate;

        return Dao::getEntityByCond("Rpt_date_patientpgroup", $cond, $bind, self::$_database);
    }

}
