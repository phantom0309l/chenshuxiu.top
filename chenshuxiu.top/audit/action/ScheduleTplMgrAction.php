<?php

class ScheduleTplMgrAction extends AuditBaseAction
{

    public function doListNew() {
        $auditorid_market = XRequest::getValue("auditorid_market", 0);
        $doctor_name = XRequest::getValue("doctor_name", '');
        $pagesize = XRequest::getValue("pagesize", 30);
        $pagenum = XRequest::getValue("pagenum", 1);

        $bind = [];
        $cond = "";
        $sql = "select a.*
                from doctors a
                inner join doctordiseaserefs b on b.doctorid = a.id
                where 1=1 ";

        $diseaseidstr = $this->getContextDiseaseidStr();

        $cond .= " and b.diseaseid in ($diseaseidstr) ";

        if ($doctor_name) {
            $cond .= " and a.name like :doctor_name ";
            $bind[':doctor_name'] = "%{$doctor_name}%";
        }

        if ($auditorid_market) {
            $cond .= " and a.auditorid_market = :auditorid_market";
            $bind[":auditorid_market"] = $auditorid_market;
        }
        $sql .= $cond;

        $doctors = Dao::loadEntityList4Page("Doctor", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("doctors", $doctors);

        // 翻页begin
        $countSql = "select count(*)
                from doctors a
                inner join doctordiseaserefs b on b.doctorid = a.id
                where 1=1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/scheduletplmgr/listnew?auditorid_market={$auditorid_market}&doctor_name={$doctor_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        XContext::setValue("doctor_name", $doctor_name);
        XContext::setValue("auditorid_market", $auditorid_market);
        return self::SUCCESS;
    }

    public function doUpdateScheduleTimeJson() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        $time = date("Y-m-d H:i:s", time());
        if ($doctor instanceof Doctor) {
            $doctor->lastschedule_updatetime = $time;
        }
        echo $time;
        return self::BLANK;
    }

    // 设置门诊相关
    public function doListOfDoctor() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $scheduletplid = XRequest::getValue("scheduletplid", 0);

        $doctor = Doctor::getById($doctorid);
        $scheduletpl = ScheduleTpl::getById($scheduletplid);
        if ($scheduletpl) {
            $doctor = $scheduletpl->doctor;
        }

        $scheduletpls = $doctor->getScheduleTpls();
        $scheduletplTable = ScheduleTplService::getTableForDoctor($scheduletpls);

        XContext::setValue("doctor", $doctor);
        XContext::setValue("scheduletpl", $scheduletpl);
        XContext::setValue("scheduletpls", $scheduletpls);
        XContext::setValue("scheduletplTable", $scheduletplTable);

        return self::SUCCESS;
    }

    public function doAjaxModifyBulletin() {
        $bulletin = XRequest::getValue("bulletin", "");

        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, "医生不存在");

        $doctor->bulletin = $bulletin;

        return self::TEXTJSON;
    }

    public function doDeleteScheduleJson() {
        $scheduletplid = XRequest::getValue("scheduletplid", 0);
        $scheduletpl = ScheduleTpl::getById($scheduletplid);
        if ($scheduletpl instanceof ScheduleTpl) {
            $scheduletpl->remove();
        }
        echo "ok";
        return self::BLANK;
    }

    public function doDeletePost() {
        $scheduletplid = XRequest::getValue("scheduletplid", 0);
        $scheduletpl = ScheduleTpl::getById($scheduletplid);
        if ($scheduletpl instanceof ScheduleTpl) {
            if ($scheduletpl->getScheduleCnt() == 0) {
                $doctor = $scheduletpl->doctor;
                $scheduletpl->remove();
            } else {
                DBC::requireTrue(false, "删除失败，[{$scheduletpl->id}]还有实例！");
            }
        }

        XContext::setJumpPath("/scheduletplmgr/listofdoctor?doctorid={$doctor->id}");

        return self::SUCCESS;
    }

    // 新建
    public function doAddHtml() {
        $doctorid = XRequest::getValue("doctorid", 0);

        $doctor = Doctor::getById($doctorid);

        XContext::setValue("doctor", $doctor);

        return self::SUCCESS;
    }

    // 新建 提交
    public function doAddPost() {
        $is_show_p_wx = XRequest::getValue('is_show_p_wx', []);

        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        $diseaseid = XRequest::getValue("diseaseid", 0);
        // MARK: - 现在兼容了全部疾病
        if ($diseaseid > 0) {
            $disease = Disease::getById($diseaseid);
            DBC::requireNotEmpty($disease, "疾病为空");
        }
        $op_hz = XRequest::getValue("op_hz", '');
        $op_date = XRequest::getValue("op_date", '');
        $day_part = XRequest::getValue("day_part", '');
        $op_type = XRequest::getValue("op_type", '');
        $scheduletpl_mobile = XRequest::getValue("scheduletpl_mobile", '');
        $scheduletpl_cost = XRequest::getValue("scheduletpl_cost", '');

        $begin_hour_str_arr = XRequest::getValue("begin_hour_str", []);

        $wday = date("w", strtotime($op_date));
        $wday = ($wday == 0) ? 7 : $wday;

        $addressstr = XRequest::getValue("address", '');
        $scheduletpl_address = XRequest::getValue('scheduletpl', []);
        $scheduletpl_address = PatientAddressService::fixNull($scheduletpl_address);

        $tip = XRequest::getValue("tip", '');
        $maxcnt = XRequest::getValue("maxcnt", 0);

        $see_patienttagtplids = XRequest::getValue('see_patienttagtplids', []);
        $see_patienttagtplids = implode(',', $see_patienttagtplids);

        $row = array();
        $row['doctorid'] = $doctorid;
        $row['diseaseid'] = $diseaseid;
        $row["op_hz"] = $op_hz;
        $row["op_date"] = $op_date;
        $row["day_part"] = $day_part;
        $row["op_type"] = $op_type;
        $row["scheduletpl_mobile"] = $scheduletpl_mobile;
        $row["scheduletpl_cost"] = $scheduletpl_cost;
        $row["begin_hour_str"] = json_encode($begin_hour_str_arr, JSON_UNESCAPED_UNICODE);
        $row["wday"] = $wday;
        $row["tip"] = $tip;
        $row["maxcnt"] = $maxcnt;
        $row["is_show_p_wx_json"] = json_encode($is_show_p_wx, JSON_UNESCAPED_UNICODE);
        $row["see_patienttagtplids"] = $see_patienttagtplids;
        $row["xprovinceid"] = $scheduletpl_address['xprovinceid'];
        $row["xcityid"] = $scheduletpl_address['xcityid'];
        $row["xcountyid"] = $scheduletpl_address['xcountyid'];
        $row["content"] = $addressstr;
        $scheduletpl = ScheduleTpl::createByBiz($row);

        XContext::setJumpPath("/scheduletplmgr/listofdoctor?scheduletplid={$scheduletpl->id}");

        return self::SUCCESS;
    }

    // 修改
    public function doModifyHtml() {
        $scheduletplid = XRequest::getValue("scheduletplid", 0);
        $scheduletpl = ScheduleTpl::getById($scheduletplid);

        XContext::setValue("doctor", $scheduletpl->doctor);
        XContext::setValue("scheduletpl", $scheduletpl);
        return self::SUCCESS;
    }

    // 大修 提交, 没有实例的情况下, 可以随意改
    public function doModifyPost() {
        $is_show_p_wx = XRequest::getValue('is_show_p_wx', []);
        $arr = [
            'diseaseid' => 1,
            'op_hz' => 1,
            'day_part' => 1,
            'op_type' => 1,
            'scheduletpl_mobile' => 1,
            'scheduletpl_cost' => 1,
            'begin_hour_str' => 1,
            'op_date' => 1,
            'maxcnt' => 1,
            'address' => 1,
            'tip' => 1
        ];
        $is_show_p_wx += $arr;

        $scheduletplid = XRequest::getValue("scheduletplid", 0);
        $op_date = XRequest::getValue("op_date", '');
        $wday = date("w", strtotime($op_date));
        $wday = ($wday == 0) ? 7 : $wday;

        $diseaseid = XRequest::getValue("diseaseid", 0);
        // MARK: - 现在兼容了全部疾病
        if ($diseaseid > 0) {
            $disease = Disease::getById($diseaseid);
            DBC::requireNotEmpty($disease, "疾病为空");
        }
        $op_hz = XRequest::getValue("op_hz", '');
        $day_part = XRequest::getValue("day_part", '');
        $op_type = XRequest::getValue("op_type", '');
        $scheduletpl_mobile = XRequest::getValue("scheduletpl_mobile", '');
        $scheduletpl_cost = XRequest::getValue("scheduletpl_cost", '');
        $begin_hour_str_arr = XRequest::getValue("begin_hour_str", []);
        $tip = XRequest::getValue("tip", '');
        $maxcnt = XRequest::getValue("maxcnt", 0);

        $see_patienttagtplids = XRequest::getValue('see_patienttagtplids', []);
        $see_patienttagtplids = implode(',', $see_patienttagtplids);

        // 门诊地址
        $addressstr = XRequest::getValue("address", '');
        $scheduletpl_address = XRequest::getValue('scheduletpl', []);
        $scheduletpl_address = PatientAddressService::fixNull($scheduletpl_address);

        $scheduletpl = ScheduleTpl::getById($scheduletplid);
        $scheduletpl->diseaseid = $diseaseid;
        $scheduletpl->op_date = $op_date;
        $scheduletpl->day_part = $day_part;
        $scheduletpl->wday = $wday;
        $scheduletpl->op_hz = $op_hz;
        $scheduletpl->op_type = $op_type;
        $scheduletpl->scheduletpl_mobile = $scheduletpl_mobile;
        $scheduletpl->scheduletpl_cost = $scheduletpl_cost;
        $scheduletpl->begin_hour_str = json_encode($begin_hour_str_arr, JSON_UNESCAPED_UNICODE);
        $scheduletpl->tip = $tip;
        $scheduletpl->maxcnt = $maxcnt;
        $scheduletpl->is_show_p_wx_json = json_encode($is_show_p_wx, JSON_UNESCAPED_UNICODE);
        $scheduletpl->see_patienttagtplids = $see_patienttagtplids;
        $scheduletpl->xprovinceid = $scheduletpl_address['xprovinceid'];
        $scheduletpl->xcityid = $scheduletpl_address['xcityid'];
        $scheduletpl->xcountyid = $scheduletpl_address['xcountyid'];
        $scheduletpl->content = $addressstr;

        XContext::setJumpPath("/scheduletplmgr/listofdoctor?scheduletplid={$scheduletplid}");

        return self::SUCCESS;
    }

    // 小修 提交, 不修改日期, 只修改: 疾病, 类型, maxcnt
    public function doModifySimplePost() {
        // echo "doModifySimplePost ===\n";
        // exit;

        $scheduletplid = XRequest::getValue("scheduletplid", 0);

        $diseaseid = XRequest::getValue("diseaseid", 0);
        // MARK: - 现在兼容了全部疾病
        if ($diseaseid > 0) {
            $disease = Disease::getById($diseaseid);
            DBC::requireNotEmpty($disease, "疾病为空");
        }
        $op_type = XRequest::getValue("op_type", '');
        $scheduletpl_mobile = XRequest::getValue("scheduletpl_mobile", '');
        $scheduletpl_cost = XRequest::getValue("scheduletpl_cost", '');

        $begin_hour_str_arr = XRequest::getValue("begin_hour_str", []);

        $tip = XRequest::getValue("tip", '');
        $maxcnt = XRequest::getValue("maxcnt", 0);
        $tip = XRequest::getValue("tip", '');

        $see_patienttagtplids = XRequest::getValue('see_patienttagtplids', []);
        $see_patienttagtplids = implode(',', $see_patienttagtplids);

        $is_show_p_wx = XRequest::getValue('is_show_p_wx', []);
        $arr = [
            'diseaseid' => 1,
            'op_hz' => 1,
            'day_part' => 1,
            'op_type' => 1,
            'scheduletpl_mobile' => 1,
            'scheduletpl_cost' => 1,
            'begin_hour_str' => 1,
            'op_date' => 1,
            'maxcnt' => 1,
            'address' => 1,
            'tip' => 1
        ];
        $is_show_p_wx += $arr;

        $scheduletpl = ScheduleTpl::getById($scheduletplid);
        $scheduletpl->diseaseid = $diseaseid;
        $scheduletpl->op_type = $op_type;
        $scheduletpl->begin_hour_str = json_encode($begin_hour_str_arr, JSON_UNESCAPED_UNICODE);
        $scheduletpl->tip = $tip;
        $scheduletpl->maxcnt = $maxcnt;
        $scheduletpl->scheduletpl_mobile = $scheduletpl_mobile;
        $scheduletpl->scheduletpl_cost = $scheduletpl_cost;
        $scheduletpl->tip = $tip;
        $scheduletpl->see_patienttagtplids = $see_patienttagtplids;
        $scheduletpl->is_show_p_wx_json = json_encode($is_show_p_wx, JSON_UNESCAPED_UNICODE);

        // 修改门诊地址
        $addressstr = XRequest::getValue("address", '');
        $scheduletpl_address = XRequest::getValue('scheduletpl', []);

        $scheduletpl->xprovinceid = $scheduletpl_address['xprovinceid'] ?? 0;
        $scheduletpl->xcityid = $scheduletpl_address['xcityid'] ?? 0;
        $scheduletpl->xcountyid = $scheduletpl_address['xcountyid'] ?? 0;
        $scheduletpl->content = $addressstr;

        $schedules = ScheduleDao::getListByScheduleTpl_DateSpan($scheduletpl, date('Y-m-d'), '2020-01-01');
        foreach ($schedules as $a) {
            $a->diseaseid = $diseaseid;
            $a->tkttype = $op_type;
            $a->maxcnt = $maxcnt;
        }

        XContext::setJumpPath("/scheduletplmgr/listofdoctor?scheduletplid={$scheduletplid}");

        return self::SUCCESS;
    }

    // 关闭
    public function doClosePost() {
        $scheduletplid = XRequest::getValue("scheduletplid", 0);

        $scheduletpl = ScheduleTpl::getById($scheduletplid);

        $revisitTktCnt_sum = 0;
        $scheduleCnt_delete = 0;
        // 删除未来还没有被预订的出诊实例
        $schedules = ScheduleDao::getListByScheduleTpl_DateSpan($scheduletpl, date('Y-m-d'), '2020-01-01');
        foreach ($schedules as $a) {
            $cnt = $a->getRevisitTktCnt();
            $revisitTktCnt_sum += $cnt;

            if ($cnt < 1) {
                $a->remove();
                $scheduleCnt_delete++;
            }
        }

        $preMsg = '';
        // 未来没有加号单,则可以下线
        if ($revisitTktCnt_sum < 1) {
            $scheduletpl->status = 0;
            $preMsg = "成功关闭,并删除没有加号单的出诊实例 {$scheduleCnt_delete} 个";
        } else {
            $preMsg = "关闭失败,尚有加号单未到期";
        }

        XContext::setJumpPath("/scheduletplmgr/listofdoctor?scheduletplid={$scheduletplid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 打开
    public function doOpenPost() {
        $scheduletplid = XRequest::getValue("scheduletplid", 0);

        $entity = ScheduleTpl::getById($scheduletplid);
        if ($entity instanceof ScheduleTpl) {
            $entity->status = 1;
        }

        XContext::setJumpPath("/scheduletplmgr/listofdoctor?scheduletplid={$scheduletplid}");
        return self::SUCCESS;
    }
}
