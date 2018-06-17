<?php
/*
 * PatientDrugStateDao
 */
class PatientDrugStateDao extends Dao {
    // 获取当前已支付订单
    public static function getLastByPatient (Patient $patient) {
        $cond = " and patientid = :patientid order by pos desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityByCond('PatientDrugState', $cond, $bind);
    }

}
