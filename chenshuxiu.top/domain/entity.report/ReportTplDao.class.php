<?php

/*
 * ReportTplDao
 */

class ReportTplDao extends Dao
{
    public static function getAll() {
        return Dao::getEntityListByBind('ReportTpl');
    }

    public static function getOne() {
        return Dao::getEntityByBind('ReportTpl');
    }

}