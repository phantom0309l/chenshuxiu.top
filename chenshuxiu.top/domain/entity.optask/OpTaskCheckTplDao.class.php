<?php

/*
 * OpTaskCheckTplDao
 */

class OpTaskCheckTplDao extends Dao
{

    // getByEname
    Public static function getByEname($ename) {
        $cond = ' AND ename=:ename ';
        $bind = [];
        $bind[':ename'] = $ename;

        return Dao::getEntityByCond('OpTaskCheckTpl', $cond, $bind);
    }

}