<?php
// RevisitTktDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class RevisitTktDao extends Dao
{

    // 名称: getCntByScheduleidDoctorid
    // 备注: 获取已约出去数目
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getCntByScheduleidDoctorid ($scheduleid, $doctorid,$yuyue_platform='fangcun') {
        $sql = "select count(*)
            from revisittkts
            where scheduleid = :scheduleid and doctorid = :doctorid
                and yuyue_platform = :yuyue_platform
                and isclosed = 0 and patientid > 0
                and status = 1 and auditstatus in (0,1) ";

        $bind = array(
            ":scheduleid" => $scheduleid,
            ":doctorid" => $doctorid,
            ":yuyue_platform" => $yuyue_platform);

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfSchedule
    // 备注: 预约数目
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getCntOfSchedule (Schedule $schedule, $status = 'all', $isclosed = 'all',$yuyue_platform='fangcun') {
        $cond = " AND scheduleid = :scheduleid AND patientid > 0 ";

        $bind = [];
        $bind[':scheduleid'] = $schedule->id;

        if ($status != 'all') {
            $cond .= ' AND status = :status ';
            $bind[':status'] = $status;
        }

        if ($isclosed != 'all') {
            $cond .= ' AND isclosed = :isclosed ';
            $bind[':isclosed'] = $isclosed;
        }

        $cond .= ' AND yuyue_platform = :yuyue_platform ';
        $bind[':yuyue_platform'] = $yuyue_platform;

        $sql = " select count(*) as cnt
            from revisittkts
            where 1=1 $cond ";

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfScheduleTpl
    // 备注: 某出诊模板的全部加号单数目
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getCntOfScheduleTpl ($scheduletplid,$yuyue_platform='fangcun') {
        $sql = " select count(*) as cnt
            from revisittkts a
            inner join schedules b on a.scheduleid = b.id
            where b.scheduletplid = :scheduletplid
            and a.yuyue_platform = :yuyue_platform
            and a.patientid > 0 ";

        $bind = [];
        $bind[':scheduletplid'] = $scheduletplid;
        $bind[':yuyue_platform'] = $yuyue_platform;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfScheduleTplGtToday
    // 备注: 某出诊模板大于今天的全部加号单数目
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getCntOfScheduleTplGtToday ($scheduletplid,$yuyue_platform='fangcun') {
        $sql = " select count(*) as cnt
            from revisittkts a
            inner join schedules b on a.scheduleid = b.id
            where b.scheduletplid = :scheduletplid
            and a.yuyue_platform = :yuyue_platform
            and a.patientid > 0 and a.thedate >= :today ";

        $bind = [];
        $bind[':scheduletplid'] = $scheduletplid;
        $bind[':yuyue_platform'] = $yuyue_platform;
        $bind[':today'] = date('Y-m-d');

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getLastOfPatient
    // 备注: 获取患者的最后一个加号单
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getLastOfPatient ($patientid, $doctorid=0, $createby = null) {
        $cond = "";
        $bind = [];

        if ($createby != null) {
            $cond .= ' and createby = :createby ';
            $bind[':createby'] = $createby;
        }

        if ( $doctorid > 0 ){
            $cond .= ' and doctorid = :doctorid ';
            $bind[':doctorid'] = $doctorid;
        }

        $cond .= " AND patientid = :patientid
            order by id desc
            limit 1 ";

        $bind[':patientid'] = $patientid;

        return Dao::getEntityByCond("RevisitTkt", $cond, $bind);
    }

    // 名称: getLastOfPatient_Open
    // 备注: 获取患者的最后一个打开的加号单
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getLastOfPatient_Open ($patientid, $doctorid=0, $createby = null) {
        $cond = "";
        $bind = [];

        if ($createby != null) {
            $cond .= ' and createby = :createby ';
            $bind[':createby'] = $createby;
        }

        if ( $doctorid > 0 ){
            $cond .= ' and doctorid = :doctorid ';
            $bind[':doctorid'] = $doctorid;
        }

        $cond .= " and patientid = :patientid and isclosed = 0
            order by id desc
            limit 1 ";

        $bind[':patientid'] = $patientid;

        return Dao::getEntityByCond("RevisitTkt", $cond, $bind);
    }

    // 名称: getLastOfPatient_Vaild_Ago
    // 备注: 获取患者今天以前最后一个有效的加号单
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getLastOfPatient_Vaild_Ago ($patientid, $doctorid=0, $createby = null) {
        $cond = "";
        $bind = [];

        if ($createby != null) {
            $cond .= ' and createby=:createby ';
            $bind[':createby'] = $createby;
        }

        if ( $doctorid > 0 ){
            $cond .= ' and doctorid = :doctorid ';
            $bind[':doctorid'] = $doctorid;
        }

        $cond .= " AND patientid = :patientid and thedate <= :today and status = 1
            order by id desc
            limit 1 ";

        $bind[':patientid'] = $patientid;
        $bind[':today'] = date('Y-m-d');

        return Dao::getEntityByCond("RevisitTkt", $cond, $bind);
    }

    // 名称: getListBySchedule
    // 备注: 获取加号单列表
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getListBySchedule (Schedule $schedule) {
        $cond = " and scheduleid = :scheduleid and isclosed = 0 and status = 1 and patientid > 0
            group by patientid ";

        $bind = array(
            ":scheduleid" => $schedule->id);

        return Dao::getEntityListByCond("RevisitTkt", $cond, $bind);
    }

    // 名称: 获取时间在将来的患者的最后一个有效加号单
    // 备注: 用于修改日期
    // 创建: by xuzhe
    // 修改: by xuzhe
    public static function getNextByPatient_Vaild ($patientid, $doctorid=0) {
        $cond = "";
        $bind = [];

        if ( $doctorid > 0 ){
            $cond .= ' and doctorid = :doctorid ';
            $bind[':doctorid'] = $doctorid;
        }

        $cond .= " and patientid = :patientid and thedate > :today and status = 1 and isclosed = 0
            order by id desc
            limit 1 ";

        $bind[':patientid'] = $patientid;
        $bind[':today'] = date('Y-m-d');

        return Dao::getEntityByCond("RevisitTkt", $cond, $bind);
    }

}
