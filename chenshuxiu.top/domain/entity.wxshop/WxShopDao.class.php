<?php

/*
 * WxShopDao
 */
class WxShopDao extends Dao
{

    // 名称: getByAccess_token
    // 备注: WxApi 在消息发送失败时,重新获取wxshop,然后重新生成access_token
    // 创建: by sjp
    // 修改: by sjp
    public static function getByAccess_token ($access_token = '') {
        $cond = " AND access_token = :access_token ";
        $bind = [];
        $bind[':access_token'] = $access_token;
        return Dao::getEntityByCond("WxShop", $cond, $bind);
    }

    // 名称: 获取疾病绑定的微信服务号
    // 备注:
    // 创建: by sjp
    // 修改: by sjp
    public static function getByDiseaseid ($diseaseid) {
        $cond = " AND diseaseid = :diseaseid ";

        // 特殊情况处理
        if ($diseaseid == 1) {
            $cond .= " AND id = 1 "; // 方寸儿童管理服务平台
        } elseif ($diseaseid == 5) {
            $cond .= " AND id = 8 "; // 方寸测试症
        }

        $bind = [];
        $bind[':diseaseid'] = $diseaseid;
        return Dao::getEntityByCond("WxShop", $cond, $bind);
    }

    // 名称: 根据gh获取微信服务号
    // 备注: 这个是主要的接口, 用户发消息过来ToUserName=gh, 模板消息里的链接也都需要加上gh=
    // 创建: by sjp
    // 修改: by sjp
    public static function getByGh ($gh = 'gh_f797daaac3f3') {
        $cond = " AND gh = :gh ";
        $bind = [];
        $bind[':gh'] = $gh;

        return Dao::getEntityByCond("WxShop", $cond, $bind);
    }

    // 名称: getAllList
    // 备注:
    // 创建: by txj
    // 修改: by sjp : getList => getAllList
    public static function getAllList () {
        return Dao::getEntityListByCond("WxShop");
    }

    // 名称: 获取尚未绑定的服务号
    // 备注: wxshop->diseaseid 作为一个关联条件, 这里是特殊情况!
    // 创建: 20170419 by sjp
    public static function getList_NotBindDoctorByDoctor (Doctor $doctor) {
        $sql = "select a.*
            from wxshops a
            inner join doctordiseaserefs b on b.diseaseid=a.diseaseid
            left join doctorwxshoprefs x on ( x.doctorid=b.doctorid and x.wxshopid=a.id and x.diseaseid = 0 )
            where a.id not in (2,3,4,12,14,15,17,21) and x.id is null and b.doctorid = :doctorid";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;

        return Dao::loadEntityList('WxShop', $sql, $bind);
    }
}
