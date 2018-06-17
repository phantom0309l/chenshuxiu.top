<?php
/*
 * ScheduleDao
 */
class ScheduleDao extends Dao
{
    // 名称: getByDoctorThedate
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctorThedate (Doctor $doctor, $thedate) {
        $cond = " and doctorid=:doctorid and thedate=:thedate ";
        $bind = array(
            ':doctorid' => $doctor->id,
            ':thedate' => $thedate);
        return Dao::getEntityByCond('Schedule', $cond, $bind);
    }

    // 名称: getListByDoctorThedate
    // 备注:实例列表 of Doctor
    // 创建:
    // 修改:
    public static function getListByDoctorThedate (Doctor $doctor, $thedate) {
        $cond = " and doctorid=:doctorid and thedate=:thedate and scheduletplid <> 0 ";
        $bind = array(
            ':doctorid' => $doctor->id,
            ':thedate' => $thedate);
        return Dao::getEntityListByCond('Schedule', $cond, $bind);
    }

    // 名称: getCntOfScheduleTpl
    // 备注:实例数目 of 模板
    // 创建:
    // 修改:
    public static function getCntOfScheduleTpl (ScheduleTpl $scheduletpl) {
        $bind = [];
        $bind[':scheduletplid'] = $scheduletpl->id;
        $sql = ' select count(*) as cnt from schedules where scheduletplid = :scheduletplid ';

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfScheduleTplGtToday
    // 备注:实例数目 of 模板,大于今天
    // 创建:
    // 修改:
    public static function getCntOfScheduleTplGtToday (ScheduleTpl $scheduletpl) {
        $bind = [];
        $bind[':scheduletplid'] = $scheduletpl->id;
        $bind[':today'] = date('Y-m-d');
        $sql = ' select count(*) as cnt from schedules where scheduletplid = :scheduletplid and thedate >= :today ';

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByDoctor
    // 备注:实例列表 of Doctor
    // 创建:
    // 修改:
    public static function getListByDoctor (Doctor $doctor, $fromdate, $todate) {
        $cond = " and doctorid=:doctorid and thedate>=:fromdate and thedate<=:todate order by thedate, daypart ";
        $bind = array(
            ':doctorid' => $doctor->id,
            ':fromdate' => $fromdate,
            ':todate' => $todate);
        return Dao::getEntityListByCond('Schedule', $cond, $bind);
    }

    // 名称: getValidListByDoctor
    // 备注:实例列表 of Doctor
    // 创建:chenning
    // 修改:
    public static function getValidListByDoctor (Doctor $doctor, $fromdate, $todate, $diseaseid = null) {

        $cond = " and doctorid=:doctorid and thedate>=:fromdate and thedate<=:todate and status = 1";
        $bind = array(
            ':doctorid' => $doctor->id,
            ':fromdate' => $fromdate,
            ':todate' => $todate
        );
        if (!empty($diseaseid)) {
            $cond.= " and diseaseid = :diseaseid ";
            $bind[':diseaseid'] = $diseaseid;
        }
        $cond .= " order by thedate, daypart ";

        return Dao::getEntityListByCond('Schedule', $cond, $bind);
    }

    // 名称: getListByDoctorDisease 
    // 备注:实例列表 of Doctor
    // 创建:
    // 修改:
    public static function getListByDoctorDisease (Doctor $doctor, Disease $disease, $fromdate, $todate) {
        $cond = " and doctorid=:doctorid  and diseaseid=:diseaseid and thedate>=:fromdate and thedate<=:todate order by thedate ";
        $bind = array(
            ':doctorid' => $doctor->id,
            ':diseaseid' => $disease->id,
            ':fromdate' => $fromdate,
            ':todate' => $todate);
        return Dao::getEntityListByCond('Schedule', $cond, $bind);
    }

    // 名称: getListByScheduleTpl
    // 备注:例列表 of 模板, 翻页
    // 创建:
    // 修改:
    public static function getListByScheduleTpl (ScheduleTpl $scheduletpl, $pagesize = 1000, $pagenum = 1) {
        $cond = ' AND scheduletplid = :scheduletplid order by thedate ';
        $bind = [];
        $bind[':scheduletplid'] = $scheduletpl->id;
        return Dao::getEntityListByCond4Page('Schedule', $pagesize, $pagenum, $cond, $bind);
    }

    // 名称: getListByScheduleTpl_DateSpan
    // 备注:实例列表 of 模板, 日期范围
    // 创建:
    // 修改:
    public static function getListByScheduleTpl_DateSpan (ScheduleTpl $scheduletpl, $begindate = '2016-04-01', $enddate = '2016-06-01') {
        $cond = ' AND scheduletplid = :scheduletplid AND thedate >= :begindate AND thedate < :enddate order by thedate ';
        $bind = [];
        $bind[':scheduletplid'] = $scheduletpl->id;
        $bind[':begindate'] = $begindate;
        $bind[':enddate'] = $enddate;

        return Dao::getEntityListByCond('Schedule', $cond, $bind);
    }

    // 名称: getMaxOneByScheduleTpl
    // 备注:最大实例
    // 创建:
    // 修改:
    public static function getMaxOneByScheduleTpl (ScheduleTpl $scheduletpl) {
        $bind = [];
        $bind[':scheduletplid'] = $scheduletpl->id;

        $cond = ' and scheduletplid = :scheduletplid order by thedate desc limit 1 ';

        return Dao::getEntityByCond('Schedule', $cond, $bind);
    }

    // 名称: getMinOneByScheduleTpl
    // 备注:最小实例
    // 创建:
    // 修改:
    public static function getMinOneByScheduleTpl (ScheduleTpl $scheduletpl) {
        $bind = [];
        $bind[':scheduletplid'] = $scheduletpl->id;
        $cond = ' and scheduletplid = :scheduletplid order by thedate asc limit 1 ';

        return Dao::getEntityByCond('Schedule', $cond, $bind);
    }

}
