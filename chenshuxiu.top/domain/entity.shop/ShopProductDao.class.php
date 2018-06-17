<?php

/*
 * ShopProductDao
 */
class ShopProductDao extends Dao
{

    public static function getShopProductByObjtypeObjid ($objtype, $objid) {
        $cond = " and objtype=:objtype and objid=:objid ";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityByCond('ShopProduct', $cond, $bind);
    }

    public static function getShopProductBySku_code ($sku_code) {
        $cond = " and sku_code=:sku_code ";
        $bind = [];
        $bind[':sku_code'] = $sku_code;

        return Dao::getEntityByCond('ShopProduct', $cond, $bind);
    }

    public static function getMedicineProductListByDoctor (Doctor $doctor) {
        $sql = "select a.*
                    from shopproducts a
                    inner join doctorshopproductrefs b on b.shopproductid = a.id
                    where b.doctorid = :doctorid and a.objtype = 'MedicineProduct' and a.objid > 0 and a.status = 1
                    order by a.pos asc, a.title_pinyin asc";

        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        return Dao::loadEntityList('ShopProduct', $sql, $bind);
    }

    public static function getMedicineProductListByDoctorIgnoreArrStr (Doctor $doctor, $ignore_arr_str) {
        $sql = "select a.*
                    from shopproducts a
                    inner join doctorshopproductrefs b on b.shopproductid = a.id
                    where b.doctorid = :doctorid and a.objtype = 'MedicineProduct' and a.objid > 0 and a.status = 1
                    and a.id not in ($ignore_arr_str)
                    order by a.pos asc, a.title_pinyin asc";

        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        return Dao::loadEntityList('ShopProduct', $sql, $bind);
    }
}
