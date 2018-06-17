<?php

/*
 * DoctorDiseaseRefDao
 */
class DoctorDiseaseRefDao extends Dao
{
    // 名称: getByDoctoridDiseaseid
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctoridDiseaseid($doctorid, $diseaseid) {
        $cond = " AND doctorid=:doctorid AND diseaseid=:diseaseid ";
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':diseaseid'] = $diseaseid;

        return Dao::getEntityByCond("DoctorDiseaseRef", $cond, $bind);
    }

    // 名称: getListByDisease
    // 备注: 疾病关联的医生
    public static function getListByDisease(Disease $disease) {
        $cond = " and diseaseid=:diseaseid order by doctorid ";
        $bind = [];
        $bind[':diseaseid'] = $disease->id;

        return Dao::getEntityListByCond("DoctorDiseaseRef", $cond, $bind);
    }

    // 名称: getListByDoctor
    // 备注: 医生关联的疾病
    // 修改: 改名 getListByDoctorid => getListByDoctor
    public static function getListByDoctor(Doctor $doctor) {
        $cond = " and doctorid=:doctorid order by id ";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;

        return Dao::getEntityListByCond("DoctorDiseaseRef", $cond, $bind);
    }

    // 名称: getList_NotHaveDoctorWxShopRef
    // 备注:
    // 创建: 20170724 by txj
    // 修改:
    public static function getList_NotHaveDoctorWxShopRef (DoctorWxShopRef $doctorwxshopref) {
        $doctor = $doctorwxshopref->doctor;
        $wxshop = $doctorwxshopref->wxshop;
        $sql = "select a.*
            from doctordiseaserefs a
            left join doctorwxshoprefs x on x.doctorid=a.doctorid
            where x.id is null and a.doctorid = :doctorid and x.diseaseid > 0 and x.wxshopid = :wxshopid";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':wxshopid'] = $wxshop->id;

        return Dao::loadEntityList('DoctorDiseaseRef', $sql, $bind);
    }
}
