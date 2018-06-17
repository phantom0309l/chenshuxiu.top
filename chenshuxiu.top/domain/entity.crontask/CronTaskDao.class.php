<?php
// CronTaskDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160628

class CronTaskDao extends Dao
{
    // 名称: getByCronprocessidWxuserid
    // 备注:
    // 创建:
    // 修改:
    public static function getByCronprocessidWxuserid ($cronprocessid, $wxuserid) {
        $cond = ' and cronprocessid=:cronprocessid and wxuserid=:wxuserid ';
        $bind = [];
        $bind[':cronprocessid'] = $cronprocessid;
        $bind[':wxuserid'] = $wxuserid;

        return Dao::getEntityByCond('CronTask', $cond, $bind);
    }
}