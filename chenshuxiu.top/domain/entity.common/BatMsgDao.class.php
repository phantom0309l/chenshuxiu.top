<?php
// BatMsgDao
// 建议废弃 by sjp 20160627

// owner by xxx
// review by sjp 20160627
// TODO rework

class BatMsgDao extends Dao
{
    // 名称: getList
    // 备注:获取所有群发消息列表
    // 创建:
    // 修改:
    public static function getAllList () {
        $cond = "order by auditstatus asc , status desc, issend asc";
        $batmsgs = Dao::getEntityListByCond("BatMsg", $cond, []);
        return $batmsgs;
    }

    // 名称: getNeedSendList
    // 备注:获取需要发送的群发列表
    // 创建:
    // 修改:
    public static function getNeedSendList () {
        $cond = " AND auditstatus=1 AND status=1 AND issend=0 ";
        $batmsgs = Dao::getEntityListByCond("BatMsg", $cond, []);
        return $batmsgs;
    }

    // 名称: getNotAuditTip
    // 备注:获取未审核的门诊消息
    // 创建:
    // 修改:
    public static function getNotAuditTip ($userid) {
        $cond = " AND typestr='DoctorSetTip' AND auditstatus=0 AND status=0 AND issend=0 AND userid=:userid";
        $bind = [];
        $bind[':userid'] = $userid;
        $a = Dao::getEntityByCond("BatMsg", $cond, $bind);
        return $a;
    }
}