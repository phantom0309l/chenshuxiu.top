<?php
/*
 * Doctor_SuperiorDao
 */
class Doctor_SuperiorDao extends Dao {

    //取所有上级
    public static function getListByDoctorid($doctorid) {
        $cond = ' AND doctorid=:doctorid';
        $bind = [
            ':doctorid' => $doctorid,
        ];

        return Dao::getEntityListByCond('Doctor_Superior', $cond, $bind);
    }

    //取所有下属
    public static function getListBySuperiorDoctorid($superior_doctorid) {
        $cond = ' AND superior_doctorid=:superior_doctorid';
        $bind = [
            ':superior_doctorid' => $superior_doctorid,
        ];

        return Dao::getEntityListByCond('Doctor_Superior', $cond, $bind);
    }

    //用于精确查找两个医生是否是从属关系
    public static function getOneBy2Doctorid($doctorid, $superior_doctorid) {
        $cond = ' AND doctorid=:doctorid AND superior_doctorid=:superior_doctorid';
        $bind = [
            ':doctorid' => $doctorid,
            ':superior_doctorid' => $superior_doctorid,
        ];
        return Dao::getEntityByCond('Doctor_Superior', $cond, $bind);
    }
}
