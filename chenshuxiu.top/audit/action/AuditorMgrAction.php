<?php

class AuditorMgrAction extends AuditBaseAction
{

    // 员工列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 100);
        $pagenum = XRequest::getValue("pagenum", 1);
        $type = XRequest::getValue("type", 0);
        $auditroleid = XRequest::getValue("auditroleid", 0);
        $auditroleid = intval($auditroleid);

        $cond = " and status = 1 ";
        $bind = [];

        if ($type > 0) {
            $cond .= " and type=:type ";
            $bind[':type'] = $type;
        }

        if ($auditroleid > 0) {
            $cond .= " and auditroleids like '%{$auditroleid}%' ";
        }

        $auditors = Dao::getEntityListByCond4Page("Auditor", $pagesize, $pagenum, $cond, $bind);
        XContext::setValue("auditors", $auditors);
        XContext::setValue('type', $type);
        XContext::setValue('auditroleid', $auditroleid);

        // 翻页begin
        $countSql = "select count(*) as cnt from auditors where 1=1 $cond";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/auditormgr/list/?auditroleid={$auditroleid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url, $bind);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        return self::SUCCESS;
    }

    // 离职员工列表
    public function doLeaveOfficeList() {
        $auditors = Dao::getEntityListByCond("Auditor", " and status = 0 order by updatetime ");

        XContext::setValue("auditors", $auditors);

        return self::SUCCESS;
    }

    // 市场员工列表
    public function doListForMarket() {
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        $auditorid_prev = XRequest::getValue("auditorid_prev", 0);
        $xprovinceid_control = XRequest::getValue("xprovinceid_control", 0);
        $is_standard = XRequest::getValue("is_standard", 2);

        $cond = '';
        $bind = [];

        if ($diseasegroupid > 0) {
            $cond .= " and diseasegroupid = :diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        if ($auditorid_prev > 0) {
            $cond .= " and auditorid_prev = :auditorid_prev ";
            $bind[":auditorid_prev"] = $auditorid_prev;
        }

        if ($xprovinceid_control > 0) {
            $cond .= " and xprovinceid_control = :xprovinceid_control ";
            $bind[":xprovinceid_control"] = $xprovinceid_control;
        }

        if ($is_standard == 0) {
            $cond .= " and standard_date = '0000-00-00' ";
        }

        if ($is_standard == 1) {
            $cond .= " and standard_date != '0000-00-00' ";
        }

        $cond .= ' and status = 1 order by id ';
        $auditorsBak = Dao::getEntityListByCond("Auditor", $cond, $bind);

        $auditors = array();
        foreach ($auditorsBak as $a) {
            if ($a->isHasRole(array(
                'market',
                'market_jianzhi'))) {
                $auditors[] = $a;
            }
        }
        XContext::setValue("auditors", $auditors);
        XContext::setValue('diseasegroupid', $diseasegroupid);
        XContext::setValue('auditorid_prev', $auditorid_prev);
        XContext::setValue('xprovinceid_control', $xprovinceid_control);
        XContext::setValue('is_standard', $is_standard);

        $sql = "select
                    a.auditorid_market as auditorid,
                    tt.themonth,
                    sum(tt.amount) as amount,
                    sum(tt.cnt) as cnt
                from doctors a
                inner join (
                    select
                    the_doctorid,
                    left(time_pay,7) as themonth,
                    sum(cast(item_sum_price as signed)-cast(refund_amount as signed)) as amount,
                    count(*) as cnt
                    from shoporders
                    where is_pay=1 group by the_doctorid, themonth
                )tt on tt.the_doctorid = a.id
                group by a.auditorid_market, tt.themonth order by a.auditorid_market asc, tt.themonth desc";
        $arr = Dao::queryRows($sql);

        // 整理一下数据
        $results = [];
        foreach ($arr as $a) {
            $auditorid = $a["auditorid"];
            if (!isset($results[$auditorid])) {
                $results[$auditorid] = [];
            }

            $themonth = $a["themonth"];
            if (!isset($results[$auditorid][$themonth])) {
                $results[$auditorid][$themonth] = [];
            }

            $results[$auditorid][$themonth]["cnt"] = $a["cnt"];
            $results[$auditorid][$themonth]["amount"] = sprintf("%.2f", $a["amount"] / 100);
        }
        XContext::setValue('results', $results);

        return self::SUCCESS;
    }

    // 员工推广码
    public function doQrcode() {
        $auditor = Dao::getEntityById("Auditor", XRequest::getValue('auditorid', 0));
        $auditor->getQrTicket();
        XContext::setValue('auditor', $auditor);
        return self::SUCCESS;
    }

    // 员工新建
    public function doAdd() {
        $type = XRequest::getValue("type", 0);
        $auditorid_prev = XRequest::getValue("auditorid_prev", 0);
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        $xprovinceid_control = XRequest::getValue("xprovinceid_control", 0);
        XContext::setValue('type', $type);
        XContext::setValue('auditorid_prev', $auditorid_prev);
        XContext::setValue('xprovinceid_control', $xprovinceid_control);
        XContext::setValue('diseasegroupid', $diseasegroupid);
        return self::SUCCESS;
    }

    // 员工新建提交
    public function doAddPost() {
        $type = XRequest::getValue("type", 0);
        DBC::requireNotEmpty($type, '请选择员工类型');

        $pictureid = XRequest::getValue("pictureid", 0);
        $name = XRequest::getValue("name", '');
        $username = XRequest::getValue("username", '');

        $regex = '/[a-zA-Z0-9]+/';
        if (false == preg_match($regex, $username, $match)) {
            $preMsg = "用户名不能为空或包含汉字";
            XContext::setJumpPath("/auditormgr/add?preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        $user = UserDao::getByUserName($username);
        if ($user instanceof User) {
            $preMsg = "{$username}已存在,建议修改用户名为 fc+用户拼音 例如：fczhangsan";
            XContext::setJumpPath("/auditormgr/add?preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        $mobile = XRequest::getValue("mobile", '');
        DBC::requireNotEmpty($mobile, '手机号不能为空');
        $password = XRequest::getValue("password", '');
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        $xprovinceid_control = XRequest::getValue("xprovinceid_control", 0);
        $auditorid_prev = XRequest::getValue("auditorid_prev", 0);
        $cdr_no1 = XRequest::getValue("cdr_no1", "");
        $cdr_no2 = XRequest::getValue("cdr_no2", "");
        $auditremark = XRequest::getValue("auditremark", '');

        $auditorGroupId = XRequest::getValue("auditorgroupid", 0);

        if (empty($password)) {
            $password = substr($mobile, -6);
        }

        $sql = "select max(id) as maxid from auditors";
        $maxid = Dao::queryValue($sql, []);

        // user 创建
        $row = array();
        $row["id"] = $maxid + 1;
        $row["name"] = $name;
        $row["username"] = $username;
        $row["mobile"] = $mobile;
        $row["password"] = $password;
        $row["auditremark"] = $auditremark;

        $user = User::createByBiz($row);

        // auditor 创建
        $row = array();
        $row["id"] = $maxid + 1;
        $row["type"] = $type;
        $row["pictureid"] = $pictureid;
        $row["name"] = $name;
        $row["userid"] = $user->id;
        $row["diseasegroupid"] = $diseasegroupid;
        $row["xprovinceid_control"] = $xprovinceid_control;
        $row["auditorid_prev"] = $auditorid_prev;
        $row["cdr_no1"] = $cdr_no1;
        $row["cdr_no2"] = $cdr_no2;
        $row["remark"] = $auditremark;
        $auditor = Auditor::createByBiz($row);

        if ($auditorGroupId) {
            $row = [
                'auditorid' => $auditor->id,
                'auditorgroupid' => $auditorGroupId
            ];

            AuditorGroupRef::createByBiz($row);
        }

        XContext::setJumpPath("/auditormgr/list");
        return self::SUCCESS;
    }

    // 员工修改
    public function doModify() {
        $auditor = Dao::getEntityById("Auditor", XRequest::getValue('auditorid', 0));
        $auditorGroupRef = AuditorGroupRefDao::getByTypeAndAuditorid('base', $auditor->id);

        $auditorGroupId = $auditorGroupRef instanceof AuditorGroupRef ? $auditorGroupRef->auditorgroupid : 0;

        XContext::setValue('auditor', $auditor);
        XContext::setValue('auditorGroupId', $auditorGroupId);
        return self::SUCCESS;
    }

    // 员工修改提交
    public function doModifyPost() {
        $pictureid = XRequest::getValue('pictureid', 0);
        $status = XRequest::getValue('status', 1);
        $type = XRequest::getValue('type', 1);
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        $xprovinceid_control = XRequest::getValue("xprovinceid_control", 0);
        $auditorid_prev = XRequest::getValue("auditorid_prev", 0);
        $standard_date = XRequest::getValue("standard_date", "0000-00-00");
        $cdr_no1 = XRequest::getValue("cdr_no1", "");
        $cdr_no2 = XRequest::getValue("cdr_no2", "");

        $auditorGroupId = XRequest::getValue("auditorgroupid", 0);

        $auditor = Dao::getEntityById("Auditor", XRequest::getValue('auditorid', 0));

        $auditroleids = XRequest::getValue('auditroleids', array());

        $auditor->name = XRequest::getValue('name', $auditor->name);
        $auditor->auditroleids = implode(',', $auditroleids);
        $auditor->remark = XRequest::getValue('remark', $auditor->remark);
        $auditor->pictureid = $pictureid;
        $auditor->status = $status;
        $auditor->type = $type;
        $auditor->diseasegroupid = $diseasegroupid;
        $auditor->xprovinceid_control = $xprovinceid_control;
        $auditor->auditorid_prev = $auditorid_prev;
        $auditor->standard_date = $standard_date;
        $auditor->cdr_no1 = $cdr_no1;
        $auditor->cdr_no2 = $cdr_no2;

        //更新员工的基础员工组配置
        AuditorGroupRefService::updateBaseType($auditor, $auditorGroupId);

        XContext::setJumpPath('/auditormgr/list');
        return self::SUCCESS;
    }

    // 员工开启监控
    public function doOpenOpsJson() {
        $wxuserid = XRequest::getValue('wxuserid', 0);

        $wxuser = WxUser::getById($wxuserid);

        if (false == $wxuser instanceof WxUser) {
            echo 'wxuserid 不存在';
            return self::BLANK;
        }

        $wxuser->setOpsOpen();
        echo 'suc';
        return self::BLANK;
    }

    // 员工关闭监控
    public function doCloseOpsJson() {
        $wxuserid = XRequest::getValue('wxuserid', 0);

        $wxuser = WxUser::getById($wxuserid);

        if (false == $wxuser instanceof WxUser) {
            echo 'wxuserid 不存在';
            return self::BLANK;
        }

        $wxuser->setOpsClose();
        echo 'suc';
        return self::BLANK;
    }

    // 员工接受消息推送
    public function doOpenSendMsg() {
        $auditorid = XRequest::getValue('auditorid', 0);

        $auditor = Auditor::getById($auditorid);

        $auditor->can_send_msg = 1;
        echo 'suc';
        return self::BLANK;
    }

    // 员工拒绝消息推送
    public function doCloseSendMsg() {
        $auditorid = XRequest::getValue('auditorid', 0);

        $auditor = Auditor::getById($auditorid);

        $auditor->can_send_msg = 0;
        echo 'suc';
        return self::BLANK;
    }

    // 员工数据清理
    public function doClearData() {
        $userid = XRequest::getValue('userid', 0);
        $user = User::getById($userid);
        XContext::setValue('user', $user);
        return self::SUCCESS;
    }

    // 员工数据清理Json
    public function doClearDataJson() {
        $userid = XRequest::getValue('userid', 0);
        if ($userid < 10000 || $userid > 20000) {
            echo '数据不能被删除';
            return self::BLANK;
        }

        $removeArr = array(
            "studyplans" => "StudyPlan",
            "patientpgrouprefs" => "PatientPgroupRef");
        foreach ($removeArr as $table => $entity_name) {
            $bind = [];
            $bind[":userid"] = $userid;
            $sql = "select id from {$table} where userid = :userid order by id desc limit 150";
            $arrids = Dao::queryValues($sql, $bind);
            foreach ($arrids as $id) {
                $a = $entity_name::getById($id);
                if ($a instanceof $entity_name) {
                    $a->remove();
                }
            }
        }
        echo 'ok';
        return self::BLANK;
    }

    // 变换市场负责人页面
    public function doOneForMoveAuditorMarket() {
        $auditorid_market = XRequest::getValue("auditorid_market", 0);

        $cond = " and status=1 ";
        $bind = [];

        if ($auditorid_market) {
            $cond .= " and id=:id ";
            $bind[':id'] = $auditorid_market;
        }
        $auditors = Dao::getEntityListByCond("Auditor", $cond, $bind);

        XContext::setValue("auditorid_market", $auditorid_market);

        XContext::setValue("auditors", $auditors);

        return self::SUCCESS;
    }

    // 变更市场负责人接口
    public function doMoveAuditorMarketJson() {
        $from_auditorid_market = XRequest::getValue("from_auditorid_market", 0);
        $to_auditorid_market = XRequest::getValue("to_auditorid_market", 0);
        $xProvinceId = XRequest::getValue('xprovinceid', 0);
        $xCityId = XRequest::getValue('xcityid', 0);
        $xprovinceStatus = XRequest::getValue('xprovinceStatus', 1);
        $xCityStatus = XRequest::getValue('xcityStatus', 1);

        if ($to_auditorid_market) {

            if (!$from_auditorid_market) {
                echo "default";
                return self::BLANK;
            }

            $sql = "select a.*
                      from doctors a
                      inner join hospitals b on b.id=a.hospitalid
                      where 1=1";

            $cond = " and a.status=1 ";
            $bind = [];

            $cond .= " and a.auditorid_market=:auditorid_market ";
            $bind[':auditorid_market'] = $from_auditorid_market;


            if (0 != $xProvinceId) {
                if ($xprovinceStatus == 0) {
                    $cond .= ' and a.hospitalid in (select id from hospitals
                           where xprovinceid <> :xprovinceid) ';
                } else {
                    $cond .= ' and a.hospitalid in (select id from hospitals
                           where xprovinceid = :xprovinceid) ';
                }
                $bind[':xprovinceid'] = $xProvinceId;
            }

            if (0 != $xCityId) {
                if ($xCityStatus == 0) {
                    $cond .= ' and a.hospitalid in (select id from hospitals
                           where xcityid <> :xcityid) ';
                } else {
                    $cond .= ' and a.hospitalid in (select id from hospitals
                           where xcityid = :xcityid) ';
                }

                $bind[':xcityid'] = $xCityId;
            }

            $sql .= $cond;
            $doctors = Dao::loadEntityList("Doctor", $sql, $bind);

            if (count($doctors) > 0) {
                foreach ($doctors as $doctor) {

                    $doctor->auditorid_market = $to_auditorid_market;
                }

                // 变更成功
                echo "ok";
                return self::BLANK;
            } else {

                // 没有查询到要变更的医生
                echo "notChange";
                return self::BLANK;
            }
        } else {

            // 没有收到要变更成的auditorid_market
            echo "notToMarketId";
            return self::BLANK;
        }
    }

    // 肿瘤绩效统计
    public function doCancerList() {
        $date_range = XRequest::getValue('date_range', '');
        $auditorid = XRequest::getValue('auditorid', 0);

        $first_auditor = XRequest::getValue('first_auditor', 0);
        $not_first_auditor = XRequest::getValue('not_first_auditor', 0);
        $not_auditor = XRequest::getValue('not_auditor', 0);

        $tab_select = XRequest::getValue('tab_select', '');

        $condPatient = "";
        $condDoctor = "";
        if (!empty($date_range)) {
            $arr = explode('至', $date_range);
            $from_date = $arr[0];
            $to_date = $arr[1];
            $to_date = $to_date . ' 23:59:59';
        } else {
            $to_date = date('Y-m-d') . ' 23:59:59';
            $from_date = date('Y-m-d', strtotime("-1 months", strtotime($to_date)));
        }

        $condPatient .= " AND f.createtime BETWEEN '{$from_date}' AND '{$to_date}' ";
        $condDoctor .= " AND b.createtime BETWEEN '{$from_date}' AND '{$to_date}' ";
        $condShoporder = " AND g.time_pay BETWEEN '{$from_date}' AND '{$to_date}' ";

        if ($auditorid) {
            // 新增患者
            $sql = "select a.name as '市场', e.name as '省份', d.name as '医院', b.name as '医生', count(distinct f.id) as cnt
                    from auditors a
                      inner join doctors b on b.auditorid_market = a.id
                      inner join doctordiseaserefs c on c.doctorid = b.id
                      inner join hospitals d on d.id = b.hospitalid
                      inner join xprovinces e on e.id = d.xprovinceid
                      inner join patients f on f.doctorid = b.id
                    where c.diseaseid in (8, 15, 19, 21) and a.id = {$auditorid} {$condPatient}
                    group by a.name, e.name, d.name, b.name
                    order by a.id, cnt desc";
            $patientcnts = Dao::queryRows($sql);

            // 新增医生
            $sql = "select a.name as '市场', e.name as '省份', d.name as '医院', b.name as '医生'
                    from auditors a
                      inner join doctors b on b.auditorid_market = a.id
                      inner join doctordiseaserefs c on c.doctorid = b.id
                      inner join hospitals d on d.id = b.hospitalid
                      inner join xprovinces e on e.id = d.xprovinceid
                    where c.diseaseid in (8, 15, 19, 21) and a.id = {$auditorid} {$condDoctor}
                    group by a.name, e.name, d.name, b.name ";
            $doctorcnts = Dao::queryRows($sql);

            // 订单
            $sql = "select DISTINCT a.name as '市场', e.name as '省份', d.name as '医院', b.name as '医生', f.name as '患者', if(g.pos = 1, '是', '否') as '是否首单', concat(g.id, '|', g.is_lead_by_auditor) as '运营转化', if(g.refund_amount > 0, if(g.amount - g.refund_amount > 0, '部分退款', '全部退款'), '已支付') as '订单状态', concat('￥', TRUNCATE((g.amount - g.refund_amount) / 100, 2)) as '实付金额(含运费)', concat('￥', TRUNCATE((g.amount - g.express_price) / 100, 2)) as '订单金额(不含运费)'
                    from auditors a
                      inner join doctors b on b.auditorid_market = a.id
                      inner join doctordiseaserefs c on c.doctorid = b.id
                      inner join hospitals d on d.id = b.hospitalid
                      inner join xprovinces e on e.id = d.xprovinceid
                      inner join patients f on f.doctorid = b.id
                      inner join shoporders g on g.patientid = f.id
                    where c.diseaseid in (8, 15, 19, 21) and g.is_pay = 1 and a.id = {$auditorid} {$condShoporder}
                    order by a.id, g.amount desc ";
            $shopordercnts = Dao::queryRows($sql);
        } else {
            $sql = "select a.name as '市场', count(distinct f.id) as cnt
                    from auditors a
                      inner join doctors b on b.auditorid_market = a.id
                      inner join doctordiseaserefs c on c.doctorid = b.id
                      inner join hospitals d on d.id = b.hospitalid
                      inner join xprovinces e on e.id = d.xprovinceid
                      inner join patients f on f.doctorid = b.id
                    where c.diseaseid in (8, 15, 19, 21) {$condPatient}
                    group by a.name
                    order by cnt desc ";
            $patientcnts = Dao::queryRows($sql);

            // 新增医生
            $sql = "select a.name as '市场', count(distinct b.id) as cnt
                    from auditors a
                      inner join doctors b on b.auditorid_market = a.id
                      inner join doctordiseaserefs c on c.doctorid = b.id
                      inner join hospitals d on d.id = b.hospitalid
                      inner join xprovinces e on e.id = d.xprovinceid
                    where c.diseaseid in (8, 15, 19, 21) {$condDoctor}
                    group by a.name
                    order by cnt desc ";
            $doctorcnts = Dao::queryRows($sql);

            // 订单
            $sql = "select a.name as '市场', count(distinct g.id) as cnt
                    from auditors a
                      inner join doctors b on b.auditorid_market = a.id
                      inner join doctordiseaserefs c on c.doctorid = b.id
                      inner join hospitals d on d.id = b.hospitalid
                      inner join xprovinces e on e.id = d.xprovinceid
                      inner join patients f on f.doctorid = b.id
                      inner join shoporders g on g.patientid = f.id
                    where c.diseaseid in (8, 15, 19, 21) and g.is_pay = 1 {$condShoporder}
                    group by a.name
                    order by cnt desc ";
            $shopordercnts = Dao::queryRows($sql);
        }

        XContext::setValue('auditorid', $auditorid);
        XContext::setValue('patientcnts', $patientcnts);
        XContext::setValue('first_auditor', $first_auditor);
        XContext::setValue('not_first_auditor', $not_first_auditor);
        XContext::setValue('not_auditor', $not_auditor);
        XContext::setValue('tab_select', $tab_select);
        XContext::setValue('doctorcnts', $doctorcnts);
        XContext::setValue('shopordercnts', $shopordercnts);
        XContext::setValue('date_range', $date_range);

        return self::SUCCESS;
    }
}
