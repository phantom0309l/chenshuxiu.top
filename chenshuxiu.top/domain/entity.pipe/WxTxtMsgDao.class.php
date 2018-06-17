<?php
/*
 * WxTxtMsgDao
 */
class WxTxtMsgDao extends Dao
{
    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient ($patientid) {
        $cond = " and patientid = :patientid
            order by id ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("WxTxtMsg", $cond, $bind);
    }
}
