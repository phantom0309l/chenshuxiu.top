<?php

/*
 * DiseaseDao
 */
class DiseaseDao extends Dao
{
    // 名称: getListAll
    // 备注:
    // 创建:
    // 修改:
    public static function getListAll () {
        return Dao::getEntityListByCond("Disease");
    }

    // getDiseaseListByDiseasegroup
    public static function getDiseaseListByDiseasegroup (DiseaseGroup $diseasegroup) {
        $cond = " and diseasegroupid=:diseasegroupid ";
        $bind = [];
        $bind[':diseasegroupid'] = $diseasegroup->id;
        return Dao::getEntityListByCond("Disease", $cond, $bind);
    }

    // getIdsByDiseasegroup
    public static function getIdsByDiseasegroup (DiseaseGroup $diseasegroup) {
        $sql = " SELECT id FROM diseases WHERE diseasegroupid=:diseasegroupid ";

        $bind = [];
        $bind[':diseasegroupid'] = $diseasegroup->id;
        return Dao::queryValues($sql, $bind);
    }
}
