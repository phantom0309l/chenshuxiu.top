<?php
/*
 * PatientTagTplDao
 */
class PatientTagTplDao extends Dao
{
    public static function getListByDoctor (Doctor $doctor) {
        // #4130, 协和风湿免疫科, 王迁 也能看 (医生自己和监管的医生)
        $doctorids_str = $doctor->getDoctorIdsStr();

        $cond = " and doctorid in ({$doctorids_str}) order by pos asc ";
        $bind = [];

        return Dao::getEntityListByCond("PatientTagTpl", $cond, $bind);
    }

    public static function getListByDoctor4Ipad (Doctor $doctor) {
        $cond = " and doctorid = :doctorid order by pos asc, createtime asc";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;

        return Dao::getEntityListByCond("PatientTagTpl", $cond, $bind);
    }

    public static function getByNameDoctorid ($name, $doctorid) {
        $cond = " and name = :name and doctorid = :doctorid ";
        $bind = [];
        $bind[':name'] = $name;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityByCond("PatientTagTpl", $cond, $bind);
    }
}