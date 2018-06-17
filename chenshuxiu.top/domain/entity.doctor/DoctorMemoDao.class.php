<?php

/*
 * DoctorMemoDao
 */
class DoctorMemoDao extends Dao
{
    // 名称: getListByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctorid ($doctorid) {
        $cond = "and doctorid=:doctorid and thedate>=:thedate order by thedate asc";

        $today = date("Y-m-d");

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':thedate'] = $today;

        return Dao::getEntityListByCond("DoctorMemo", $cond, $bind);
    }
}
