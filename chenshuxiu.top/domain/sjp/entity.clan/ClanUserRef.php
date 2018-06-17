<?php

/*
 * ClanUserRef
 */
class ClanUserRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'clanid',  // 部落id
            'userid',  // 参与者
            'topclanid',  // 部落id
            'isowner',  // 群主
            'istop',  // 置顶
            'ishide',  // 隐身
            'isshield',  // 屏蔽通知,消息免打扰
            'isdeleted',  // 用户客户端删除,当有人发消息时,继续打开
            'iskicked',  // 被群组踢出
            'kickeduserids',  // 踩之的用户们，重新申请加入的字段暂不考虑
            'lastclearmaxchipid',  // 上一次客户端清空聊天记录时,最大的碎片id
            'lastshieldmaxchipid',  // 上一次客户端屏蔽消息时,最大的碎片id
            'lastwritechipid',  // 上一次发言,碎片id
            'lastwritetime',  // 上一次发言时间
            'lastreadchipid',  // 上一次阅读,碎片id
            'lastreadtime',  // 上一次阅读时间
            'lastpushchipid',  // 最后推送,碎片id
            'lastpushtime',  // 最后推送时间
            'lat',  // 纬度,推送时或加入时位置
            'lng'); // 经度,推送时或加入时位置
    }
}
