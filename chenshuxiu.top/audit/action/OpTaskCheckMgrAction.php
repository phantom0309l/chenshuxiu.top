<?php

class OpTaskCheckMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $optaskcheckid = XRequest::getValue("optaskcheckid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if ($optaskcheckid > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $optaskcheckid;
        }

        //获得实体
        $sql = "select *
                    from optaskchecks
                    where 1 = 1 {$cond} order by id desc";
        $opTaskChecks = Dao::loadEntityList4Page("OpTaskCheck", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("opTaskChecks", $opTaskChecks);

        //获得分页
        $countSql = "select count(*)
                    from optaskchecks
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/optaskcheckmgr/list?optaskcheckid={$optaskcheckid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("optaskcheckid", $optaskcheckid);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne() {
        $optaskcheckid = XRequest::getValue("optaskcheckid", 0);

        $opTaskCheck = OpTaskCheck::getById($optaskcheckid);

        XContext::setValue("opTaskCheck", $opTaskCheck);
        return self::SUCCESS;
    }

    public function doAdd() {
        return self::SUCCESS;
    }

    public function doAddPost() {

        $optaskchecktplid = XRequest::getValue("optaskchecktplid", 0);
        $xanswersheetid = XRequest::getValue("xanswersheetid", 0);
        $auditor_id = XRequest::getValue("auditor_id", 0);
        $optask_id = XRequest::getValue("optask_id", 0);
        $checked_auditor_id = XRequest::getValue("checked_auditor_id", 0);
        $checked_time = XRequest::getValue("checked_time", '0000-00-00 00:00:00');
        $status = XRequest::getValue("status", 0);
        $woy = XRequest::getValue("woy", 0);
        $remark = XRequest::getValue("remark", '');


        $row = array();
        $row["optaskchecktplid"] = $optaskchecktplid;
        $row["xanswersheetid"] = $xanswersheetid;
        $row["auditor_id"] = $auditor_id;
        $row["optask_id"] = $optask_id;
        $row["checked_auditor_id"] = $checked_auditor_id;
        $row["checked_time"] = $checked_time;
        $row["status"] = $status;
        $row["woy"] = $woy;
        $row["remark"] = $remark;


        OpTaskCheck::createByBiz($row);

        XContext::setJumpPath("/optaskcheckmgr/list");
        return self::SUCCESS;
    }

    public function doModify() {
        $optaskcheckid = XRequest::getValue("optaskcheckid", 0);

        $opTaskCheck = OpTaskCheck::getById($optaskcheckid);
        DBC::requireTrue($opTaskCheck instanceof OpTaskCheck, "opTaskCheck不存在:{$optaskcheckid}");
        XContext::setValue("opTaskCheck", $opTaskCheck);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost() {
        $optaskcheckid = XRequest::getValue("optaskcheckid", 0);
        $optaskchecktplid = XRequest::getValue("optaskchecktplid", 0);
        $xanswersheetid = XRequest::getValue("xanswersheetid", 0);
        $auditor_id = XRequest::getValue("auditor_id", 0);
        $optask_id = XRequest::getValue("optask_id", 0);
        $checked_auditor_id = XRequest::getValue("checked_auditor_id", 0);
        $checked_time = XRequest::getValue("checked_time", '0000-00-00 00:00:00');
        $status = XRequest::getValue("status", 0);
        $woy = XRequest::getValue("woy", 0);
        $remark = XRequest::getValue("remark", '');

        $opTaskCheck = OpTaskCheck::getById($optaskcheckid);
        DBC::requireTrue($opTaskCheck instanceof OpTaskCheck, "opTaskCheck不存在:{$optaskcheckid}");

        $opTaskCheck->optaskchecktplid = $optaskchecktplid;
        $opTaskCheck->xanswersheetid = $xanswersheetid;
        $opTaskCheck->auditor_id = $auditor_id;
        $opTaskCheck->optask_id = $optask_id;
        $opTaskCheck->checked_auditor_id = $checked_auditor_id;
        $opTaskCheck->checked_time = $checked_time;
        $opTaskCheck->status = $status;
        $opTaskCheck->woy = $woy;
        $opTaskCheck->remark = $remark;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/optaskcheckmgr/modify?optaskcheckid=" . $optaskcheckid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }


    //
    public function doListOfAuditor() {
        $myAuditor = $this->myauditor;
        $myAuditorGroupRef = AuditorGroupRefDao::getByTypeAndAuditorid('base', $myAuditor->id);

        $auditorGroupRefs = AuditorGroupRefDao::getListByAuditorGroupid($myAuditorGroupRef->auditorgroupid);
        //获取本周的起止时间
        $weekStartTimeAndEndTimeArr = $this->getWeekStartTimeAndEndTime(date('Y-m-d'));
        $default_time_slot = $weekStartTimeAndEndTimeArr['startTime'] . ' - ' . $weekStartTimeAndEndTimeArr['endTime'];
        $default_time_spot = date('Y-m-d', time());

        XContext::setValue('auditorGroupRefs', $auditorGroupRefs);
        XContext::setValue('auditor_id', $myAuditor->id);
        XContext::setValue('default_time_slot', $default_time_slot);
        XContext::setValue('default_time_spot', $default_time_spot);
        return self::SUCCESS;
    }

    public function doGetWorkEfficiencyJson() {
        //获取本周的起止时间
        $weekStartTimeAndEndTimeArr = $this->getWeekStartTimeAndEndTime(date('Y-m-d'));
        $auditor_id = XRequest::getValue('auditor_id', $this->myauditor->id);
        $startTime = XRequest::getValue('startTime', $weekStartTimeAndEndTimeArr['week_start']);
        $endTime = XRequest::getValue('endTime', date('Y-m-d', strtotime($weekStartTimeAndEndTimeArr['week_end'])));
        $endTime = date('Y:m:d H:i:s', strtotime($endTime) + 86400);

        $json = [];

        // 关闭任务统计
        $closeOptaskCntGroupByTplid = OpTaskDao::getCntByAuditorAndDonetimeAndStart($auditor_id, $startTime, $endTime, 1, 'optasktplid');
        $closeOptaskCnt = 0;
        if (!empty($closeOptaskCntGroupByTplid)) {
            foreach ($closeOptaskCntGroupByTplid as $key => $optask) {
                $closeOptaskCntGroupByTplid[$key]['name'] = $optask['name'] . ' : ' . $optask['value'] . '个';
                $closeOptaskCnt += $optask['value'];
            }
        }

        // 电话统计
        $call_in_type = [1];
        $call_out_type = [3];

        // 呼出电话总数量
        $cdrMeetingCntOfCallOut = CdrMeetingDao::getCntByAuditorIdAndCallTypeAndStatusAndCreateTime($auditor_id, $call_out_type, [], $startTime, $endTime);
        // 呼出且接通的电话数量
        $cdrMeetingCntOfCallOutAndSuccess = CdrMeetingDao::getCntByAuditorIdAndCallTypeAndStatusAndCreateTime($auditor_id, $call_out_type, [28], $startTime, $endTime);
        // 呼出且接通的电话的平均时长
        $cdrMettingAvgOfCallOutS = CdrMeetingDao::getAvgCallTimeByAuditorIdAndCallTypeAndStatus($auditor_id, 3, [28], $startTime, $endTime);
        $cdrMettingAvgOfCallOutM = round((float)$cdrMettingAvgOfCallOutS / 60, 2);
        // 接起呼入的电话数量
        $cdrMeetingCntOfCallInAndSuccess = CdrMeetingDao::getCntByAuditorIdAndCallTypeAndStatusAndCreateTime($auditor_id, $call_in_type, [1], $startTime, $endTime);
        // 接起呼入的平均通话时长
        $cdrMettingAvgOfCallInS = CdrMeetingDao::getAvgCallTimeByAuditorIdAndCallTypeAndStatus($auditor_id, 1, [1], $startTime, $endTime);
        $cdrMettingAvgOfCallInM = round((float)$cdrMettingAvgOfCallInS / 60, 2);

        // 最后一个电话的结束时间
        $lastMeeting = CdrMeetingDao::getLastEntityByAuditorAndTimeSlot($auditor_id, $startTime, $endTime);
        $lastMeetingTime = date('Y-m-d H:i:s', $lastMeeting->cdr_end_time);

        // 呼出接通的通话时长
        $cdrMeeting_out_list = CdrMeetingService::getCntByAuditorAndTimeSort($auditor_id, $startTime, $endTime);

        // 消息统计
        $cntPushMsg = PushMsgDao::getCntByObjtypeAndObjid('Auditor', $auditor_id, $startTime, $endTime);
        // 最后一条消息的发出时间
        $lastPushMsg = PushMsgDao::getLastEntityByObjtypeAndObjidAndTimeSlot('Auditor', $auditor_id, $startTime, $endTime);
        $lastPushMsgTime = $lastPushMsg->createtime;

        // 拼json
        $json['optask']['cnt'] = $closeOptaskCnt;
        $json['optask']['list'] = $closeOptaskCntGroupByTplid;
        $json['cdrMeeting']['cntOfCallOut'] = $cdrMeetingCntOfCallOut;
        $json['cdrMeeting']['cntOfCallOutSucc'] = $cdrMeetingCntOfCallOutAndSuccess;
        $json['cdrMeeting']['avgOfCallOut'] = $cdrMettingAvgOfCallOutM;
        $json['cdrMeeting']['cntOfCallInSucc'] = $cdrMeetingCntOfCallInAndSuccess;
        $json['cdrMeeting']['avgOfCallIn'] = $cdrMettingAvgOfCallInM;
        $json['cdrMeeting']['lastMeetingTime'] = $lastMeetingTime;
        $json['cdrMeeting']['list'] = $cdrMeeting_out_list;
        $json['pushMsg']['cntPushMsg'] = $cntPushMsg;
        $json['pushMsg']['lastPushMsgTime'] = $lastPushMsgTime;
        $json['startTime'] = date('Y-m-d', strtotime($startTime));
        $json['endTime'] = date('Y-m-d', strtotime($endTime)-1);
        XContext::setValue('json', $json);
        return self::TEXTJSON;
    }


    public function doGetQualityJson() {
        $auditor_id = XRequest::getValue('auditor_id', $this->myauditor->id);
        $date = XRequest::getValue('time', date('Y-m-d', strtotime('-1week')));
        $isnow = XRequest::getValue('isnow', 0);

        // 判断是否时 before 和 after
        if ($isnow == 1) {
            $date = date('Y-m-d', strtotime('+1week', strtotime($date)));
        } elseif ($isnow == -1) {
            $date = date('Y-m-d', strtotime('-1week', strtotime($date)));
        }

        $weekStartTimeAndEndTimeArr = $this->getWeekStartTimeAndEndTime($date);
        $week_start = $weekStartTimeAndEndTimeArr['week_start'];
        $week_end = $weekStartTimeAndEndTimeArr['week_end'];

        // 根据时间断获取 optaskchecks
        $optaskChecks = OpTaskCheckDao::getListByAuditorIdAndTheDateSlot($auditor_id, $week_start, $week_end);

        $data = [];
        $optaskCheckArr = [];

        if (is_array($optaskChecks) && !empty($optaskChecks)) {
            $data['errno'] = 1;
            $data['errmsg'] = '请求成功';
            foreach ($optaskChecks as $key => $optaskCheck) {
                $optaskCheckArr[$key]['patientName'] = $optaskCheck->optask->patient->name;
                $optaskCheckArr[$key]['optaskTplTitle'] = $optaskCheck->optask->optasktpl->title;
                $optaskCheckArr[$key]['optaskCheckId'] = $optaskCheck->id;
                $optaskCheckArr[$key]['thedate'] = $optaskCheck->thedate;
                $optaskCheckArr[$key]['plantime'] = $optaskCheck->optask->plantime;
                $optaskCheckArr[$key]['createAuditorName'] = $optaskCheck->optask ? $optaskCheck->optask->getCreateAuditorName() : '';
                $optaskCheckArr[$key]['shipstr'] = $optaskCheck->optask->user->shipstr;
                $optaskCheckArr[$key]['patientId'] = $optaskCheck->optask->patientid;
                $optaskCheckArr[$key]['is_checked'] = $optaskCheck->status;
            }

            $auditor = Auditor::getById($auditor_id);
            $cnt = $this->getCnt($auditor, $week_start, $week_end);
        } else {
            $data['errno'] = 0;
            $data['errmsg'] = '暂无数据';
            $cnt = [
                'name' => [],
                'value' => []
            ];
        }

        $data['qualityOpTaskCheckCnt'] = $cnt;
        $data['date']['now'] = $date;
        $data['date']['weekStart'] = explode(' ', $week_start)[0];
        $data['date']['weekEnd'] = explode(' ', $week_end)[0];
        $data['data'] = $optaskCheckArr;

        XContext::setValue('json', $data);
        return self::TEXTJSON;
    }

    public function doCheckPost() {
        $optaskCheckId = XRequest::getValue('optask_check_id', '0');
        $sheets = XRequest::getValue('sheets', []);

        $optaskCheck = OpTaskCheck::getById($optaskCheckId);
        DBC::requireTrue($optaskCheck instanceof OpTaskCheck, "id为 {$optaskCheckId} 的 OpTaskCheck 记录不存在");
        $myuser = $this->myuser;
        $objtype = 'OpTaskCheck';
        $objid = $optaskCheck->id;

        $maxXAnswer = XWendaService::doPost($sheets, $myuser, $objtype, $objid);
        $optaskCheck->xanswersheetid = $maxXAnswer->xanswersheetid;
        $optaskCheck->checked_auditor_id = $this->myauditor->id;
        $optaskCheck->checked_time = date('Y-m-d H:i:s');
        $optaskCheck->status = 1;
        $optaskCheck->remark = $maxXAnswer->content;
        return self::BLANK;
    }

    private function getCnt(Auditor $auditor, $week_start, $week_end) {
        $auditorgroup = AuditorGroupRefDao::getByTypeAndAuditorid('base', $auditor->id)->auditorgroup;
        $optaskCheckTplEname = OpTaskCheckTpl::getOpTaskCheckTplEnameByAuditorGroupEname($auditorgroup->ename);
        $optaskCheckTpl = OpTaskCheckTplDao::getByEname($optaskCheckTplEname);
        DBC::requireTrue($optaskCheckTpl instanceof OpTaskCheckTpl, "auditorGroupEname 为 {$auditorgroup->ename} 对应的optaskcheck不存在 ");
        $xquestions = $optaskCheckTpl->xquestionsheet->getQuestions();

        $result = [];
        foreach ($xquestions as $xquestion) {
            if ($xquestion->isChoice()) {
                $xoption = XOption::getByXQuestionidAndContent($xquestion->id, '合格');
                $optackCheckCnt = OpTaskCheckDao::getCntByAuditorAndTimeSlotAndQuestionAndOption($auditor->id, $week_start, $week_end, $xquestion->id, $xoption->id);
                $result['name'][] = $optackCheckCnt['name'] . '     ' . $optackCheckCnt['value'];
                $result['value'][] = $optackCheckCnt['value'];
            }
        }

        return $result;
    }

    private function getWeekStartTimeAndEndTime($date) {
        // 获取周起止时间
        $w = date('w', strtotime($date));
        $time = strtotime("$date -" . ($w ? $w - 1 : 6) . ' days');
        $week_start = date('Y-m-d', $time); //获取周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_end = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', $time), date('d', $time) - date('w', $time) + 7, date('Y')));

        return ['week_start' => $week_start, 'week_end' => $week_end];
    }
}
