<?php

/*
 * MsgTemplateDao
 */
class MsgTemplateDao extends Dao
{

    // 名称: getByEnameDiseaseidDoctorid
    // 备注: 消息模板, 不同精确度
    // 创建: by xuzhe
    // 修改: 20170419 by sjp
    public static function getByEnameDiseaseidDoctorid($ename, $diseaseid = 0, $doctorid = 0) {
        if (empty($diseaseid)) {
            $diseaseid = 0;
        }

        if (empty($doctorid)) {
            $doctorid = 0;
        }

        $cond = "and ename = :ename and diseaseid = :diseaseid and doctorid = :doctorid  limit 1";
        $bind = [];
        $bind[':ename'] = $ename;
        $bind[':diseaseid'] = $diseaseid;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityByCond('MsgTemplate', $cond, $bind);
    }

    // 名称: getListByDiseaseid
    // 备注:
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getListByDisease(Disease $disease) {
        $cond = " and diseaseid = :diseaseid ";
        $bind = [];
        $bind[':diseaseid'] = $disease->id;

        return Dao::getEntityListByCond('MsgTemplate', $cond, $bind);
    }
}