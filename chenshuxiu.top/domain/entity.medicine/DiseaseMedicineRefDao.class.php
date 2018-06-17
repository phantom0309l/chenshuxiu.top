<?php

/*
 * DiseaseMedicineRefDao
 */
class DiseaseMedicineRefDao extends Dao
{

    // 名称: getByDiseaseAndMedicine
    // 备注:
    // 创建:
    // 修改:
    public static function getByDiseaseAndMedicine (Disease $disease, Medicine $medicine) {
        $cond = "AND diseaseid=:diseaseid AND medicineid=:medicineid ";
        $bind = array(
            ':diseaseid' => $disease->id,
            ':medicineid' => $medicine->id);
        return Dao::getEntityByCond("DiseaseMedicineRef", $cond, $bind);
    }

    // 名称: getListByDisease
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDisease (Disease $disease) {
        return self::getListByDiseaseid($disease->id);
    }

    // 名称: getListByDiseaseid
    // 备注:
    // 创建:
    // 修改: xuzhe 20160928
    public static function getListByDiseaseid ($diseaseid = 0, $groupstr = '') {
        $condfix = '';
        $bind = [];
        if ($diseaseid) {
            $condfix .= " and dmf.diseaseid=:diseaseid";
            $bind[":diseaseid"] = $diseaseid;
        }
        if ($groupstr) {
            $condfix .= " and m.groupstr=:groupstr";
            $bind[":groupstr"] = $groupstr;
        }

        $sql = " select dmf.*
        from diseasemedicinerefs dmf
        inner join medicines m on m.id=dmf.medicineid
        where 1=1 {$condfix} ";

        return Dao::loadEntityList('DiseaseMedicineRef', $sql, $bind);
    }

    // 名称: getListByMedicine
    // 备注:
    // 创建:
    // 修改:
    public static function getListByMedicine (Medicine $medicine) {
        $cond = "AND medicineid=:medicineid order by level desc, pos , id";
        $bind = array(
            ':medicineid' => $medicine->id);
        return Dao::getEntityListByCond("DiseaseMedicineRef", $cond, $bind);
    }
}
