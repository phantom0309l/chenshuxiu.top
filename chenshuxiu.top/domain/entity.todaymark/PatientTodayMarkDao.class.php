<?php

/*
 * PatientTodayMarkDao
 */

class PatientTodayMarkDao extends Dao
{
    public static function getListByPatientIdThedate($patientid, $thedate) {
        $cond = "AND patientid = :patientid AND thedate = :thedate";
        
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':thedate'] = $thedate;
        
        return Dao::getEntityListByCond("PatientTodayMark", $cond, $bind);
    }
    
    
}