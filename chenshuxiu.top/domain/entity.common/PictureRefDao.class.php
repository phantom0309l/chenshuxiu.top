<?php
// PictureRefDao

// owner by sjp
// create by sjp
// review by sjp 20160628

class PictureRefDao extends Dao
{
    // 名称: getBy2Id
    // 备注:获取 by obj+pictureid
    // 创建:
    // 修改:
    public static function getBy2Id ($objtype, $objid, $pictureid) {
        $cond = " and objtype=:objtype and objid=:objid and pictureid=:pictureid ";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;
        $bind[':pictureid'] = $pictureid;

        return Dao::getEntityByCond('PictureRef', $cond, $bind);
    }

    // 名称: getByObjPictureid
    // 备注:获取 by obj+pictureid
    // 创建:
    // 修改:
    public static function getByObjPictureid (Entity $obj, $pictureid) {
        return self::getBy2Id(get_class($obj), $obj->id, $pictureid);
    }

    // 名称: getListByObj
    // 备注:获取关系列表 of Obj
    // 创建:
    // 修改:
    public static function getListByObj (Entity $obj, $cnt = 100) {
        return self::getListByObjTypeObjId(get_class($obj), $obj->id, $cnt);
    }

    // 名称: getListByObjTypeObjId
    // 备注:获取关系列表 of Obj
    // 创建:
    // 修改:
    public static function getListByObjTypeObjId ($objtype, $objid, $cnt = 100) {
        $cnt = intval($cnt);

        $cond = " and objtype=:objtype and objid=:objid limit $cnt ";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityListByCond('PictureRef', $cond, $bind);
    }

    // 名称: refs2picture_thumburls
    // 备注:objs 转换 picture_thumburls
    // 创建:
    // 修改:
    public static function refs2picture_thumburls (Array $pictureRefs, $width = 0, $height = 0, $iscut = false) {
        $arr = array();
        foreach ($pictureRefs as $a) {
            if ($a instanceof PictureRef && $a->picture instanceof Picture) {
                $arr[] = $a->picture->getSrc($width, $height, $iscut);
            }
        }

        return $arr;
    }

    // 名称: refs2picture_urls
    // 备注:objs 转换 picture_urls
    // 创建:
    // 修改:
    public static function refs2picture_urls (Array $pictureRefs) {
        $arr = array();
        foreach ($pictureRefs as $a) {
            if ($a instanceof PictureRef && $a->picture instanceof Picture) {
                $arr[] = $a->picture->getSrc();
            }
        }

        return $arr;
    }
}