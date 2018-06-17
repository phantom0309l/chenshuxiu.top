<?php

/*
 * LikeDao
 */
class LikeDao extends Dao
{
    // 名称: getCaiCnt
    // 备注:
    // 创建:
    // 修改:
    public static function getCaiCnt ($objtype, $objid) {
        $sql = "select count(*) as cnt
            from likes
            where status=0 and objtype=:objtype and objid=:objid ";

        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getDingCnt
    // 备注:
    // 创建:
    // 修改:
    public static function getDingCnt ($objtype, $objid) {
        $sql = "select count(*) as cnt
            from likes
            where status=1 and objtype=:objtype and objid=:objid ";

        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getDingList
    // 备注:
    // 创建:
    // 修改:
    public static function getDingList ($objtype, $objid) {
        $cond = "AND objtype = :objtype AND objid = :objid AND status=1 order by createtime ";

        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityListByCond("Like", $cond, $bind);
    }

    // 名称: getListByWxuserDing
    // 备注:
    // 创建:
    // 修改:
    public static function getListByWxuserDing (WxUser $wxuser, $objtype, $objid) {
        $cond = "AND wxuserid=:wxuserid AND objtype=:objtype AND objid=:objid AND status=1 order by createtime ";

        $bind = [];
        $bind[':wxuserid'] = $wxuser->id;
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityListByCond("Like", $cond, $bind);
    }

    // 名称: getOneByWxUser
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByWxUser (WxUser $wxuser, $objtype, $objid) {
        $cond = "AND wxuserid=:wxuserid AND objtype=:objtype AND objid=:objid AND status=1 order by createtime ";

        $bind = [];
        $bind[':wxuserid'] = $wxuser->id;
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityByCond("Like", $cond, $bind);
    }
}
