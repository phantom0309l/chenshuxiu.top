<?php

/*
 * DiseaseGroupDao
 */
class DiseaseGroupDao extends Dao
{

    public static function getAll () {
        return Dao::getEntityListByBind('DiseaseGroup');
    }
    
    public static function getByName ($name) {
        $cond = " and name=:name ";
        $bind = [];
        $bind[':name'] = $name;

        return Dao::getEntityByCond('DiseaseGroup', $cond, $bind);
    }

}
