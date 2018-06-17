<?php

/*
 * Chip 消息碎片表, 按Clan散列, 消息包括, 纯文字, 图文, 位置, 声音, 视频, 网页等, 见objtype TODO by
 * sjp:20141212: 关于图文的评论, 也可以采用级联部落的方式解决, 暂时搁置
 */
class Chip extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'topclanid',
            'clanid',
            'userid',
            'clientno',
            'objtype',  // Txt, Picture, User,
                       // Webpage,
                       // Location, Voice, Video
            'objid',
            'pictureid',
            'title',
            'content',
            'url',
            'lat',
            'lng',
            'iscomment',  // 可以出现在最新评论
            'upcnt',  // 顶
            'upuserids',  // 顶
            'downcnt',  // 踩
            'downuserids',  // 踩
            'isdeletedbyself',  // 被自己删除
            'deletetime',  // 删除时间
            'isdeletedbyother',  // 被其他人删除
            'deletetimebyother',  // 删除时间
            'deleteuserid',  // 删除人
            'deleteremark',  // 删除备注
            'status',
            'remark');
    }
}
