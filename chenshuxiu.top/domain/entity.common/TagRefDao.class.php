<?php
// TagRefDao

// owner by xxx
// create by sjp
// review by sjp 20160628

class TagRefDao extends Dao
{
    // 名称: getByObjtypeObjidTagid
    // 备注:
    // 创建:
    // 修改:
    public static function getByObjtypeObjidTagid ($objtype, $objid, $tagid) {
        $cond = " AND objtype=:objtype AND objid=:objid AND tagid=:tagid";
        $bind = [];

        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;
        $bind[':tagid'] = $tagid;

        return Dao::getEntityByCond('TagRef', $cond, $bind);
    }

    // 名称: getListByObj
    // 备注:
    // 创建:
    // 修改:
    public static function getListByObj (Entity $obj, $typestr = '') {
        if ($typestr) {
            return self::getListByObjTypestr($obj, $typestr);
        }

        $bind = [];
        $cond = " AND objtype = :objtype AND objid=:objid order by id ";
        $bind[':objtype'] = get_class($obj);
        $bind[':objid'] = $obj->id;

        return Dao::getEntityListByCond("TagRef", $cond, $bind);
    }

    // 名称: getListByObjtypeTagid
    // 备注: 现在用于了前台页面用药分类展示
    // 创建:txj
    // 修改:
    public static function getListByObjtypeTagid ($objtype, $tagid) {
        $cond = " AND objtype=:objtype AND tagid=:tagid";
        $bind = [];

        $bind[':objtype'] = $objtype;
        $bind[':tagid'] = $tagid;

        return Dao::getEntityListByCond('TagRef', $cond, $bind);
    }

    // 名称: getListByObjTypestr
    // 备注:依据typrstr连表找出患者标签的关系
    // 创建:
    // 修改:
    private static function getListByObjTypestr (Entity $obj, $typestr) {
        $bind = [];
        $sql = " select a.* from tagrefs a
                 inner join tags b on b.id = a.tagid
                 where b.typestr = :typestr AND a.objtype = :objtype AND a.objid=:objid
                 order by a.id ";

        $bind[':typestr'] = $typestr;
        $bind[':objtype'] = get_class($obj);
        $bind[':objid'] = $obj->id;

        return Dao::loadEntityList("TagRef", $sql, $bind);
    }

    // 名称: getTagNamesStr
    // 备注:患者与症断结果关系数组(排除其他合并症项)
    // 创建:
    // 修改:
    public static function getTagNamesStr (Entity $obj, $typestr = 'Disease') {
        $tagrefs = self::getListByObj($obj, $typestr);
        if (empty($tagrefs)) {
            return ' - ';
        }

        $arr = array();
        foreach ($tagrefs as $a) {
            // 排除 其他合并症 & 其他
            if ($a->tag->id != 10 && $a->tag->id != 60) {
                $arr[] = $a->tag->name;
            }
        }

        return implode(', ', $arr);
    }
}
