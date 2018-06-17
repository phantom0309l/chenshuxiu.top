<?php
// FitPageItemDao

// owner by fhw
// create by fhw
// review by sjp 20160628

class FitPageItemDao extends Dao
{
    // 名称: getBy2Id
    // 备注:
    // 创建:
    // 修改:
    public static function getBy2Id ($fitpageid, $fitpagetplitemid) {
        $cond = " and fitpageid = :fitpageid and fitpagetplitemid = :fitpagetplitemid order by pos";
        $bind = array();

        $bind[':fitpageid'] = $fitpageid;
        $bind[':fitpagetplitemid'] = $fitpagetplitemid;

        return Dao::getEntityByCond('FitPageItem', $cond, $bind);
    }

    // 名称: getBy2Id
    // 备注:
    // 创建:
    // 修改:
    public static function getByFitpageidCode ($fitpageid, $code) {
        $cond = " and fitpageid = :fitpageid and code = :code";
        $bind = [];

        $bind[':fitpageid'] = $fitpageid;
        $bind[':code'] = $code;

        return Dao::getEntityByCond('FitPageItem', $cond, $bind);
    }

    // 名称: getBy2Id
    // 备注:
    // 创建:
    // 修改:
    public static function getByFitPageidAndCode ($fitpageid, $code) {
        $sql = "SELECT *
        FROM fitpageitems a
        LEFT JOIN fitpagetplitems b on a.fitpagetplitemid = b.id
        WHERE fitpageid = :fitpageid
        AND b.code = :code";
        $bind = [];
        $bind[':fitpageid'] = $fitpageid;
        $bind[':code'] = $code;

        return Dao::loadEntity('FitPageItem', $sql, $bind);
    }

    // 名称: getListByFitPage
    // 备注:
    // 创建:
    // 修改:
    public static function getListByFitPage (FitPage $fitpage) {
        $cond = " and fitpageid = :fitpageid order by pos ";
        $bind = [];

        $bind[':fitpageid'] = $fitpage->id;

        return Dao::getEntityListByCond('FitPageItem', $cond, $bind);
    }

    // 名称: getIsMustListByFitPage
    // 备注:
    // 创建:
    // 修改:
    public static function getIsMustListByFitPage (FitPage $fitpage) {
        $cond = " and ismust = 1 and fitpageid = :fitpageid order by pos ";
        $bind = array();

        $bind[':fitpageid'] = $fitpage->id;

        return Dao::getEntityListByCond('FitPageItem', $cond, $bind);
    }

    // 名称: getListByFitPageTplItem
    // 创建:
    // 修改:
    public static function getListByFitPageTplItem (FitPageTplItem $fitpagetplitem) {
        $cond = " and fitpagetplitemid = :fitpagetplitemid";
        $bind = [];

        $bind[':fitpagetplitemid'] = $fitpagetplitem->id;

        return Dao::getEntityListByCond('FitPageItem', $cond, $bind);
    }
}