<?php

/*
 * Guest_schulterecordDao
 */
class Guest_schulterecordDao extends Dao
{
    // 名称: getCnt
    // 备注:
    // 创建:
    // 修改:
    public static function getCnt () {
        $sql = "select count(*) from guest_schulterecords";
        return Dao::queryValue($sql, []);
    }

    // 名称: getLastByOpenid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastByOpenid ($openid) {
        $cond = "and openid = :openid order by id desc limit 1 ";

        $bind = [];
        $bind[':openid'] = $openid;

        return Dao::getEntityByCond("Guest_schulterecord", $cond, $bind);
    }
}
