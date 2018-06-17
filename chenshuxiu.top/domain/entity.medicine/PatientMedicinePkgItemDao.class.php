<?php
// PatientMedicinePkgItemDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class PatientMedicinePkgItemDao extends Dao
{
    // 名称: getByPatientmedicinepkgidMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientmedicinepkgidMedicineid ($patientmedicinepkgid, $medicineid) {
        $cond = " and patientmedicinepkgid=:patientmedicinepkgid and medicineid=:medicineid limit 1 ";
        $bind = array(
            ':patientmedicinepkgid' => $patientmedicinepkgid,
            ':medicineid' => $medicineid);

        return Dao::getEntityByCond('PatientMedicinePkgItem', $cond, $bind);
    }

    // 名称: getListByPatientmedicinepkgid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientmedicinepkgid ($patientmedicinepkgid) {
        $sql = "select pmg.* from patientmedicinepkgitems pmg
                JOIN doctormedicinerefs dmr ON pmg.medicineid=dmr.medicineid
                WHERE pmg.patientmedicinepkgid=:patientmedicinepkgid
                AND dmr.doctorid=:doctorid
                ORDER BY dmr.pos asc";

        $patientmedicinepkg = PatientMedicinePkg::getById($patientmedicinepkgid);

        // done pcard fix , TODO by sjp 20160804 : 这个函数也很奇怪,为啥非要联表呢?
        $revisitrecord = $patientmedicinepkg->revisitrecord;

        $bind = array(
            ":patientmedicinepkgid" => $patientmedicinepkgid,
            ":doctorid" => $revisitrecord->doctorid);

        return Dao::loadEntityList('PatientMedicinePkgItem', $sql, $bind);
    }
}