<?php
/*
 * DoctorShopProductRefDao
 */
class DoctorShopProductRefDao extends Dao {
    // 名称: getOneByDoctorShopProduct
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByDoctorShopProduct ($doctor, $shopproduct) {
        $cond = " and doctorid = :doctorid and shopproductid = :shopproductid";
        $bind = [];
        $bind[":doctorid"] = $doctor->id;
        $bind[":shopproductid"] = $shopproduct->id;
        return Dao::getEntityByCond("DoctorShopProductRef", $cond, $bind);
    }

    public static function getListByDoctor ($doctor) {
        $cond = " and doctorid = :doctorid";
        $bind = [];
        $bind[":doctorid"] = $doctor->id;
        return Dao::getEntityListByCond("DoctorShopProductRef", $cond, $bind);
    }

}
