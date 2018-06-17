<?php

/*
 * ReportPictureDao
 */

class ReportPictureDao extends Dao
{
    protected static $entityName = 'ReportPicture';

    public static function getListByReportid($reportid) {
        $cond = ' AND reportid=:reportid';
        $bind = [
            ':reportid' => $reportid
        ];
        return self::getEntityListByCond(self::$entityName, $cond, $bind);
    }
}