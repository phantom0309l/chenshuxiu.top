<?php
/*
 * PatientRemarkDao
 */
class PatientRemarkDao extends Dao
{
    // 名称: getByRevisitrecordidName
    // 备注:
    // 创建:
    // 修改:
    public static function getByRevisitrecordidName ($revisitrecordid, $name) {
        $cond = ' and revisitrecordid = :revisitrecordid and name = :name
            order by id asc
            limit 1';

        $bind = array(
            ':revisitrecordid' => $revisitrecordid,
            ':name' => $name);

        return Dao::getEntityByCond('PatientRemark', $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid, $typestr = '') {
        $cond = ' and patientid = :patientid ';

        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($typestr) {
            $cond .= ' and typestr = :typestr ';
            $bind[':typestr'] = $typestr;
        }

        $cond .= ' order by thedate asc ';

        return Dao::getEntityListByCond('PatientRemark', $cond, $bind);
    }

    // 名称: getListByRevisitrecordid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByRevisitrecordid ($revisitrecordid) {
        $cond = ' and revisitrecordid = :revisitrecordid order by id asc';
        $bind = array(
            ':revisitrecordid' => $revisitrecordid);

        return Dao::getEntityListByCond('PatientRemark', $cond, $bind);
    }

    public static function getLastByPatientidDoctoridAndName($patientid, $doctorid, $name) {
        $cond = ' AND patientid = :patientid AND doctorid = :doctorid AND name = :name
            ORDER BY id DESC
            LIMIT 1';

        $bind = array(
            ':patientid' => $patientid,
            ':doctorid' => $doctorid,
            ':name' => $name);

        return Dao::getEntityByCond('PatientRemark', $cond, $bind);
    }

}
