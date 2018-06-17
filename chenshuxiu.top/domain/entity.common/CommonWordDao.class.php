<?php
/*
 * CommonWordDao
 */
class CommonWordDao extends Dao
{
    // 名称: getAllGroupstr
    // 备注:
    // 创建:
    // 修改:
    public static function getAllGroupstr () {
        $sql = "select groupstr from commonwords group by groupstr";

        return Dao::queryValues($sql, []);
    }

    // 名称: getAllTypestr
    // 备注:
    // 创建:
    // 修改:
    public static function getAllTypestr () {
        $sql = "select typestr from commonwords group by typestr";

        return Dao::queryValues($sql, []);
    }

    // 名称: getListByOwnertypeOwneridTypestr
    // 备注:
    // 创建:
    // 修改:
    public static function getListByOwnertypeOwneridTypestr ($ownertype, $ownerid, $typestr) {
        $cond = " and ownertype=:ownertype and ownerid=:ownerid and typestr=:typestr order by weight desc";
        $bind = array(
            ":ownertype" => $ownertype,
            ":ownerid" => $ownerid,
            ":typestr" => $typestr);

        return Dao::getEntityListByCond("CommonWord", $cond, $bind);
    }
}