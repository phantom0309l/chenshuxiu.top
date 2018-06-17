<?php

/*
 * HospitalDao
 */
class HospitalDao extends Dao
{
    // 名称: getListByWxShop
    // 备注:
    // 创建: 20170419 by sjp
    public static function getListByWxShop (WxShop $wxshop, $condFix = "") {
        $sql = "select distinct a.*
                from hospitals a
                inner join doctors b on b.hospitalid=a.id
                inner join doctorwxshoprefs c on c.doctorid=b.id
                where c.wxshopid=:wxshopid and a.id !=5 {$condFix}";
        $bind = array(
            ':wxshopid' => $wxshop->id);

        return Dao::loadEntityList('Hospital', $sql, $bind);
    }
}
