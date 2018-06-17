<?php
/*
 * PictureDataSheetDao
 */
class PictureDataSheetDao extends Dao
{

    public static function getListByPatientpictureid ($patientpictureid) {
        $cond = ' and patientpictureid=:patientpictureid ';

        $bind = array(
            ':patientpictureid' => $patientpictureid);

        return Dao::getEntityListByCond('PictureDataSheet', $cond, $bind);
    }

}
