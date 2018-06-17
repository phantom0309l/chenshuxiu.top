<?php
/*
 * Actelion_JifenDao
 */
class Actelion_JifenDao extends Dao
{
    public static function getByPatientidObj ($patientid, $obj) {
        $cond = " and patientid = :patientid and objtype = :objtype and objid = :objid limit 1 ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':objtype'] = get_class($obj);
        $bind[':objid'] = $obj->id;

        return Dao::getEntityByCond('Actelion_Jifen', $cond, $bind);
    }
}