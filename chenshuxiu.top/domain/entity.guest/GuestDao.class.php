<?php

/*
 * GuestDao
 */
class GuestDao extends Dao
{
    // 名称: getByOpenid
    // 备注:
    // 创建:
    // 修改:
    public static function getByOpenid ($openid) {
        $cond = "and openid = :openid order by id desc ";

        $bind = [];
        $bind[':openid'] = $openid;

        return Dao::getEntityByCond("Guest", $cond, $bind);
    }

    // 名称: getOrCreateByOpenid
    // 备注:
    // 创建:
    // 修改:
    public static function getOrCreateByOpenid ($openid) {
        if (empty($openid)) {
            return null;
        }

        // 避免重复创建
        $guest = self::getByOpenid($openid);
        if ($guest instanceof Guest) {
            return $guest;
        }

        $wxuser = WxUserDao::getByOpenid($openid);
        if ($wxuser instanceof WxUser) {
            $row = array();
            $row["openid"] = $wxuser->openid;
            $row["unionid"] = $wxuser->unionid;
            $row["nickname"] = $wxuser->nickname;
            $row["sex"] = $wxuser->sex;
            $row["language"] = $wxuser->language;
            $row["headimgurl"] = $wxuser->headimgurl;
            $row["city"] = $wxuser->city;
            $row["province"] = $wxuser->province;
            $row["country"] = $wxuser->country;
            $guest = Guest::createByBiz($row);
            return $guest;
        }

        return null;
    }
}
