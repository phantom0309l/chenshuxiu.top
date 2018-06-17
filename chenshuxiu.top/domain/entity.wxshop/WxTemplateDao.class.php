<?php
/*
 * WxTemplateDao
 */
class WxTemplateDao extends Dao
{
    // 名称: getByCode
    // 备注:根据微信模板id获取
    // 创建: by txj
    // 修改: by txj
    public static function getByCode ($code) {
        $cond = " AND code = :code  ";
        $bind = [];
        $bind[':code'] = $code;

        return Dao::getEntityByCond("WxTemplate", $cond, $bind);
    }

    // 名称: getByEname
    // 备注: 根据微信模板ename
    // 创建: by txj
    // 修改: by txj
    public static function getByEname ($wxshopid, $ename) {
        $cond = " AND wxshopid = :wxshopid AND ename = :ename ";
        $bind = [];
        $bind[':wxshopid'] = $wxshopid;
        $bind[':ename'] = $ename;

        return Dao::getEntityByCond("WxTemplate", $cond, $bind);
    }

    // 名称: getListByWxShopId
    // 备注: 根据wxshopid
    // 创建: by txj
    // 修改:
    public static function getListByWxShopId ($wxshopid) {
        $cond = " AND wxshopid = :wxshopid";
        $bind = [];
        $bind[":wxshopid"] = $wxshopid;
        return Dao::getEntityListByCond("WxTemplate", $cond, $bind);
    }
}
