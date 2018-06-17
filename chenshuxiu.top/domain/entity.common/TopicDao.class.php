<?php

/*
 * TopicDao
 */
class TopicDao extends Dao
{
    // 名称: getListByObjtype
    // 备注:
    // 创建:
    // 修改:
    public static function getListByObjtype ($objtype, $condEx = "") {
        $cond = "AND objtype = :objtype {$condEx}";

        $bind = [];
        $bind[":objtype"] = $objtype;

        return Dao::getEntityListByCond("Topic", $cond, $bind);
    }

    // 名称: getListByObjtypeObjcode
    // 备注:
    // 创建:
    // 修改:
    public static function getListByObjtypeObjcode ($objtype, $objcode, $pagesize = "", $id = "") {
        $sqlpart = "";
        $limit = "";
        if ($pagesize) {
            $pagesize = intval($pagesize);
            $limit = "limit {$pagesize}";
        }
        if ($id) {
            $id = intval($id);
            $sqlpart = " AND id<{$id}";
        }
        $cond = "AND objtype=:objtype AND objcode=:objcode {$sqlpart} order by createtime desc {$limit}";

        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objcode'] = $objcode;

        return Dao::getEntityListByCond("Topic", $cond, $bind);
    }

    // 名称: getListByObjtypeObjid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByObjtypeObjid ($objtype, $objid, $condEx = "") {
        $cond = "AND objtype = :objtype AND objid = :objid {$condEx}";

        $bind = [];
        $bind[":objtype"] = $objtype;
        $bind[":objid"] = $objid;

        return Dao::getEntityListByCond("Topic", $cond, $bind);
    }

    // 名称: getListByWxuserObjtype
    // 备注:
    // 创建:
    // 修改:
    public static function getListByWxuserObjtype ($wxuser, $objtype, $condEx = "") {
        $cond = "AND wxuserid = :wxuserid AND objtype = :objtype {$condEx}";

        $bind = [];
        $bind[":wxuserid"] = $wxuser->id;
        $bind[":objtype"] = $objtype;

        return Dao::getEntityListByCond("Topic", $cond, $bind);
    }

    // 名称: getListByWxuserObjtypeObjcode
    // 备注:
    // 创建:
    // 修改:
    public static function getListByWxuserObjtypeObjcode (WxUser $wxuser, $objtype, $objcode) {
        $cond = "AND wxuserid=:wxuserid AND objtype=:objtype AND objcode=:objcode order by createtime desc";

        $bind = [];
        $bind[":wxuserid"] = $wxuser->id;
        $bind[":objtype"] = $objtype;
        $bind[":objcode"] = $objcode;

        return Dao::getEntityListByCond("Topic", $cond, $bind);
    }

    // 名称: getListByUserObjtype
    // 备注:
    // 创建:
    // 修改:
    public static function getListByUserObjtype (User $user, $objtype) {
        $cond = "AND userid=:userid AND objtype=:objtype order by createtime desc";

        $bind = [];
        $bind[":userid"] = $user->id;
        $bind[":objtype"] = $objtype;

        return Dao::getEntityListByCond("Topic", $cond, $bind);
    }
}
