<?php
/*
 * CronTabDao
 */
class CronTabDao extends Dao
{

    public static function getByProcess_name ($process_name) {
        $cond = 'and process_name=:process_name';
        $bind = [];
        $bind[':process_name'] = $process_name;
        return Dao::getEntityByCond('CronTab', $cond, $bind);
    }
}
