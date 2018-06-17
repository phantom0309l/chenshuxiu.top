<?php
/*
 * RecipeDao
 */
class RecipeDao extends Dao
{

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid) {
        $cond = " AND patientid = :patientid ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        return Dao::getEntityListByCond('Recipe', $cond, $bind);
    }

    // 名称: getListByPatientidThedate
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientidThedate ($patientid, $thedate) {
        $cond = " AND patientid = :patientid AND thedate = :thedate";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':thedate'] = $thedate;
        return Dao::getEntityListByCond('Recipe', $cond, $bind);
    }

}
