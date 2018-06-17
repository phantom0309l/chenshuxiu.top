<?php

/*
 * Clan
 */
class Clan extends LatLngEntity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'randno',  // (需要和topclan的randno保持一致）
            'topclanid',  // 顶级部落id
            'istop',  // 置顶,官方置顶标识 TODO by sjp :需要吗?
            'userid',  // 创建人
            'objtype',  // User, Forum, Work
            'objid',  // 关联对象id
            'lat',  // 纬度
            'lng',  // 经度
            'name',  // 名称
            'pictureid',  // 封面
            'content',  // 说明
            'lastchipid',  // 最后一条碎片id
            'status',
            'remark');
    }
}
