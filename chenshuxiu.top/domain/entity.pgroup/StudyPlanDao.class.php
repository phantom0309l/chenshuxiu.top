<?php
/*
 * StudyPlanDao
 */
class StudyPlanDao extends Dao {

    // 名称: getListByPatientpgrouprefid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientpgrouprefid ($patientpgrouprefid, $condEx="") {
        $cond = " and patientpgrouprefid = :patientpgrouprefid {$condEx} ";
        $bind = [];
        $bind[':patientpgrouprefid'] = $patientpgrouprefid;

        return Dao::getEntityListByCond('StudyPlan', $cond, $bind);
    }

    // 名称: getListByEnddate
    // 备注:
    // 创建:
    // 修改:
    public static function getListByEnddate ($enddate, $condEx="") {
        $cond = " and enddate = :enddate {$condEx} ";
        $bind = [];
        $bind[':enddate'] = $enddate;

        return Dao::getEntityListByCond('StudyPlan', $cond, $bind);
    }

    // 名称: getListByPatientpgrouprefidObjcode
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientpgrouprefidObjcode ($patientpgrouprefid, $objcode, $condEx="") {
        $cond = " and patientpgrouprefid = :patientpgrouprefid and objcode = :objcode {$condEx} ";
        $bind = [];
        $bind[':patientpgrouprefid'] = $patientpgrouprefid;
        $bind[':objcode'] = $objcode;

        return Dao::getEntityListByCond('StudyPlan', $cond, $bind);
    }

    // 名称: getOneByPatientpgrouprefidObjcode
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientpgrouprefidObjcode ($patientpgrouprefid, $objcode, $condEx="") {
        $cond = " and patientpgrouprefid = :patientpgrouprefid and objcode = :objcode {$condEx} ";
        $bind = [];
        $bind[':patientpgrouprefid'] = $patientpgrouprefid;
        $bind[':objcode'] = $objcode;

        return Dao::getEntityByCond('StudyPlan', $cond, $bind);
    }

    // 名称: getOneByPatientpgrouprefidObj
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientpgrouprefidObj ($patientpgrouprefid, $obj, $condEx="") {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $cond = " and patientpgrouprefid = :patientpgrouprefid and objtype = :objtype and objid = :objid {$condEx} ";
        $bind = [];
        $bind[':patientpgrouprefid'] = $patientpgrouprefid;
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityByCond('StudyPlan', $cond, $bind);
    }

}
