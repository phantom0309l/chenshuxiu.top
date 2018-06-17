<?php

/*
 * DoctorWxShopRefDao
 */
class DoctorWxShopRefDao extends Dao
{

    // 名称: getByDoctorWxShop
    // 创建: 20170419 by sjp
    public static function getByDoctorWxShop(Doctor $doctor, WxShop $wxshop) {
        $cond = ' and doctorid = :doctorid and wxshopid = :wxshopid ';
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':wxshopid'] = $wxshop->id;

        return Dao::getEntityByCond('DoctorWxShopRef', $cond, $bind);
    }

    // 名称: getByDoctoridWxShopidDiseaseid
    // 创建: 20170722 by txj
    public static function getByDoctoridWxShopidDiseaseid($doctorid, $wxshopid, $diseaseid) {
        $cond = ' and doctorid = :doctorid and wxshopid = :wxshopid and diseaseid = :diseaseid';
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':wxshopid'] = $wxshopid;
        $bind[':diseaseid'] = $diseaseid;

        return Dao::getEntityByCond('DoctorWxShopRef', $cond, $bind);
    }

    // 名称: getOneByDoctorDisease
    // 创建: 20170419 by sjp : 等旧接口改掉,这个函数就不需要了
    public static function getOneByDoctorDisease(Doctor $doctor, Disease $disease) {
        $sql = "select a.*
            from doctorwxshoprefs a
            inner join wxshops b on b.id=a.wxshopid
            where a.doctorid = :doctorid and b.diseaseid=:diseaseid and a.diseaseid = 0";

        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':diseaseid'] = $disease->id;

        $ref = Dao::loadEntity('DoctorWxShopRef', $sql, $bind);

        if ($ref instanceof DoctorWxShopRef) {
            return $ref;
        }

        $cond = ' and doctorid = :doctorid ';
        $bind = [];
        $bind[':doctorid'] = $doctor->id;

        return Dao::getEntityByCond('DoctorWxShopRef', $cond, $bind);
    }

    // 名称: getListByDoctor
    // 备注: 基于某个医生所有的（默认+专属疾病）的doctorWxShopRefs
    // 创建: 20170419 by sjp
    public static function getListByDoctor(Doctor $doctor): array {
        $cond = ' and doctorid = :doctorid order by id ';
        $bind = [];
        $bind[':doctorid'] = $doctor->id;

        return Dao::getEntityListByCond('DoctorWxShopRef', $cond, $bind);
    }

    // 名称: getListByDoctor
    // 备注: 基于某个医生默认的doctorWxShopRefs
    // 创建: 20170724 by txj
    public static function getDefaultListByDoctor(Doctor $doctor): array {
        $cond = ' and doctorid = :doctorid and diseaseid = 0 order by id ';
        $bind = [];
        $bind[':doctorid'] = $doctor->id;

        return Dao::getEntityListByCond('DoctorWxShopRef', $cond, $bind);
    }

    // 名称: getListByDoctorWxShop
    // 创建: 20170724 by txj
    public static function getListByDoctorWxShop(Doctor $doctor, WxShop $wxshop) {
        $cond = ' and doctorid = :doctorid and wxshopid = :wxshopid ';
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':wxshopid'] = $wxshop->id;

        return Dao::getEntityListByCond('DoctorWxShopRef', $cond, $bind);
    }
}
