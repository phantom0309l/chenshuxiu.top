<?php
// DoctorMedicineRefDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class DoctorMedicineRefDao extends Dao
{
    // 名称: getByDoctoridMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctoridMedicineid ($doctorid, $medicineid) {
        $cond = ' and doctorid=:doctorid and medicineid=:medicineid limit 1 ';

        $bind = array(
            ':doctorid' => $doctorid,
            ':medicineid' => $medicineid);

        return Dao::getEntityByCond('DoctorMedicineRef', $cond, $bind);
    }

    // 名称: getByDoctoridTitle
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctoridTitle ($doctorid, $title) {
        $cond = ' and doctorid=:doctorid and title=:title limit 1 ';

        $bind = array(
            ':doctorid' => $doctorid,
            ':title' => $title);

        return Dao::getEntityByCond('DoctorMedicineRef', $cond, $bind);
    }

    // 名称: getDoctorMedicineIds
    // 备注:
    // 创建:
    // 修改:
    public static function getDoctorMedicineIds ($doctorid) {
        $arr = array();

        $doctormedicinerefs = DoctorMedicineRefDao::getListByDoctorid($doctorid);
        foreach ($doctormedicinerefs as $a) {
            $arr[] = $a->medicineid;
        }

        return $arr;
    }

    // 名称: getListByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctorid ($doctorid) {
        $cond = " and doctorid = :doctorid order by pos asc ";
        $bind = [];

        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond('DoctorMedicineRef', $cond, $bind);
    }

    // 名称: getListByMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByMedicineid ($medicineid) {
        $cond = " and medicineid = :medicineid order by pos asc ";
        $bind = [];

        $bind[':medicineid'] = $medicineid;

        return Dao::getEntityListByCond('DoctorMedicineRef', $cond, $bind);
    }

    // 名称: getCntByMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByMedicineid ($medicineid) {
        $sql = " select count(*)
        from doctormedicinerefs
        where medicineid = :medicineid order by pos asc ";
        $bind = [];

        $bind[':medicineid'] = $medicineid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getMaxPosByDoctoridGroupstr
    // 备注:
    // 创建:
    // 修改:
    public static function getMaxPosByDoctoridGroupstr ($doctorid, $groupstr) {
        $sql = "select *
                from doctormedicinerefs dmr
                inner join medicines m on m.id=dmr.medicineid
                where m.groupstr=:groupstr and dmr.doctorid=:doctorid
                order by dmr.pos desc
                limit 1";

        $bind = array(
            ":groupstr" => $groupstr,
            ":doctorid" => $doctorid);

        $ref = Dao::loadEntity("DoctorMedicineRef", $sql, $bind);

        return $ref->pos;
    }
}