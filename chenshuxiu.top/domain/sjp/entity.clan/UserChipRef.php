<?php
// /////////////////////////////
// UserChipRef
// 关联表,方便用户查询自己发过的消息
// 按user散列,为避免状态错误,相关状态存储在Chip
// /////////////////////////////
class UserChipRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'userid',
            'chipid',
            'clanid',
            'topclanid',
            'objtype',
            'objid',
            'isopen'); // 朋友圈公开,公开范围暂时不设置
    }
}
