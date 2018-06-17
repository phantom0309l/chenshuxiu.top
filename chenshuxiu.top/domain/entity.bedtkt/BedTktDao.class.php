<?php
/*
 * BedTktDao
 */
class BedTktDao extends Dao
{
    public static function getByPatientEdit( Patient $patient, $typestr = 'treat' ){
        $cond = " AND patientid = :patientid AND status=0 AND typestr=:typestr ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':typestr'] = $typestr;

        return Dao::getEntityByCond("BedTkt", $cond, $bind);
    }

    public static function getByPatientOpen( Patient $patient , $typestr = 'treat' ){
        $statusstr = BedTkt::getOpenStatusStr();

        $cond = " AND patientid = :patientid AND status in ({$statusstr}) AND typestr=:typestr  ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':typestr'] = $typestr;

        return Dao::getEntityByCond("BedTkt", $cond, $bind);
    }

    public static function getByPatientStatus( Patient $patient, $status , $typestr = 'treat' ) {

        $cond = " AND patientid = :patientid AND status = :status AND typestr=:typestr  ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':status'] = $status;
        $bind[':typestr'] = $typestr;

        return Dao::getEntityByCond("BedTkt", $cond, $bind);
    }

}
