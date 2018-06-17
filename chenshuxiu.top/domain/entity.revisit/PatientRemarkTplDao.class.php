<?php
/*
 * PatientRemarkTplDao
 */
class PatientRemarkTplDao extends Dao
{
    // 名称: getAllName
    // 备注:
    // 创建:
    // 修改:
    public static function getAllName () {
        $sql = "select name from patientremarktpls group by name";

        return Dao::queryValues($sql, []);
    }

    // 名称: getByDoctoridName
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctoridName ($doctorid, $name, $diseaseid = 0, $typestr = '') {
        $cond = ' and doctorid = :doctorid and name = :name ';
        $bind = array(
            ':doctorid' => $doctorid,
            ':name' => $name);

        if ($diseaseid > 0) {
            $cond .= ' and diseaseid = :diseaseid ';
            $bind[':diseaseid'] = $diseaseid;
        }

        if ($typestr) {
            $cond .= ' and typestr = :typestr ';
            $bind[':typestr'] = $typestr;
        }

        $cond .= ' order by pos asc limit 1';
        return Dao::getEntityByCond('PatientRemarkTpl', $cond, $bind);
    }

    // 名称: getListByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctorid ($doctorid, $typestr = '') {
        $cond = ' and doctorid = :doctorid ';
        $bind = array(
            ':doctorid' => $doctorid);

        if ($typestr) {
            $cond .= ' and typestr = :typestr ';
            $bind[':typestr'] = $typestr;
        }

        $cond .= ' order by pos asc';

        return Dao::getEntityListByCond('PatientRemarkTpl', $cond, $bind);
    }

}
