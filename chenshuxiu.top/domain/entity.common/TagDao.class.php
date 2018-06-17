<?php
// TagDao

// owner by xxx
// create by sjp
// review by sjp 20160628

class TagDao extends Dao
{
    // 名称: getByName
    // 备注:
    // 创建:
    // 修改:
    public static function getByName ($name) {
        $cond = " AND name = :name order by id ";

        $bind = [];
        $bind[':name'] = $name;

        return Dao::getEntityByCond("Tag", $cond, $bind);
    }

    // 名称: getListByTypestr
    // 备注:
    // 创建:
    // 修改:
    public static function getListByTypestr ($typestr = "") {
        $cond = " ";
        $bind = [];

        if ($typestr) {
            $cond .= " and typestr = :typestr order by id";
            $bind[':typestr'] = $typestr;
        }

        return Dao::getEntityListByCond("Tag", $cond, $bind);
    }

    public static function getListByFuzzyName($name) {
        $cond = " AND name LIKE :name ";
        $bind = [
            ":name" => "%{$name}%"
        ];
        return Dao::getEntityListByCond("Tag", $cond, $bind);
    }

    public static function getByTypestrAndName ($typestr, $name) {
        $cond = " AND typestr=:typestr AND name = :name order by id ";

        $bind = [];
        $bind[':typestr'] = $typestr;
        $bind[':name'] = $name;

        return Dao::getEntityByCond("Tag", $cond, $bind);
    }
}