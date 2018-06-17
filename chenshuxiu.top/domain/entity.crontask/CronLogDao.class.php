<?php
// CronLogDao

class CronLogDao extends Dao
{
    // 名称: getByCreateDateTypestr
    // 备注:by createdate + typestr
    // 创建:
    // 修改:
    public static function getByCreateDateTypestr ($createdate, $typestr) {
        $cond = "AND DATE(createtime) = :createdate AND typestr=:typestr";
        $bind = array(
            ":createdate" => $createdate,
            ":typestr" => $typestr);
        return Dao::getEntityByCond("CronLog", $cond, $bind, 'statdb');
    }

    // 名称: getCnt
    // 备注:总行数
    // 创建:
    // 修改:
    public static function getCnt () {
        $sql = "select count(*) from cronlogs ";
        return Dao::queryValue($sql, [], 'statdb');
    }

    // 名称: getLastByTypeStr
    // 备注:某个时间以后的最后一条cronlog by typestr + offsettime
    // 创建:
    // 修改:
    public static function getLastByTypeStr ($typestr, $offsettime = "") {
        $bind = [];
        $bind[':typestr'] = $typestr;
        if ($offsettime) {
            $cond = "AND typestr=:typestr AND createtime > :createtime order by id desc";
            $bind[':createtime'] = $offsettime;
        } else {
            $cond = "AND typestr=:typestr order by id desc";
        }
        return Dao::getEntityByCond("CronLog", $cond, $bind, 'statdb');
    }

    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList ($pagenum = 1, $rownum = 15) {
        $pagenum = intval($pagenum);
        $rownum = intval($rownum);

        $skipnum = ($pagenum - 1) * $rownum;

        $cond = "ORDER BY id DESC LIMIT $skipnum , $rownum";
        return Dao::getEntityListByCond("CronLog", $cond, [], 'statdb');
    }

    // 名称: getListByRemarkAndDateSpan
    // 备注:获取列表 by remark + datespan
    // 创建:
    // 修改:
    public static function getListByRemarkAndDateSpan ($remark, $startdate, $enddate) {
        $cond = "AND remark = :remark AND createtime > :startdate AND createtime < :enddate ";

        $bind = array(
            ":remark" => $remark,
            ":startdate" => $startdate,
            ":enddate" => $enddate);

        return Dao::getEntityListByCond("CronLog", $cond, $bind, 'statdb');
    }

    public static function getLastOneByCronTab (CronTab $crontab) {
        $cond = "and crontabid=:crontabid order by id desc";

        $bind = [];
        $bind[':crontabid'] = $crontab->id;

        return Dao::getEntityByCond("CronLog", $cond, $bind, 'statdb');
    }
}
