<?php
/*
 * WxOpMsgDao
 */
class WxOpMsgDao extends Dao
{
    // 名称: getLastWxOpMsg
    // 备注: 获取当前医生当前患者下最后一条未读信息
    // 创建:
    // 修改:
    public static function getLastWxOpMsg ($patientid) {
        $sql = "select b.*
            from patients a
            inner join wxopmsgs b on a.id = b.patientid
            where a.id = :patientid and b.isnew = 1 and b.status = 1 and b.auditorid > 0
            order by b.createtime desc
            limit 1";

        $bind = [];
        $bind[':patientid'] = $patientid;

        $lastwxopmsg = Dao::loadEntityList("WxOpMsg", $sql, $bind);

        if ($lastwxopmsg) {
            foreach ($lastwxopmsg as $item) {
                $str = "运营汇报：" . mb_substr($item->content, 0, 10) . "...";
                return $str;
            }
        }
        return "";
    }

    // 名称: getListSendByAuditor
    // 备注: 根据患者id获取医生未读取的wxopmsg
    // 创建:
    // 修改:
    public static function getListSendByAuditor ($patientid) {
        $cond = " and patientid = :patientid and auditorid > 0 and isnew = 1 and status =1
            order by id desc
            limit 100";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("WxOpMsg", $cond, $bind);
    }

    // 名称: getNewMsgCnt
    // 备注: 获取当前医生当前患者下未读信息数量
    // 创建:
    // 修改:
    public static function getNewMsgCnt ($patientid) {
        $sql = " select count(*) as cnt
            from  wxopmsgs
            where isnew = 1 and status = 1 and auditorid > 0 and patientid = :patientid  ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        $cnt = 0 + Dao::queryValue($sql, $bind);

        if ($cnt > 99) {
            $cnt = 99;
        }

        return $cnt;
    }

    // 名称: getTotalNewMsgByDoctorid
    // 备注:根据医生id获取当前未读运营消息总数
    // 创建:
    // 修改:
    public static function getTotalNewMsgByDoctorid ($doctorid) {
        $bind = [];

        $sql = " select count(*) as cnt
            from wxopmsgs
            where doctorid = :doctorid and auditorid > 0 and isnew = 1 and status =1
                and patientid in ( select patientid from pcards where doctorid = :doctorid )
            order by id desc
            limit 100";

        $bind[':doctorid'] = $doctorid;

        return Dao::queryValue($sql, $bind);
    }

}
