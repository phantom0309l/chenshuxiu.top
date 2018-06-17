<?php
/*
 * ScheduleTplDao
 */
class ScheduleTplDao extends Dao
{
    // 名称: getListByDoctor
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctor (Doctor $doctor) {

        $bind = [];
        $cond = " AND doctorid = :doctorid order by wday asc ";
        $bind[':doctorid'] = $doctor->id;

        $arr = Dao::getEntityListByCond('ScheduleTpl', $cond, $bind);

        $arr1 = array();
        $arr2 = array();
        foreach ($arr as $a) {
            if ($a->opdateIsPass()) {
                $arr2[] = $a;
            } else {
                $arr1[] = $a;
            }
        }

        return array_merge($arr1, $arr2);
    }
}
