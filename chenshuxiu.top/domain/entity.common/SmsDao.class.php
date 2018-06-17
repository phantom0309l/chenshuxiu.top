<?php
// SmsDao

// owner by sjp
// create by sjp
// review by sjp 20160628

class SmsDao extends Dao
{
    // 名称: getListByMobile
    // 备注:获取列表
    // 创建:
    // 修改:
    public static function getListByMobile ($mobile) {
        $cond = "AND mobile=:mobile";

        $bind = array(
            ':mobile' => $mobile);

        return Dao::getEntityListByCond('Sms', $cond, $bind);
    }
}