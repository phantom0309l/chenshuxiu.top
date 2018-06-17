<?php

/*
 * AuditorDiseaseRefDao
 */
class AuditorDiseaseRefDao extends Dao
{

    // 名称: getListByAuditor
    public static function getListByAuditor (Auditor $auditor) {
        $cond = " and auditorid = :auditorid ";

        $bind = [];
        $bind[':auditorid'] = $auditor->id;

        return Dao::getEntityListByCond("AuditorDiseaseRef", $cond, $bind);
    }

    // 名称: getOne
    // 备注:
    // 创建:
    // 修改:
    public static function getOne ($condEx = "") {
        $cond = " {$condEx}";
        return Dao::getEntityByCond("AuditorDiseaseRef", $cond);
    }

    // 名称: getOneByOptasktplidAuditorid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByAuditoridDiseaseid ($auditorid, $diseaseid) {
        $cond = " and diseaseid = :diseaseid and auditorid = :auditorid";
        $bind = [];
        $bind[":diseaseid"] = $diseaseid;
        $bind[":auditorid"] = $auditorid;
        return Dao::getEntityByCond("AuditorDiseaseRef", $cond, $bind);
    }

    public static function getListByDiseaseid($diseaseid) {
        $cond = " AND diseaseid = :diseaseid ";

        $bind = [
            ':diseaseid' => $diseaseid
        ];
        return Dao::getEntityByCond('AuditorDiseaseRef', $cond, $bind);
    }
}
