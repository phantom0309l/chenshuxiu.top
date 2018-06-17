<?php

class CheckupTplMenuDao extends Dao
{

    protected static $entityName = 'CheckupTplMenu';

    protected static $tableName = 'checkuptplmenus';

    // 名称: getByDoctorIdAndDiseaseId
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctorIdAndDiseaseId($doctorid, $diseaseid) {
        $cond = ' AND doctorid=:doctorid AND diseaseid=:diseaseid ';
        $bind = array(
            ':doctorid' => $doctorid,
            ':diseaseid' => $diseaseid);
        return self::getEntityByCond(self::$entityName, $cond, $bind);
    }

    // 名称: getCntByDoctorIdAndDiseaseId
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByDoctorIdAndDiseaseId($doctorid, $diseaseid) {
        $sql = 'SELECT COUNT(*) FROM `' . self::$tableName . '` WHERE doctorid=:doctorid AND diseaseid=:diseaseid';
        $bind = array(
            ':doctorid' => $doctorid,
            ':diseaseid' => $diseaseid);
        return self::queryValue($sql, $bind);
    }

    public static function getListByDoctorid($doctorid) {
        $cond = ' AND doctorid=:doctorid ';
        $bind = [
            ':doctorid' => $doctorid
        ];
        return self::getEntityListByCond(self::$entityName, $cond, $bind);
    }
}
