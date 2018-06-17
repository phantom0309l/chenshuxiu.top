<?php
/*
 * MedicineDao
 */
class MedicineDao extends Dao
{

    // 名称: getByName
    // 备注:
    // 创建:
    // 修改:
    public static function getByName ($name) {
        $cond = ' and ( name=:name or scientificname=:name) ';

        $bind = array(
            ":name" => $name);

        return Dao::getEntityByCond("Medicine", $cond, $bind);
    }

    // 名称: getListAll
    // 备注:
    // 创建:
    // 修改:
    public static function getListAll () {
        return Dao::getEntityListByCond("Medicine");
    }

    // 名称: getListByDiseaseid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDiseaseid ($diseaseid) {
        $refs = DiseaseMedicineRefDao::getListByDiseaseid($diseaseid);
        return array_map(function  ($ref) {
            return $ref->medicine;
        }, $refs);
    }

    // 名称: getListByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctorid ($doctorid) {
        $refs = DoctorMedicineRefDao::getListByDoctorid($doctorid);

        return array_map(function  ($ref) {
            return $ref->medicine;
        }, $refs);
    }

    // 名称: getPatientNotselectedMedicines
    // 备注:
    // 创建:
    // 修改:
    public static function getPatientNotselectedMedicines (PatientMedicinePkg $patientmedicinepkg) {
        // done pcard fix , 应该取 PatientMedicinePkg->doctorid
        $patient = $patientmedicinepkg->patient;

        $medicines = MedicineDao::getListByDoctorid($patient->doctorid);
        $existmedicine_ids = PatientMedicinePkgDao::getPatientmedicineids($patientmedicinepkg->id);

        $arr = array();

        foreach ($medicines as $a) {
            if (false == in_array($a->id, $existmedicine_ids)) {
                $arr[] = $a;
            }
        }

        return $arr;
    }

    // 名称: getRowsBydoctoridAndDiseaseid
    // 备注:
    // 创建:
    // 修改:
    public static function getRowsBydoctoridAndDiseaseid ($doctorid, $diseaseid, $confix = "") {
        $sql = " SELECT count(DISTINCT a.id) AS id, b.medicineid, c.level, d.name, c.diseaseid
            FROM patients a
            INNER JOIN pcards x ON x.patientid = a.id
            INNER JOIN patientmedicinerefs b  ON a.id = b.patientid
            INNER JOIN diseasemedicinerefs c ON b.medicineid = c.medicineid
            INNER JOIN medicines d ON b.medicineid = d.id
            WHERE x.doctorid = :doctorid AND c.diseaseid = :diseaseid AND b.status = 1  ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':diseaseid'] = $diseaseid;

        if ($confix) {
            $sql .= $confix;
        }

        $sql .= " GROUP BY b.medicineid ORDER BY id DESC ";

        return Dao::queryRows($sql, $bind);
    }

    // 名称: getListByGroupstr
    // 备注:
    // 创建: xuzhe 20160926
    // 修改:
    public static function getListByGroupstr( $groupstr ){
        $cond = " and groupstr=:groupstr ";
        $bind = array(
            ':groupstr' => $groupstr
        );

        return Dao::getEntityListByCond( 'Medicine' , $cond, $bind );
    }
}
