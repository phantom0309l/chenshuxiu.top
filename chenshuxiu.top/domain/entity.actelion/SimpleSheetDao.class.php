<?php
/*
 * SimpleSheetDao
 */
class SimpleSheetDao extends Dao
{
    public static function getListBySimpleSheetTpl (SimpleSheetTpl $simplesheettpl) {
        $cond = " and simplesheettplid = :simplesheettplid order by id desc ";
        $bind = [];
        $bind[':simplesheettplid'] = $simplesheettpl->id;

        return Dao::getEntityListByCond('SimpleSheet', $cond, $bind);
    }

    public static function getByPatientidSimpleSheetTplidThedate ($patientid, $simplesheettplid, $thedate) {
        $cond = " and patientid = :patientid and simplesheettplid = :simplesheettplid and thedate = :thedate ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':simplesheettplid'] = $simplesheettplid;
        $bind[':thedate'] = $thedate;

        return Dao::getEntityByCond('SimpleSheet', $cond, $bind);
    }
}