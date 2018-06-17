<?php
/*
 * PatientRecordTplDao
 */
class PatientRecordTplDao extends Dao {

    // 获取List通过疾病组
    public static function getListByDiseaseGroup(DiseaseGroup $diseaseGroup) {
        $cond = " and diseasegroupid = :diseasegroupid";
        $bind = [];
        $bind[':diseasegroupid'] = $diseaseGroup->id;

        return Dao::getEntityListByCond('PatientRecordTpl', $cond, $bind);
    }

    // 获取显示列表 通过疾病组
    public static function getIsShowListByDiseaseGroup(DiseaseGroup $diseaseGroup) {
        $cond = " and diseasegroupid = :diseasegroupid and is_show = 1";
        $bind = [];
        $bind[':diseasegroupid'] = $diseaseGroup->id;

        return Dao::getEntityListByCond('PatientRecordTpl', $cond, $bind);
    }
}
