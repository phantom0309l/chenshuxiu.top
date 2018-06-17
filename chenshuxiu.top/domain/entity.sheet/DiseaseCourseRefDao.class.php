<?php

/*
 * DiseaseCourseRefDao
 */
class DiseaseCourseRefDao extends Dao
{
    // 名称: getListByCourseid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByCourseid ($courseid) {
        $cond = " and courseid = :courseid ";
        $bind = array(
            ':courseid' => $courseid);

        return Dao::getEntityListByCond('DiseaseCourseRef', $cond, $bind);
    }

    // 名称: getListByDiseaseid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDiseaseid ($diseaseid) {
        $cond = " and diseaseid = :diseaseid ";
        $bind = array(
            ':diseaseid' => $diseaseid);

        return Dao::getEntityListByCond('DiseaseCourseRef', $cond, $bind);
    }

    // 名称: getListByDiseaseidDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDiseaseidDoctorid ($diseaseid, $doctorid) {
        $cond = " and diseaseid = :diseaseid and doctorid = :doctorid ";
        $bind = array(
            ':diseaseid' => $diseaseid,
            ':doctorid' => $doctorid
        );

        return Dao::getEntityListByCond('DiseaseCourseRef', $cond, $bind);
    }

    // 名称: getListByDiseaseids
    public static function getListByDiseaseids (array $diseaseids) {
        $str = implode(',', $diseaseids);
        $cond = " and diseaseid in ( {$str} ) order by diseaseid ";

        return Dao::getEntityListByCond('DiseaseCourseRef', $cond);
    }

    public static function getByDiseaseidDoctoridCourseid ($diseaseid, $doctorid, $courseid) {
        $cond = " and diseaseid = :diseaseid and doctorid = :doctorid and courseid = :courseid ";
        $bind = array(
            ':diseaseid' => $diseaseid,
            ':doctorid' => $doctorid,
            ':courseid' => $courseid
        );

        return Dao::getEntityByCond('DiseaseCourseRef', $cond, $bind);
    }
}
