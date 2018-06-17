<?php

/*
 * PatientTodayMarkTplDao
 */

class PatientTodayMarkTplDao extends Dao
{
    public static function getListByDiseasegroupid($diseasegroupid) {
        $cond = " AND diseasegroupid = :diseasegroupid ";
        $bind = [
            ':diseasegroupid' => $diseasegroupid
        ];
        
        return Dao::getEntityListByCond('PatientTodayMarkTpl', $cond, $bind);
    }
    
    public static function getOneByDiseasegroupidTitle($diseasegroupid, $title) {
        $cond = " AND diseasegroupid = :diseasegroupid AND title = :title ";
        
        $bind = [
            ':diseasegroupid' => $diseasegroupid,
            ':title' => $title
        ];
        
        return Dao::getEntityByCond('PatientTodayMarkTpl', $cond, $bind);
    }
    
}