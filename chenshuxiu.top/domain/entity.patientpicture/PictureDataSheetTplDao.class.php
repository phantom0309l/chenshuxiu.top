<?php
/*
 * PictureDataSheetTplDao
 */
class PictureDataSheetTplDao extends Dao
{
    public static function getListByDiseaseid ($diseaseid) {
        $cond = ' and diseaseid=:diseaseid ';

        $bind = array(
            ':diseaseid' => $diseaseid
        );

        return Dao::getEntityListByCond('PictureDataSheetTpl', $cond, $bind);
    }
}
