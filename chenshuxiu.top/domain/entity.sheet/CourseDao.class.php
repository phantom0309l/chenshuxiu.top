<?php

/*
 * CourseDao
 */
class CourseDao extends Dao
{

    // 名称: getAllCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getAllCourse () {
        return Dao::getEntityListByCond("Course");
    }

    // 名称: getListByGroupstr
    // 备注:获得一个分组下所有的course
    // 创建:
    // 修改:
    public static function getListByGroupstr ($groupstr) {
        $cond = " and groupstr = :groupstr order by id asc ";
        $bind = [];
        $bind[':groupstr'] = $groupstr;

        return Dao::getEntityListByCond("Course", $cond, $bind);
    }

    // 名称: getListByTag
    // 备注:可能会有多个course指向同一个tag的情况
    // 创建:
    // 修改:
    public static function getListByTag (Tag $tag) {
        $cond = " and tagid = :tagid ";
        $bind = [];
        $bind[':tagid'] = $tag->id;

        return Dao::getEntityListByCond("Course", $cond, $bind);
    }
}
