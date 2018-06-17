<?php
// 微信用户管理
class WxUserMgrAction extends AuditBaseAction
{

    // 首页
    public function doDefault() {
        return self::SUCCESS;
    }

    public function doListForPipe() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);
        $fromdate = XRequest::getValue('fromdate', '');
        $todate = XRequest::getValue('todate', '');
        //微信名 或 wxuserid
        $keyword = XRequest::getValue('keyword', '');
        $doctor_name = XRequest::getValue('doctor_name', '');
        $type = XRequest::getValue('type', 'notbaodao');
        $is_sendtxtmsg = XRequest::getValue('is_sendtxtmsg', 'all');

        $cond = '';
        $bind = [];

        //按关注时间
        if ($fromdate) {
            $cond .= " and a.createtime >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate) {
            $cond .= " and a.createtime < :todate ";
            $bind[':todate'] = date("Y-m-d", strtotime($todate) + 86400);
        }

        //按微信号wxuserid
        if($keyword){
            $cond .= ' and (a.nickname like :nickname or a.id like :wxuserid) ';
            $bind[':nickname'] = "%{$keyword}%";
            $bind[':wxuserid'] = "%{$keyword}%";
        }

        //按医生姓名
        if($doctor_name){
            $cond .= ' and d.name like :doctor_name ';
            $bind[':doctor_name'] = "%{$doctor_name}%";
        }

        //按是否报到
        if($type == 'notbaodao'){
            $cond .= ' and b.patientid=0 ';
        }else {
            $cond .= ' and b.patientid!=0 ';
        }

        //按是否发送文本消息
        //未发送
        if($is_sendtxtmsg == 2){
            $cond .= " and t.id is null ";
        }
        //发送过
        if($is_sendtxtmsg == 1){
            $cond .= " and t.id > 0 ";
        }

        //疾病
        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and c.diseaseid in ($diseaseidstr) ";

        $cond .= " AND a.subscribe = 1 order by a.lastpipe_createtime desc, a.id desc ";

        $sql = "select a.* from wxusers a
                inner join users b on a.userid=b.id
                inner join wxshops c on c.id=a.wxshopid
                left join doctors d on d.id=a.doctorid
                left join (select id,wxuserid from wxtxtmsgs group by wxuserid)t on t.wxuserid = a.id
                where 1=1 {$cond}";

        $wxusers = Dao::loadEntityList4Page("WxUser", $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $countSql = "select count(a.id) from wxusers a
            inner join users b on a.userid=b.id
            inner join wxshops c on c.id=a.wxshopid
            left join doctors d on d.id=a.doctorid
            left join (select id,wxuserid from wxtxtmsgs group by wxuserid)t on t.wxuserid = a.id
            where 1=1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/wxusermgr/listforpipe?keyword={$keyword}&doctor_name={$doctor_name}&type={$type}&is_sendtxtmsg={$is_sendtxtmsg}&fromdate={$fromdate}&todate={$todate}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('wxusers', $wxusers);

        XContext::setValue('fromdate', $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue('keyword', $keyword);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('type', $type);
        XContext::setValue('is_sendtxtmsg', $is_sendtxtmsg);

        return self::SUCCESS;
    }

    // wxuser列表
    public function doList() {
        $nickname = XRequest::getValue('nickname', '');

        $hospitalid = XRequest::getValue("hospitalid", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);
        $auditorid_expand = XRequest::getValue("auditorid_expand", 0);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $fromdate = XRequest::getValue('fromdate', '0000-00-00');
        $todate = XRequest::getValue('todate', '0000-00-00');
        $woy = XRequest::getValue("woy", 0);

        if ($woy > 0) {
            $fromdate = XDateTime::getDateYmdByWoy($woy);
            $todate = XDateTime::getDateYmdByWoy($woy + 1);
        }

        // 服务号
        $wxshopid = XRequest::getValue('wxshopid', 0);

        // 订阅
        $subscribe = XRequest::getValue('subscribe', 'all');

        // 医生类别
        $doctortype = XRequest::getValue('doctortype', 'valid');

        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $hospitals = Dao::getEntityListByCond('Hospital', " order by id asc limit 500 ");

        $cond = "";
        $bind = [];

        $url = "/wxusermgr/list?1=1";

        $url .= "&subscribe={$subscribe}";
        if ($subscribe == "yes") {
            $cond .= " and subscribe = 1 ";
        } elseif ($subscribe == "no") {
            $cond .= " and subscribe = 0 ";
        }

        XContext::setValue('hospitalid', $hospitalid);
        XContext::setValue('auditorid_market', $auditorid_market);
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('wxshopid', $wxshopid);
        XContext::setValue('subscribe', $subscribe);
        XContext::setValue('doctortype', $doctortype);

        // 筛选医生
        if ($doctorid) {
            $url .= "&doctorid={$doctorid}";
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;

            $hospitalid = 0;
            $auditorid_market = 0;
            $doctortype = 'all';
            XContext::setValue('doctortype', $doctortype);

            $doctor = Doctor::getById($doctorid);

            // 修正回显
            XContext::setValue('hospitalid', $doctor->hospitalid);
            XContext::setValue('auditorid_market', $doctor->auditorid_market);
        }

        // 疾病筛选 20170419 by sjp
        // wxshops.diseaseid 被 doctordiseaserefs + doctorwxshoprefs 代替
        // $cond .= " and wxshopid in ( select id from wxshops where
        // diseaseid = :diseaseid )";

        $diseaseidstr = $this->getContextDiseaseidStr();

        $sql = " select distinct a.id
                    from wxshops a
                    inner join doctorwxshoprefs b on b.wxshopid=a.id
                    inner join doctordiseaserefs c on c.doctorid=b.doctorid
                    inner join doctors d on d.id=b.doctorid
                    where d.hospitalid <> 5 and c.diseaseid in ($diseaseidstr) ";

        $wxshopids = Dao::queryValues($sql);

        $wxshopids_str = implode(',', $wxshopids);

        $cond .= " and wxshopid in ({$wxshopids_str}) ";

        // 筛选医院
        if ($hospitalid) {
            $url .= "&hospitalid={$hospitalid}";
            $cond .= " and doctorid in ( select id from doctors where hospitalid = :hospitalid )";
            $bind[':hospitalid'] = $hospitalid;
        }

        // 筛选市场负责人
        if ($auditorid_market) {
            $url .= "&auditorid_market={$auditorid_market}";
            $cond .= " and doctorid in ( select id from doctors where auditorid_market = :auditorid_market )";
            $bind[':auditorid_market'] = $auditorid_market;
        }

        // 筛选市场推广人
        if ($auditorid_expand) {
            $url .= "&auditorid_expand={$auditorid_expand}";
            $cond .= " and ref_objtype = 'Auditor' and ref_objid = :auditorid_expand ";
            $bind[':auditorid_expand'] = $auditorid_expand;

            XContext::setValue('auditorid_expand', $auditorid_expand);
        }

        // 开始时间
        if ($fromdate != '0000-00-00') {
            $url .= "&fromdate={$fromdate}";
            $cond .= " and left(createtime,10) >= :fromdate ";

            $bind[':fromdate'] = $fromdate;
            XContext::setValue('fromdate', $fromdate);
        }

        // 截至时间
        if ($todate != '0000-00-00') {
            $url .= "&todate={$todate}";
            $cond .= " and left(createtime,10) < :todate ";

            $bind[':todate'] = $todate;
            XContext::setValue('todate', $todate);
        }

        $ignoreDoctorIdStr = Doctor::getTestDoctorIdStr();

        if ($wxshopid) {
            $url .= "&wxshopid={$wxshopid}";
            $cond .= " and wxshopid = :wxshopid ";
            $bind[':wxshopid'] = $wxshopid;
        }

        // 医生类别
        $url .= "&doctortype={$doctortype}";
        switch ($doctortype) {
            case 'valid':
                $cond .= " and ( doctorid not in ({$ignoreDoctorIdStr}) and doctorid > 0 and doctorid < 10000 ) ";
                break;
            case 'invalid':
                $cond .= " and ( doctorid in ({$ignoreDoctorIdStr}) or doctorid > 10000 ) ";
                break;
            case 'null':
                $cond .= " and doctorid=0 ";
                break;
            default:
                break;
        }

        if ($nickname != '') {
            $url .= "&nickname={$nickname}";
            $cond .= " and nickname like :nickname ";
            $bind[':nickname'] = "%{$nickname}%";

            XContext::setValue('nickname', $nickname);
        }

        $cond .= ' and wxshopid != 3 order by id desc ';

        $wxusers = Dao::getEntityListByCond4Page("WxUser", $pagesize, $pagenum, $cond, $bind);

        // 翻页begin
        $countSql = "select count(*) as cnt from wxusers where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('hospitals', $hospitals);
        XContext::setValue("wxusers", $wxusers);

        $myauditor = $this->myauditor;
        if ($myauditor->isOnlyOneRole('market')) {
            return 'market';
        }

        return self::SUCCESS;
    }

    // 退订列表
    public function doUnsubscribeList() {
        $pagesize = XRequest::getValue("pagesize", 50);
        $pagenum = XRequest::getValue("pagenum", 1);
        $sql = "select * from wxusers where subscribe = 0 and wx_ref_code !='' order by unsubscribe_time desc";

        $resultArr = $this->getResultArr();

        $wxusers = Dao::loadEntityList4Page("WxUser", $sql, $pagesize, $pagenum, []);
        XContext::setValue("wxusers", $wxusers);
        XContext::setValue("resultArr", $resultArr);

        // 翻页begin
        $countSql = "select count(*)
                from wxusers
                where subscribe = 0 and wx_ref_code !=''
                order by unsubscribe_time desc";
        $cnt = Dao::queryValue($countSql, []);
        $url = "/wxusermgr/unsubscribelist";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    private function getResultArr() {
        $baodaocnt = 0;
        $notbaodaocnt = 0;
        $num1 = 0;
        $num2 = 0;
        $num3 = 0;

        $sqlbaodaocnt = " select count(1)
            from patients a
            inner join users b on b.patientid = a.id
            inner join wxusers c on c.userid = b.id
            where c.subscribe = 0 and c.wx_ref_code !='' ";
        $baodaocnt = Dao::queryValue($sqlbaodaocnt, []);

        $sqlnotbaodaocnt = " select count(1)
            from wxusers a
            left join users b on b.id = a.userid
            left join patients c on c.id = b.patientid
            where a.subscribe = 0 and a.wx_ref_code !='' and c.id is NULL ";
        $notbaodaocnt = Dao::queryValue($sqlnotbaodaocnt, []);

        $sqlnum1 = " select count(1)
            from patients a
            inner join users b on b.patientid = a.id
            inner join wxusers c on c.userid = b.id
            where c.subscribe = 0 and c.wx_ref_code !=''
                and UNIX_TIMESTAMP(c.unsubscribe_time) - UNIX_TIMESTAMP(a.createtime) < 86400 ";
        $num1 = Dao::queryValue($sqlnum1, []);

        $sqlnum2 = " select count(*)
            from patients a
            inner join users b on b.patientid = a.id
            inner join wxusers c on c.userid = b.id
            where c.subscribe = 0 and c.wx_ref_code !=''
                and UNIX_TIMESTAMP(c.unsubscribe_time) - UNIX_TIMESTAMP(a.createtime) > 86400
                and UNIX_TIMESTAMP(c.unsubscribe_time) - UNIX_TIMESTAMP(a.createtime) <= 86400*7; ";
        $num2 = Dao::queryValue($sqlnum2, []);

        $sqlnum3 = " select count(*)
            from patients a
            inner join users b on b.patientid = a.id
            inner join wxusers c on c.userid = b.id
            where c.subscribe = 0 and c.wx_ref_code !=''
                and UNIX_TIMESTAMP(c.unsubscribe_time) - UNIX_TIMESTAMP(a.createtime) > 86400 * 7; ";
        $num3 = Dao::queryValue($sqlnum3, []);

        return array(
            'baodaocnt' => $baodaocnt,
            "notbaodaocnt" => $notbaodaocnt,
            "num1" => $num1,
            "num2" => $num2,
            "num3" => $num3);
    }

    // 删除微信
    public function doDeleteWxUser() {
        $wxusers = Dao::getEntityListByCond('WxUser', "and nickname in ('行为训练管理员','宫瑜','老史','子方','chenx_en2','cphappy')");

        XContext::setValue('wxusers', $wxusers);

        return self::SUCCESS;
    }

    // 删除微信提交
    public function doDeleteWxUserPost() {
        $nickname = XRequest::getValue('nickname', '老史');

        $cond = "and nickname=:nickname ";
        $bind = [];
        $bind[':nickname'] = $nickname;

        $wxusers = Dao::getEntityListByCond('WxUser', $cond, $bind);

        $str = "删除微信号[ {$nickname} ]的关注 ";
        foreach ($wxusers as $a) {
            $str .= " {$a->id} ";
            $a->remove();

            if ($a->user instanceof User) {
                $a->user->remove();
            }
        }
        XContext::setJumpPath("/wxusermgr/deleteWxUser?preMsg=" . urlencode($str));
        return self::SUCCESS;
    }

    // 取消报到提交
    public function doResetPatientIdPost() {
        $nickname = XRequest::getValue('nickname', '老史');

        $cond = "and nickname=:nickname ";
        $bind = [];
        $bind[':nickname'] = $nickname;

        $wxusers = Dao::getEntityListByCond('WxUser', $cond, $bind);

        $str = "已取消微信号[ {$nickname} ]的患者报到: ";
        foreach ($wxusers as $a) {
            $str .= " {$a->id} ";

            if ($a->user instanceof User && $a->user->patient instanceof Patient) {
                $a->user->patient->set4lock('doctorid', 0);

                // 运营下线
                PatientStatusService::auditor_offline($a->user->patient, $this->myauditor, "取消报到提交");

                // 修正 user->patientid
                $a->user->fixPatientId(0);
            }
            $a->wx_ref_code = '';
        }
        XContext::setJumpPath("/wxusermgr/deleteWxUser?preMsg=" . urlencode($str));
        return self::SUCCESS;
    }
}
