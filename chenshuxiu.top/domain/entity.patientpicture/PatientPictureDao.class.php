<?php
/*
 * PatientPictureDao
 */
class PatientPictureDao extends Dao
{
    public static function getByObj ($obj) {
        $cond = ' and objid=:objid and objtype=:objtype ';

        $bind = array(
            ':objtype' => get_class($obj),
            ':objid' => $obj->id
        );
        return Dao::getEntityByCond('PatientPicture', $cond, $bind);
    }

    public static function getListByObj ($obj) {
        $cond = ' and objid=:objid and objtype=:objtype ';

        $bind = array(
            ':objtype' => get_class($obj),
            ':objid' => $obj->id
        );
        return Dao::getEntityListByCond('PatientPicture', $cond, $bind);
    }

    public static function getListByParentid ($parent_patientpictureid) {
        $cond = ' and parent_patientpictureid=:parent_patientpictureid ';

        $bind = array(
            ':parent_patientpictureid' => $parent_patientpictureid
        );

        return Dao::getEntityListByCond('PatientPicture', $cond, $bind);
    }

    public static function getListByPatientThedate ( Patient $patient,$thedate, $objtype = '', $isparent=1) {
        $cond = ' and patientid=:patientid and thedate=:thedate ';

        $bind = array(
            ':patientid' => $patient->id,
            ':thedate' => $thedate
        );

        if( $objtype !== '' ){
            $cond .= " and objtype = :objtype ";
            $bind[':objtype'] =$objtype;
        }

        if( $isparent === 1 ){
            $cond .= " and parent_patientpictureid = 0 ";
        }

        return Dao::getEntityListByCond('PatientPicture', $cond, $bind);
    }

    public static function getListByPatientidAndDoctorid ($patientid, $doctorid) {
        $cond = ' AND patientid = :patientid AND (doctorid = :doctorid OR doctorid = 0) ';

        $bind = array(
            ':patientid' => $patientid,
            ':doctorid' => $doctorid,
        );

        return Dao::getEntityListByCond('PatientPicture', $cond, $bind);
    }
}
