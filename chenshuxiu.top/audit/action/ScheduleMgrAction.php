<?php
// ScheduleMgrAction
class ScheduleMgrAction extends AuditBaseAction
{

    // 接下来的几周的出诊日历
    public function doNextWeeks () {
        $fromtime = XDateTime::getTheMondayBeginTime(date('Y-m-d'));

        $fromdate = date('Y-m-d', $fromtime);
        $todate = date('Y-m-d', $fromtime + 86400 * 27 + 1);

        $sql = "select thedate, daypart, group_concat(id) as scheduleids, group_concat(scheduletplid) as scheduletplids
            from schedules
            where thedate >= :fromdate and thedate <= :todate and doctorid not in
            (select id from doctors where hospitalid=5)
            group by thedate, daypart
            order by thedate, daypart ";
        $bind = [];
        $bind[':fromdate'] = $fromdate;
        $bind[':todate'] = $todate;

        $rows = Dao::queryRows($sql, $bind);

        $arr = [];
        foreach ($rows as $row) {

            $scheduleidsStr = $row['scheduleids'];
            $scheduleids = explode(',', $scheduleidsStr);
            $schedules = Dao::getEntityListByIds('Schedule', $scheduleids);

            $daypart = $row['daypart'];

            if ($daypart == 'all_day') {
                if (! $arr[$row['thedate']]['am']) {
                    $arr[$row['thedate']]['am'] = [];
                }
                if (! $schedules) {
                    $schedules = [];
                }
                if (!isset($arr[$row['thedate']]['am'])) {
                    $arr[$row['thedate']]['am'] = [];
                }
                if (!isset($arr[$row['thedate']]['pm'])) {
                    $arr[$row['thedate']]['pm'] = [];
                }
                $arr[$row['thedate']]['am'] = array_merge($arr[$row['thedate']]['am'], $schedules);
                $arr[$row['thedate']]['pm'] = array_merge($arr[$row['thedate']]['pm'], $schedules);
            } else {
                $arr[$row['thedate']][$row['daypart']] = $schedules;
            }
        }

        XContext::setValue('rows', $arr);

        return self::SUCCESS;
    }

    // 医生出诊列表
    public function doList () {
        $pagenum = XRequest::getValue("pagenum", 1);
        $pagesize = XRequest::getValue("pagesize", 20);

        $doctorid = XRequest::getValue('doctorid', 0);
        $fromdate = XRequest::getValue('fromdate', '');
        $todate = XRequest::getValue('todate', '');
        $scheduletplid = XRequest::getValue('scheduletplid', 0);

        $doctor = Doctor::getById($doctorid);
        $scheduletpl = ScheduleTpl::getById($scheduletplid);

        $cond = "";
        $bind = [];

        if ($doctor) {
            $cond = ' AND doctorid=:doctorid ';
            $bind[':doctorid'] = $doctor->id;
        }

        if ($scheduletpl) {
            $cond = ' AND scheduletplid=:scheduletplid ';

            $bind = [];
            $bind[':scheduletplid'] = $scheduletpl->id;

            $doctor = $scheduletpl->doctor;
        }

        if ($fromdate) {
            $cond .= ' and thedate >= :fromdate ';
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate) {
            $cond .= ' and thedate <= :todate ';
            $bind[':todate'] = $todate;
        }

        $schedules = Dao::getEntityListByCond4Page('Schedule', $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(*) as cnt from schedules where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/schedulemgr/list?doctorid={$doctorid}&scheduletplid={$scheduletplid}&fromdate={$fromdate}&todate={$todate}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        // 医生
        if ($this->mydisease instanceof Disease) {
            $doctors = DoctorDao::getListByDiseaseid($this->mydisease->id);
        }
        XContext::setValue('doctors', $doctors);

        // 模板
        $scheduletpls = array();
        if ($doctor instanceof Doctor) {
            $scheduletpls = $doctor->getScheduleTpls();
        }
        XContext::setValue('scheduletpls', $scheduletpls);

        XContext::setValue('doctor', $doctor);
        XContext::setValue('fromdate', $fromdate);
        XContext::setValue('todate', $todate);
        XContext::setValue('scheduletpl', $scheduletpl);
        XContext::setValue('schedules', $schedules);

        if ($doctor) {
            return "doctor";
        }

        return self::SUCCESS;
    }

    // 医生出诊修改
    public function doModify () {
        $scheduleid = XRequest::getValue('scheduleid', 0);

        $schedule = Schedule::getById($scheduleid);

        XContext::setValue('doctor', $schedule->doctor);
        XContext::setValue('schedule', $schedule);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $scheduleid = XRequest::getValue('scheduleid', 0);
        $maxcnt = XRequest::getValue('maxcnt', 0);
        $status = XRequest::getValue('status', 1);

        $schedule = Schedule::getById($scheduleid);
        $schedule->maxcnt = $maxcnt;

        // 下线门诊示例时，是否也要把已经预约的revisitkt下线
        if ($status == 0) {
            $auditremark = "[{$this->myauditor->name}]下线门诊以及门诊对应的加号单";

            $revisittkts = RevisitTktDao::getListBySchedule($schedule);
            foreach ($revisittkts as $revisittkt) {
                $revisittkt->auditOffline($auditremark);
            }
        }
        $schedule->status = $status;

        $preMsg = date('Y-m-d H:i:s') . " 修改已保存.";
        XContext::setJumpPath("/schedulemgr/modify?scheduleid={$scheduleid}&preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }

    // 批量创建医生出诊
    public function doBatCreateByScheduleTpl () {
        $scheduletplid = XRequest::getValue('scheduletplid', 0);
        $scheduletpl = ScheduleTpl::getById($scheduletplid);

        if ($scheduletpl) {
            $beginday = date('Y-m-d');
            $schedules = Schedule::batCreateByScheduleTpl($scheduletpl, '2020-12-31', $beginday);
        }

        XContext::setJumpPath("/schedulemgr/list?scheduletplid={$scheduletplid}&preMsg=" . urlencode("生成出诊实例成功"));
        return self::SUCCESS;
    }
}
