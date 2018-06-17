<?php
require ROOT_TOP_PATH . '/domain/third.party/QueryList/phpQuery.php';
require ROOT_TOP_PATH . '/domain/third.party/QueryList/QueryList.php';

use QL\QueryList;

class DoctorMgrAction extends AuditBaseAction
{

    public function doDefault() {
        return self::SUCCESS;
    }

    // 组合筛选列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $hospitalid = XRequest::getValue("hospitalid", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);
        $auditorid_yunying = XRequest::getValue("auditorid_yunying", 0);
        $doctorgroupid = XRequest::getValue('doctorgroupid', 0);
        $doctor_name = XRequest::getValue("doctor_name", "");
        $xprovinceid = XRequest::getValue("xprovinceid", 0);
        $xcityid = XRequest::getValue("xcityid", 0);
        $status = XRequest::getValue("status", -1);

        $myauditor = $this->myauditor;
        if ($myauditor->id != 10020 && $myauditor->isOnlyOneRole('market')) {
            $auditorid_market = $myauditor->id;
        }
        $cond = " ";
        $hospitalcond = "";
        $bind = [];

        // 过滤疾病
        $diseaseidstr = $this->getContextDiseaseidStr();
        $hospitalcond .= " and id in (
            select hospitalid
            from doctors
            where id in (
                select doctorid
                from doctordiseaserefs
                where diseaseid in ($diseaseidstr)
            )
        ) order by id asc ";

        $hospitals = Dao::getEntityListByCond("Hospital", $hospitalcond, $bind);

        $sql = "select a.*
        from doctors a
        inner join hospitals b on b.id=a.hospitalid
        where 1=1";

        $cond .= " and a.id in (
            select doctorid
            from doctordiseaserefs
            where diseaseid in ($diseaseidstr)
            )";

        // 过滤医院,医院高于疾病
        if ($hospitalid > 0) {
            $cond = " and a.hospitalid=:hospitalid  ";
            $bind = [];
            $bind[':hospitalid'] = $hospitalid;
        }

        // 过滤市场负责人,高于医院
        if ($auditorid_market > 0) {
            $cond = " and a.auditorid_market=:auditorid_market ";
            $bind = [];
            $bind[':auditorid_market'] = $auditorid_market;
        }

        // 过滤运营负责人,高于医院
        if ($auditorid_yunying > 0) {
            $cond = " and a.auditorid_yunying=:auditorid_yunying ";
            $bind = [];
            $bind[':auditorid_yunying'] = $auditorid_yunying;
        }

        // 过滤医生,医生高于医院疾病
        if ($doctor_name) {
            $hospitalid = 0;
            $cond = ' and a.name like :doctor_name ';
            $bind = [];
            $bind[':doctor_name'] = "%{$doctor_name}%";
        }

        if ($status >= 0) {
            $cond = ' and a.status= :status ';
            $bind = [];
            $bind[':status'] = $status;
        }

        if ($doctorgroupid) {
            $cond .= ' and a.doctorgroupid = :doctorgroupid ';
            $bind[':doctorgroupid'] = $doctorgroupid;
        }

        if (0 != $xprovinceid) {
            $cond .= ' and hospitalid in (select id from hospitals
            where xprovinceid = :xprovinceid) ';
            $bind[':xprovinceid'] = $xprovinceid;
        }

        if (0 != $xcityid) {
            $cond .= ' and hospitalid in (select id from hospitals
            where xcityid = :xcityid) ';
            $bind[':xcityid'] = $xcityid;
        }

        $cond .= " order by a.id ";

        $sql .= $cond;

        $doctors = Dao::loadEntityList4Page("Doctor", $sql, $pagesize, $pagenum, $bind);

        $countSql = "select count(a.id) as cnt from doctors a inner join hospitals b on b.id=a.hospitalid where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctormgr/list?hospitalid={$hospitalid}&auditorid_market={$auditorid_market}&doctor_name={$doctor_name}&xprovinceid={$xprovinceid}&xcityid={$xcityid}&status={$status}&doctorgroupid={$doctorgroupid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("hospitalid", $hospitalid);
        XContext::setValue("auditorid_yunying", $auditorid_yunying);
        XContext::setValue("doctorgroupid", $doctorgroupid);
        XContext::setValue("auditorid_market", $auditorid_market);
        XContext::setValue("doctor_name", $doctor_name);
        XContext::setValue("xprovinceid", $xprovinceid);
        XContext::setValue("xcityid", $xcityid);
        XContext::setValue("status", $status);

        XContext::setValue("hospitals", $hospitals);
        XContext::setValue("doctors", $doctors);

        XContext::setValue("pagelink", $pagelink);

        $myauditor = $this->myauditor;
        if ($myauditor->isOnlyOneRole('market')) {
            return 'market';
        }
        return self::SUCCESS;
    }

    // 按月汇总列表
    public function doListMonth() {
        $pagesize = XRequest::getValue("pagesize", 100);
        $pagenum = XRequest::getValue("pagenum", 1);

        $hospitalid = XRequest::getValue("hospitalid", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);
        $auditorgroupid = XRequest::getValue("auditorgroupid", 0);
        $menzhen_offset_daycnt = XRequest::getValue("menzhen_offset_daycnt", -1);

        $isshowall = XRequest::getValue("isshowall", 0);
        XContext::setValue('isshowall', $isshowall);

        $hospitals = Dao::getEntityListByCond("Hospital", " order by id asc ");

        $cond = " ";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond = " and id in ( select doctorid from doctordiseaserefs where diseaseid in ($diseaseidstr)) ";

        if ($hospitalid > 0) {
            $cond .= " and hospitalid=:hospitalid  ";
            $bind[':hospitalid'] = $hospitalid;
        }

        if ($auditorid_market > 0) {
            $cond .= " and auditorid_market=:auditorid_market ";
            $bind[':auditorid_market'] = $auditorid_market;
        }

        if ($auditorgroupid > 0) {
            $auditorids = AuditorGroupRefDao::getAuditorIdsByAuditorGroupId($auditorgroupid);
            $auditoridsstr = implode(",", $auditorids);
            $cond .= " and auditorid_market in ( {$auditoridsstr} ) ";
        }

        if ($menzhen_offset_daycnt > -1) {
            $cond .= " and menzhen_offset_daycnt=:menzhen_offset_daycnt ";
            $bind[':menzhen_offset_daycnt'] = $menzhen_offset_daycnt;
        }

        $ids = Doctor::getTestDoctorIdStr();
        $cond .= " and id not in ({$ids}) and id < 10000 order by id ";

        $doctors = Dao::getEntityListByCond4Page("Doctor", $pagesize, $pagenum, $cond, $bind);

        // 获取当前月和前2个月日期(如2016-04,2016-03....)
        $months = DoctorDao::getRptMonths();

        // 获取处理之后的报到数和扫码数数组

        // 有效患者
        $_patientRptGroupbyDoctorMonth = PatientDao::getRptGroupbyDoctorMonth(" and a.status = 1 and a.subscribe_cnt > 0 ");
        $patientRptGroupbyDoctorMonth = $this->dealPrefMonthArr($_patientRptGroupbyDoctorMonth);

        // 市场绩效患者
        $_patientRptGroupbyDoctorMonth_market = PatientDao::getRptGroupbyDoctorMonthForMarket();
        $patientRptGroupbyDoctorMonth_market = $this->dealPrefMonthArr($_patientRptGroupbyDoctorMonth_market);

        // 全部患者,包括,删除和审核中
        $_patientRptGroupbyDoctorMonth_all = PatientDao::getRptGroupbyDoctorMonth();
        $patientRptGroupbyDoctorMonth_all = $this->dealPrefMonthArr($_patientRptGroupbyDoctorMonth_all);

        // 扫码用户
        $_wxuserRptGroupbyDoctorMonth = WxUserDao::getRptGroupbyDoctorMonth();
        $wxuserRptGroupbyDoctorMonth = $this->dealPrefMonthArr($_wxuserRptGroupbyDoctorMonth);

        // 获取6月的报到数和扫码数总计
        $monthPatientCnts = DoctorDao::getMonthPrefs($patientRptGroupbyDoctorMonth, $doctors);
        $monthPatientMarketCnts = DoctorDao::getMonthPrefs($patientRptGroupbyDoctorMonth_market, $doctors);
        $monthPatientAllCnts = DoctorDao::getMonthPrefs($patientRptGroupbyDoctorMonth_all, $doctors);
        $monthWxUserCnts = DoctorDao::getMonthPrefs($wxuserRptGroupbyDoctorMonth, $doctors);

        $countSql = "select count(*) as cnt from doctors where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctormgr/listmonth?hospitalid={$hospitalid}&auditorid_market={$auditorid_market}&auditorgroupid={$auditorgroupid}&isshowall={$isshowall}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("hospitalid", $hospitalid);
        XContext::setValue("auditorid_market", $auditorid_market);
        XContext::setValue("auditorgroupid", $auditorgroupid);
        XContext::setValue("menzhen_offset_daycnt", $menzhen_offset_daycnt);

        XContext::setValue("hospitals", $hospitals);
        XContext::setValue('months', $months);

        XContext::setValue('patientRptGroupbyDoctorMonth', $patientRptGroupbyDoctorMonth);
        XContext::setValue('patientRptGroupbyDoctorMonth_market', $patientRptGroupbyDoctorMonth_market);
        XContext::setValue('patientRptGroupbyDoctorMonth_all', $patientRptGroupbyDoctorMonth_all);
        XContext::setValue('wxuserRptGroupbyDoctorMonth', $wxuserRptGroupbyDoctorMonth);

        XContext::setValue('monthPatientCnts', $monthPatientCnts);
        XContext::setValue('monthPatientMarketCnts', $monthPatientMarketCnts);
        XContext::setValue('monthPatientAllCnts', $monthPatientAllCnts);
        XContext::setValue('monthWxUserCnts', $monthWxUserCnts);

        XContext::setValue("doctors", $doctors);

        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 按周汇总列表
    public function doListWoy() {
        $hospitalid = XRequest::getValue("hospitalid", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);

        $pagesize = XRequest::getValue("pagesize", 1000);
        $pagenum = XRequest::getValue("pagenum", 1);

        $hospitals = Dao::getEntityListByCond("Hospital", " order by id asc ");

        $cond = " ";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond = " and id in ( select doctorid from doctordiseaserefs where diseaseid in ($diseaseidstr) ) ";

        if ($hospitalid > 0) {
            $cond = " and hospitalid=:hospitalid  ";
            $bind = [];
            $bind[':hospitalid'] = $hospitalid;
        }

        if ($auditorid_market > 0) {
            $cond = " and auditorid_market=:auditorid_market ";
            $bind = [];
            $bind[':auditorid_market'] = $auditorid_market;
        }

        $ids = Doctor::getTestDoctorIdStr();
        $cond .= " and id not in ({$ids}) and id < 10000 order by id ";

        $doctors = Dao::getEntityListByCond4Page("Doctor", $pagesize, $pagenum, $cond, $bind);

        // 获取所有的woys
        $woys = WxUserDao::getAllWoy();

        // 有效患者,获取处理之后的报到数和扫码数数组
        $_patientRptGroupbyDoctorWoy = PatientDao::getRptGroupbyDoctorWoy(false);
        $patientRptGroupbyDoctorWoy = DoctorDao::getDealPrefWoy($_patientRptGroupbyDoctorWoy);

        // 全部患者,包括删除和审核中
        $_patientRptGroupbyDoctorWoy_all = PatientDao::getRptGroupbyDoctorWoy(true);
        $patientRptGroupbyDoctorWoy_all = DoctorDao::getDealPrefWoy($_patientRptGroupbyDoctorWoy_all);

        $_wxuserRptGroupbyDoctorWoy = WxUserDao::getRptGroupbyDoctorWoy();
        $wxuserRptGroupbyDoctorWoy = DoctorDao::getDealPrefWoy($_wxuserRptGroupbyDoctorWoy);

        // 获取每一个woy的报到数和扫码数总计
        $woyPatientCnts = DoctorDao::getWoyPrefs($patientRptGroupbyDoctorWoy, $doctors);
        $woyPatientAllCnts = DoctorDao::getWoyPrefs($patientRptGroupbyDoctorWoy_all, $doctors);
        $woyWxUserCnts = DoctorDao::getWoyPrefs($wxuserRptGroupbyDoctorWoy, $doctors);

        $countSql = "select count(*) as cnt from doctors where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctormgr/listwoy?hospitalid={$hospitalid}&auditorid_market={$auditorid_market}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("hospitalid", $hospitalid);
        XContext::setValue("auditorid_market", $auditorid_market);

        XContext::setValue("hospitals", $hospitals);
        XContext::setValue("doctors", $doctors);

        XContext::setValue('woys', $woys);

        XContext::setValue('patientRptGroupbyDoctorWoy', $patientRptGroupbyDoctorWoy);
        XContext::setValue('patientRptGroupbyDoctorWoy_all', $patientRptGroupbyDoctorWoy_all);
        XContext::setValue('wxuserRptGroupbyDoctorWoy', $wxuserRptGroupbyDoctorWoy);

        XContext::setValue('woyPatientCnts', $woyPatientCnts);
        XContext::setValue('woyPatientAllCnts', $woyPatientAllCnts);
        XContext::setValue('woyWxUserCnts', $woyWxUserCnts);

        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 新建的显示
    public function doAdd() {
        $hospitalid = XRequest::getValue("hospitalid", 0);
        XContext::setValue("hospitalid", $hospitalid);

        return self::SUCCESS;
    }

    // 新建提交
    public function doAddPost() {
        $username = XRequest::getValue("username", '');
        $mobile = XRequest::getValue("mobile", '');

        if (11 != strlen($mobile)) {
            $preMsg = "请输入正确的11位手机号！";
            XContext::setJumpPath("/doctormgr/add?preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        $username = strtolower($username);

        $regex = '/[a-zA-Z0-9]+/';
        if (false == preg_match($regex, $username, $match)) {
            $preMsg = "登录名不能为空或包含汉字";
            XContext::setJumpPath("/doctormgr/add?preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        $name = XRequest::getValue("name", '');
        $gender = XRequest::getValue("gender", 0);//1男2女0未知
        $title = XRequest::getValue("title", '');
        $auditorid_yunying = XRequest::getValue("auditorid_yunying", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);
        //$auditorid_createby = XRequest::getValue("auditorid_createby", 0);
        $diseaseids = XRequest::getValue("diseaseids", []);
        DBC::requireNotEmpty($diseaseids, "diseaseids为空");
        $service_remark = XRequest::getValue("service_remark", '');
        $hospitalid = XRequest::getValue("hospitalid", 0);
        $department = XRequest::getValue("department", '');
        $pdoctorid = XRequest::getValue("pdoctorid", 0);
        $status = XRequest::getValue("status", 0);

        $menzhen_offset_daycnt = XRequest::getValue("menzhen_offset_daycnt", '');
        $menzhen_pass_date = XRequest::getValue("menzhen_pass_date", '');
        $is_audit_chufang = XRequest::getValue("is_audit_chufang", 0);
        $is_sign = XRequest::getValue("is_sign", 0);

        $superior_doctorids = XRequest::getValue('superior_doctorids', []);

        $user = UserDao::getByUserName($username);
        if ($user instanceof User) {
            $preMsg = "{$username}已存在,建议修改登录名为 地名+医生拼音 例如：bjyangli";
            XContext::setJumpPath("/doctormgr/add?preMsg=" . urlencode($preMsg));

            return self::SUCCESS;
        }

        $doctor = DoctorDao::getByCode($username);
        if ($doctor instanceof Doctor) {
            $preMsg = "医生code：{$username}已存在,建议修改登录名为 地名+医生拼音 例如：bjyangli ";
            XContext::setJumpPath("/doctormgr/add?preMsg=" . urlencode($preMsg));

            return self::SUCCESS;
        }

        $hospital = Hospital::getById($hospitalid);

        $row = array();
        $row["username"] = $username;
        $row["mobile"] = $mobile;
        $row["password"] = $username . rand(300, 999);
        $row["name"] = $name;
        $user = User::createByBiz($row);

        $row = array();
        $row["id"] = 1 + Dao::queryValue('select max(id) as maxid from doctors where id < 10000');
        $row["userid"] = $user->id;
        $row["name"] = $name;
        $row["sex"] = $gender;
        $row["mobile"] = $mobile;
        $row["auditorid_yunying"] = $auditorid_yunying;
        $row["auditorid_market"] = $auditorid_market;
        $row["auditorid_createby"] = $this->myauditor->id;

        $row["service_remark"] = $service_remark;
        $row["hospitalid"] = $hospitalid;
        $row["department"] = $department;
        $row["title"] = $title;

        $row["menzhen_offset_daycnt"] = $menzhen_offset_daycnt;
        $row["menzhen_pass_date"] = $menzhen_pass_date;
        $row["is_audit_chufang"] = $is_audit_chufang;

        //开通了续方审核
        if ($is_audit_chufang) {
            $row['audit_chufang_pass_time'] = date("Y-m-d H:i:s");
        }

        $row["is_sign"] = $is_sign;

        $row["code"] = $username;

        $row["pdoctorid"] = $pdoctorid;
        if ($pdoctorid > 0) {
            $row["patients_referencing"] = ",{$pdoctorid}";
        }
        $row["status"] = $status;

        $doctor = Doctor::createByBiz($row);

        if (in_array(1, $diseaseids)) {
            $wxshop = WxShopDao::getByDiseaseid(1);

            $row = [];

            $row['doctorid'] = $doctor->id;
            $row['wxshopid'] = $wxshop->id;
            $doctorwxshopref = DoctorWxShopRef::createByBiz($row);

            $doctorwxshopref->check_qr_ticket();
        }

        foreach ($diseaseids as $diseaseid) {
            $row = array();
            $row["doctorid"] = $doctor->id;
            $row["diseaseid"] = $diseaseid;
            $doctorDiseaseRef = DoctorDiseaseRef::createByBiz($row);
        }

        //写入医生主管表
        if ($superior_doctorids && is_array($superior_doctorids)) {
            //要插入的
            foreach ($superior_doctorids as $superior_doctorid) {
                $row = [];
                $row['doctorid'] = $doctor->id;
                $row['superior_doctorid'] = $superior_doctorid;
                $doctorSuperior = Doctor_Superior::createByBiz($row);
            }
        }

        XContext::setJumpPath("/doctorconfigmgr/overview?doctorid={$doctor->id}");

        return self::SUCCESS;
    }

    // 修改页的显示
    public function doModify() {
        $doctorid = XRequest::getValue("doctorid", 0);

        $doctor = Doctor::getById($doctorid);
        $diseaseids = $doctor->getDiseaseIdArray();
        $hospitals = Dao::getEntityListByCond("Hospital");
        $comments = CommentDao::getArrayOfDoctor($doctorid);

        $diseaseids = $doctor->getDiseaseIdArray();
        $diseaseidstr = implode(',', $diseaseids);

        $sql = "SELECT a.* FROM doctors a INNER JOIN doctordiseaserefs b
                ON a.id = b.doctorid
                WHERE a.id < 100000 AND b.diseaseid IN ({$diseaseidstr})
                GROUP BY a.id";

        $relatedDoctors = Dao::loadEntityList('Doctor', $sql);
        $relatedDoctorArr = [];
        foreach ($relatedDoctors as $one) {
            if ($one->id == $doctorid) {
                continue;
            }
            $relatedDoctorArr[$one->id] = $one->id . " " . $one->name . " " . $one->hospital->shortname;
        }

        XContext::setValue("relatedDoctorArr", $relatedDoctorArr);
        XContext::setValue("doctorDiseaseIds", $diseaseids);
        XContext::setValue("doctor", $doctor);
        XContext::setValue("hospitals", $hospitals);
        XContext::setValue("comments", $comments);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $name = XRequest::getValue("name", '');
        $gender = XRequest::getValue("gender", 0);
        $mobile = XRequest::getValue("mobile", '');
        $brief = XRequest::getValue("brief", '');
        $be_good_at = XRequest::getValue("be_good_at", '');
        $sourcestr = XRequest::getValue("sourcestr", '');
        $title = XRequest::getValue("title", '');
        $hospital_name = XRequest::getValue("hospital_name", '');
        $auditorid_yunying = XRequest::getValue("auditorid_yunying", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);
        $auditorid_createby = XRequest::getValue("auditorid_createby", 0);
        $pdoctorid = XRequest::getValue("pdoctorid", 0);
        $patients_referencing = XRequest::getValue("patients_referencing", '');
        $menzhen_offset_daycnt = XRequest::getValue("menzhen_offset_daycnt", '');
        $menzhen_pass_date = XRequest::getValue("menzhen_pass_date", '');
        $is_audit_chufang = XRequest::getValue("is_audit_chufang", 0);
        $is_sign = XRequest::getValue("is_sign", 0);
        $status = XRequest::getValue("status", 0);
        $module_pushmsg = XRequest::getValue("module_pushmsg", 0);
        $service_remark = XRequest::getValue("service_remark", '');
        $hospitalid = XRequest::getValue("hospitalid", 0);
        $department = XRequest::getValue("department", '');
        $diseaseids = XRequest::getValue("diseaseids", '');
        $superior_doctorids = XRequest::getValue('superior_doctorids', '');

        if (11 != strlen($mobile)) {
            $preMsg = "请输入正确的11位手机号！";
            XContext::setJumpPath("/doctormgr/modify?doctorid=" . $doctorid . "&preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        $doctor = Doctor::getById($doctorid);
        $doctor->name = $name;
        $doctor->sex = $gender;
        $doctor->mobile = $mobile;
        $doctor->brief = $brief;
        $doctor->be_good_at = $be_good_at;
        $doctor->user->mobile = $mobile;
        $doctor->sourcestr = $sourcestr;
        $doctor->title = $title;
        $doctor->hospital_name = $hospital_name;
        $doctor->auditorid_yunying = $auditorid_yunying;
        $doctor->auditorid_market = $auditorid_market;
        $doctor->auditorid_createby = $auditorid_createby;
        $doctor->pdoctorid = $pdoctorid;
        $doctor->patients_referencing = $patients_referencing;
        $doctor->menzhen_offset_daycnt = $menzhen_offset_daycnt;
        $doctor->menzhen_pass_date = $menzhen_pass_date;
        $doctor->is_audit_chufang = $is_audit_chufang;

        //开通了续方审核
        if ($is_audit_chufang) {
            $audit_chufang_pass_time = $doctor->audit_chufang_pass_time;
            if ($audit_chufang_pass_time == "0000-00-00 00:00:00") {
                $doctor->audit_chufang_pass_time = date("Y-m-d H:i:s");
            }
        } else {
            $doctor->audit_chufang_pass_time = "0000-00-00 00:00:00";
        }

        $doctor->is_sign = $is_sign;
        $doctor->status = $status;
        $doctor->module_pushmsg = $module_pushmsg;
        $doctor->service_remark = $service_remark;
        $doctor->hospitalid = $hospitalid;
        $doctor->department = $department;

        $doctor->user->name = $name;

        // 用户名修改,会导致二维码重新生成
        $username = XRequest::getValue("username", '');

        if ($this->myauditor->isSuperman() && $username && $doctor->user->username != $username) {
            $doctor->user->username = $username;
            $doctor->code = $username;

            foreach ($doctor->getDoctorWxShopRefs() as $doctorWxShopRef) {
                $doctorWxShopRef->qr_ticket = '';
            }
        }

        //写入医生主管表
        if ($superior_doctorids && is_array($superior_doctorids)) {
            //数据库已经存在的
            $currDoctorSuperiors = Doctor_SuperiorDao::getListByDoctorid($doctorid);
            $sameDoctorSuperiorIds = [];
            if ($currDoctorSuperiors) {
                foreach ($currDoctorSuperiors as $one) {
                    //要删除的
                    if (!in_array($one->superior_doctorid, $superior_doctorids)) {
                        $one->remove();
                    } else {
                        $sameDoctorSuperiorIds[] = $one->superior_doctorid;
                    }
                }
            }

            //要插入的
            $insertDoctorSuperiorIds = array_diff($superior_doctorids, $sameDoctorSuperiorIds);
            foreach ($insertDoctorSuperiorIds as $superior_doctorid) {
                $row = [];
                $row['doctorid'] = $doctorid;
                $row['superior_doctorid'] = $superior_doctorid;
                $doctorSuperior = Doctor_Superior::createByBiz($row);
            }
        } else {
            $cond = " AND doctorid=:doctorid";
            $bind = [
                ':doctorid' => $doctor->id,
            ];
            $doctorSuperiors = Dao::getEntityListByCond('Doctor_Superior', $cond, $bind);
            foreach ($doctorSuperiors as $doctorSuperior) {
                $doctorSuperior->remove();
            }
        }

        $row = array();
        $row["userid"] = $this->myuser->id;
        $row["objtype"] = 'Doctor';
        $row["objid"] = $doctor->id;
        $row["typestr"] = 'modify';
        $row["title"] = "医生信息修改:{$doctor->name}";
        $row["content"] = '';
        Comment::createByBiz($row);

        //医生疾病关系修改
        foreach ($doctor->getDoctorDiseaseRefs() as $ref) {
            if (false == in_array($ref->diseaseid, $diseaseids)) {
                $ref->remove();
            }
        }

        foreach ($diseaseids as $diseaseid) {
            if (false == $doctor->isBindDisease($diseaseid)) {
                $row = array();
                $row["doctorid"] = $doctor->id;
                $row["diseaseid"] = $diseaseid;
                DoctorDiseaseRef::createByBiz($row);
            }
        }

        $preMsg = "" . XDateTime::now();
        XContext::setJumpPath("/doctormgr/modify?doctorid=" . $doctorid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 就诊须知页的显示
    public function doTreatmentNotice() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);

        $lesson = $doctor->getTreatmentLesson();
        $lessonid = $lesson->id;

        XContext::setValue("lessonid", $lessonid);
        XContext::setValue("doctor", $doctor);
        return self::SUCCESS;
    }

    // 就诊须知页的显示
    public function doChanggeIsTreatmentNoticeJson() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $is_treatment_notice = XRequest::getValue("is_treatment_notice", 0);
        $doctor = Doctor::getById($doctorid);

        $lesson = $doctor->getTreatmentLesson();

        if ($is_treatment_notice && false == $lesson instanceof Lesson) {
            echo "needlesson";
            return self::BLANK;
        }

        $doctor->is_treatment_notice = $is_treatment_notice;

        echo "ok";
        return self::BLANK;
    }

    public function doDoctorWithdrawPost() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $the_month = XRequest::getValue("the_month", "");
        $amount_yuan = XRequest::getValue('amount_yuan', 0);
        DBC::requireTrue($amount_yuan > 0, "汇款金额不能为0");
        $remark = XRequest::getValue('remark', '');

        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, "医生不存在");

        $account = $doctor->getAccount();
        $doctorWithdrawOrder = OrderService::processDoctorWithdraw($account, $amount_yuan * 100, $this->myauditor);
        $doctorWithdrawOrder->remark = $remark;

        $preMsg = "" . XDateTime::now();
        XContext::setJumpPath("/doctorServiceOrderMgr/list?doctorid=" . $doctorid . "&the_month=" . $the_month . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // doSettleListMonth
    public function doSettleListMonth() {
        $cond = " ";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and id in ( select doctorid from doctordiseaserefs where diseaseid in ($diseaseidstr) ) ";

        $ids = Doctor::getTestDoctorIdStr();
        $cond .= " and id not in ({$ids}) and id < 10000 and status = 1 order by id ";

        $doctors = Dao::getEntityListByCond("Doctor", $cond, $bind);

        // 获取当前月和前2个月日期(如2016-04,2016-03....)
        $months = DoctorDao::getRptMonths(3, true);

        // 获取处理之后的报到数和扫码数数组

        // 全部有效报到患者,包括,删除　属于医生的
        // $_patientRptGroupbyDoctorMonth_all =
        // PatientDao::getRptGroupbyDoctorMonth(true);
        $_patientRptGroupbyDoctorMonth_all = DoctorSettleOrderDao::getRptGroupbyDoctorMonth();
        $patientRptGroupbyDoctorMonth_all = $this->dealPrefMonthArr($_patientRptGroupbyDoctorMonth_all, false);

        $_patientRptGroupbyDoctorMonth_baodao = Rpt_patient_month_settleDao::getListGroupByDoctoridAndThemonth(
            " and left(themonth, 7)=left(baodaodate, 7)
and pipecntbypatient > 0 and isscan = 1
and (patientdaycnt > 0 or patientdaycnt = '')  ");
        $patientRptGroupbyDoctorMonth_baodao = $this->dealPrefMonthArr($_patientRptGroupbyDoctorMonth_baodao, false);

        $_patientRptGroupbyDoctorMonth_manage = Rpt_patient_month_settleDao::getListGroupByDoctoridAndThemonth(
            " and month_pos>1 and month_pos<7
and pipecntbypatient > 0 and isscan = 1
and (patientdaycnt > 0 or patientdaycnt = '')  ");
        $patientRptGroupbyDoctorMonth_manage = $this->dealPrefMonthArr($_patientRptGroupbyDoctorMonth_manage, false);

        XContext::setValue('months', $months);

        XContext::setValue("doctors", $doctors);

        XContext::setValue('patientRptGroupbyDoctorMonth_all', $patientRptGroupbyDoctorMonth_all);
        XContext::setValue('patientRptGroupbyDoctorMonth_baodao', $patientRptGroupbyDoctorMonth_baodao);
        XContext::setValue('patientRptGroupbyDoctorMonth_manage', $patientRptGroupbyDoctorMonth_manage);

        return self::SUCCESS;
    }

    //医生收益 按月 导出
    public function doSettleListMonthOutput() {
        $date = XRequest::getValue("date", date('Y-m-d'));
        $dateYm = date('Y-m', strtotime($date));

        $cond = " ";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and id in ( select doctorid from doctordiseaserefs where diseaseid in ($diseaseidstr) ) ";

        $ids = Doctor::getTestDoctorIdStr();
        $cond .= " and id not in ({$ids}) and id < 10000 and status = 1 order by id ";

        $doctors = Dao::getEntityListByCond("Doctor", $cond, $bind);
        $indexnum = 0;

        $sql = "SELECT doctorid, left(themonth, 7) as themonth, sum(activecnt) as cnt
                FROM doctorsettleorders
                where left(themonth, 7)<='{$dateYm}'
                group by doctorid";

        $_patientRptGroupbyDoctor_all = Dao::queryRows($sql);
        $patientRptGroupbyDoctor_all = $this->dealPrefMonthArr($_patientRptGroupbyDoctor_all, false);

        $_patientRptGroupbyDoctorMonth_baodao = Rpt_patient_month_settleDao::getListGroupByDoctoridAndThemonth(
            " and left(themonth, 7) = '{$dateYm}'
and left(themonth, 7)=left(baodaodate, 7)
and pipecntbypatient > 0 and isscan = 1
and (patientdaycnt > 0 or patientdaycnt = '')  ");
        $patientRptGroupbyDoctorMonth_baodao = $this->dealRpt_patient_month_settleArr($_patientRptGroupbyDoctorMonth_baodao);

        $_patientRptGroupbyDoctorMonth_manage = Rpt_patient_month_settleDao::getListGroupByDoctoridAndThemonth(
            " and left(themonth, 7) = '{$dateYm}'
and month_pos>1 and month_pos<7
and pipecntbypatient > 0 and isscan = 1
and (patientdaycnt > 0 or patientdaycnt = '')  ");
        $patientRptGroupbyDoctorMonth_manage = $this->dealRpt_patient_month_settleArr($_patientRptGroupbyDoctorMonth_manage);

        foreach ($doctors as $doctor) {
            $indexnum++;

            $temp = array();
            $temp[] = $indexnum;
            $temp[] = $doctor->name;
            $hospital = $doctor->hospital;
            if ($hospital instanceof Hospital) {
                $temp[] = $hospital->shortname;
            } else {
                $temp[] = "";
            }
            $temp[] = $doctor->marketauditor->name;
            $temp[] = $hospital->xprovince->name;
            $temp[] = $hospital->xcity->name;
            //新患者数
            $x = isset($patientRptGroupbyDoctorMonth_baodao[$doctor->id]) ? $patientRptGroupbyDoctorMonth_baodao[$doctor->id] : 0;
            //老患者数
            $y = isset($patientRptGroupbyDoctorMonth_manage[$doctor->id]) ? $patientRptGroupbyDoctorMonth_manage[$doctor->id] : 0;
            $temp[] = $x;
            $temp[] = $y;
            $temp[] = ($x + $y) > 0 ? round($y / ($x + $y), 2) * 100 . "%" : 0;
            $temp[] = ($x + $y) * 15;
            $temp[] = isset($patientRptGroupbyDoctor_all[$doctor->id]["allcnt"]) ? $patientRptGroupbyDoctor_all[$doctor->id]["allcnt"] * 15 : 0;
            $data[] = $temp;

        }
        $headarr = array(
            "序号",
            "医生姓名",
            "医院",
            "市场负责人",
            "地区-省",
            "地区-市",
            "新患者数 x ",
            "老患者数 y ",
            "老患者率 y/(x+y) ",
            "费用 (x+y)*15 ",
            "总计 allcnt * 15");
        if (count($data) > 0) {
            ExcelUtil::createForWeb($data, $headarr);
        }
    }

    // 处理原始数组（month） 将原始数组处理为以 doctorid => cnt的数组，例如 1 => 0,1,8,3,4,4,35,55
    private function dealRpt_patient_month_settleArr(array $arr) {
        $temp_array = array();
        foreach ($arr as $a) {
            $doctorid = $a['doctorid'];
            $cnt = $a['cnt'];

            $temp_array[$doctorid] = $cnt;
        }

        return $temp_array;
    }

    // 获取按市场人员和月份分组汇总的报表
    private function getRptGroupbyMarketMonth() {
        $cond = " ";

        $sql = " select a.auditorid_market as marketid, LEFT(b.themonth, 7) AS themonth, count(b.id) as cnt
            from doctors a
            inner join statdb.rpt_patient_month_settles b ON a.id = b.doctorid
            WHERE left(b.themonth, 7) = left(b.baodaodate, 7) AND a.auditorid_market not in (0, 10008, 10009, 10021)
            GROUP BY a.auditorid_market, b.themonth
            ORDER BY a.auditorid_market, b.themonth DESC";

        return Dao::queryRows($sql);
    }

    public function doModifyDoctorGroupJson() {
        $doctorgroupid = XRequest::getValue('doctorgroupid', 0);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, "doctor is null");

        $doctor->doctorgroupid = $doctorgroupid;

        echo 'ok';

        return self::BLANK;
    }

    // 当月管理收益详情(医生)
    public function doListOfDoctor() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $themonth = XRequest::getValue("themonth", 0);

        $doctor = Doctor::getById($doctorid);

        $settleorder = DoctorSettleOrderDao::getOneByDoctoridMonth($doctorid, $themonth);
        $rpt_patient_month_settles_baodao = Rpt_patient_month_settleDao::getList_baodao($doctorid, $themonth);
        $wxusers_notbaodao = WxUserDao::getNotBaodaoByDoctorid($doctorid, $themonth);
        $rpt_patient_month_settles_manage = Rpt_patient_month_settleDao::getList_manage($doctorid, $themonth);

        XContext::setValue("doctor", $doctor);

        XContext::setValue("themonth", $themonth);

        XContext::setValue("settleorder", $settleorder);
        XContext::setValue("rpt_patient_month_settles_baodao", $rpt_patient_month_settles_baodao);
        XContext::setValue("wxusers_notbaodao", $wxusers_notbaodao);
        XContext::setValue("rpt_patient_month_settles_manage", $rpt_patient_month_settles_manage);

        return self::SUCCESS;
    }

    // 当月管理收益详情(员工)
    public function doListOfMarket() {
        $auditorid = XRequest::getValue("auditorid", 0);
        $themonth = XRequest::getValue("themonth", 0);

        $firstday_month = date("Y-m-d", strtotime($themonth));
        $lastday_month = date("Y-m-t", strtotime($themonth));

        $firstwoy = XDateTime::getWFromFirstDate($firstday_month);
        $lastwoy = XDateTime::getWFromFirstDate($lastday_month);

        $auditor = Auditor::getById($auditorid);

        $arr_staticsbywoy = array();
        for (; $firstwoy < $lastwoy; $lastwoy--) {
            $arr_staticsbywoy[$lastwoy]["themonth"] = XDateTime::getDatemdByWoy($lastwoy);
            $arr_staticsbywoy[$lastwoy]["baodao"] = AuditorDao::getBaodaoByWoy($auditorid, $lastwoy);
            $arr_staticsbywoy[$lastwoy]["notbaodao"] = AuditorDao::getNotBaodaoByWoy($auditorid, $lastwoy);
        }

        $baodao_patients = PatientDao::getBaodaoByMonth($auditorid, $themonth);
        $notbaodao_wxusers = WxUserDao::getNotBaodaoByMonth($auditorid, $themonth);

        XContext::setValue("auditor", $auditor);

        XContext::setValue("themonth", $themonth);

        XContext::setValue("baodao_patients", $baodao_patients);
        XContext::setValue("notbaodao_wxusers", $notbaodao_wxusers);
        XContext::setValue("arr_staticsbywoy", $arr_staticsbywoy);

        return self::SUCCESS;
    }

    // 医生变更市场负责人页面
    public function doOneForChangeAuditorMarket() {
        $doctorid = XRequest::getValue("doctorid", 0);

        $doctor = Doctor::getById($doctorid);

        XContext::setValue("doctor", $doctor);

        return self::SUCCESS;
    }

    // 医生变更市场负责人接口
    public function doChangeAuditorMarketJson() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $to_auditorid_market = XRequest::getValue("to_auditorid_market", 0);

        if ($to_auditorid_market) {

            if (!$doctorid) {
                echo "default";
                return self::BLANK;
            }

            $doctor = Doctor::getById($doctorid);
            $doctor->auditorid_market = $to_auditorid_market;

            // 变更成功
            echo "ok";
            return self::BLANK;
        } else {

            // 没有收到要变更成的auditorid_market
            echo "notToMarketId";
            return self::BLANK;
        }
    }

    // 去new
    public function doDeleteNewJson() {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, 'doctor is null');

        $doctor->is_new_pipe = 0;

        echo 'ok';

        return self::BLANK;
    }

    // 处理原始数组（month） 将原始数组处理为以 doctorid => cnt的数组，例如 1 => 0,1,8,3,4,4,35,55
    private function dealPrefMonthArr(array $arr, $hadthismonth = true) {
        if ($hadthismonth) {
            $months = DoctorDao::getRptMonths();
        } else {
            $months = DoctorDao::getRptMonths(3, true);
        }

        // 第3个月
        $month_2 = $months[2];
        $time2 = strtotime($month_2);

        $doctorid_month_cnt_array = array();
        foreach ($arr as $a) {
            $doctorid = $a['doctorid'];
            $themonth = $a['themonth'];
            $cnt = $a['cnt'];

            if (false == isset($doctorid_month_cnt_array[$doctorid])) {
                $doctorid_month_cnt_array[$doctorid] = array();
            }

            $doctorid_month_cnt_array[$doctorid][$themonth] = $cnt;
        }

        $doctorid_month_cnt_array_new = array();
        foreach ($doctorid_month_cnt_array as $doctorid => $arr) {
            foreach ($months as $m) {
                if (false == isset($doctorid_month_cnt_array_new[$doctorid])) {
                    $doctorid_month_cnt_array_new[$doctorid] = array();
                }

                if (false == isset($arr[$m])) {
                    $doctorid_month_cnt_array_new[$doctorid][$m] = 0;
                } else {
                    $doctorid_month_cnt_array_new[$doctorid][$m] = $arr[$m];
                }
            }
        }

        foreach ($doctorid_month_cnt_array as $doctorid => $arr) {

            $beforecnt = 0;
            $allcnt = 0;
            foreach ($arr as $m => $cnt) {
                $allcnt += $cnt;
                if (strtotime($m) < $time2) {
                    $beforecnt += $cnt;
                }
            }

            $doctorid_month_cnt_array_new[$doctorid]['beforecnt'] = $beforecnt;
            $doctorid_month_cnt_array_new[$doctorid]['allcnt'] = $allcnt;
        }

        return $doctorid_month_cnt_array_new;
    }

    public function doFetchInfo() {
        $doctorid = XRequest::getValue("doctorid", 0);

        $doctor = Doctor::getById($doctorid);

        XContext::setValue("doctor", $doctor);

        return self::SUCCESS;
    }

    public function doFetchInfoJson() {
        $doctorid = XRequest::getValue('doctorid', 0);
        $fetch_url = XRequest::getValue('fetch_url', "");
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, 'doctor is null');

        // Debug::trace($fetch_url);
        $rules = $this->getRules();

        $search_ql = QueryList::Query($fetch_url, $rules, '', [], null, 'UTF-8', 'GB2312', true);

        // 返回状态不为200
        if ($search_ql->getState() != 200 || empty($search_ql->getHtml())) {
            echo "抓取页面返回错误!";
            return self::BLANK;
        }

        // 爬取数据
        $datas = $search_ql->getData();

        if (empty($datas)) {
            echo "抓取数据为空！";
            return self::BLANK;
        }

        $data = $datas[0];
        $bigpipe = $data['bigpipe'];

        if ('' == $bigpipe) {
            echo "抓取到的BigPipe数据为空！";
            return self::BLANK;
        }

        preg_match('/BigPipe\\.onPageletArrive\\((.+)\\);/', $bigpipe, $matches);

        // 抓取到页面中的 bigpipe js
        $bigpipe = $matches[1];
        $bigpipe = json_decode($bigpipe, JSON_UNESCAPED_UNICODE);
        // print_r($bigpipe);
        // return self::BLANK;
        // Debug::trace($bigpipe);

        // json_decode后取出要正则匹配的内容
        $content = $bigpipe["content"];

        // echo "抓取到的conotent！{$content}";
        // return self::BLANK;

        preg_match('/<h1>.*>(.+?)<\\/a>大夫简介/', $content, $matches);
        $name = $matches[1];

        if ('' == $name) {
            echo "抓取目标页的医生名字为空！";
            return self::BLANK;
        }

        // echo "抓取到的医生名字！{$name}";
        // return self::BLANK;

        if ($name != $doctor->name) {
            echo "抓取目标页的医生名字与当前医生名字不一致！{$name}";
            return self::BLANK;
        }

        preg_match('/<div class="ys_tx">[\W\w]*?<img src="(.+?)"[\W\w]*?<\\/div>/', $content, $matches);
        $headimg_url = $matches[1];
        preg_match('/职　　称：<\\/td>.*\n.*>(.+?)<\\/td>/', $content, $matches);
        $title = $matches[1];
        preg_match('/科　　室：<\\/td>.*\n.*<h2>(.+?)<\\/h2>/', $content, $matches);
        $department = $matches[1];
        preg_match('/<div id="full".*>([\W\w]*?)<\\/div>/', $content, $matches);
        $brief = $matches[1];
        preg_match('/<div id="full_DoctorSpecialize".*>([\W\w]*?)<\\/div>/', $content, $matches);
        $be_good_at = $matches[1];

        // echo "抓取到的信息！{$be_good_at}";
        // return self::BLANK;

        if ($headimg_url) {
            $picture = Picture::createByFetch($headimg_url);
            if ($picture instanceof Picture) {
                $doctor->headimg_pictureid = $picture->id;
            }
        }

        if ($title) {
            $doctor->title = $title;
        }

        if ($department) {
            $department = str_replace('&nbsp;', '', $department);
            $doctor->department = $department;
        }

        if ($brief) {
            $brief = trim($brief);
            $brief = preg_replace('/<span>.*<\\/span>/', '', $brief);
            $doctor->brief = $brief;
        }

        if ($be_good_at) {
            $be_good_at = trim($be_good_at);
            $be_good_at = preg_replace('/<span>.*<\\/span>/', '', $be_good_at);
            $doctor->be_good_at = $be_good_at;
        }

        echo 'ok';
        return self::BLANK;
    }

    public function doSearchDoctorJson() {
        $q = XRequest::getValue('q', '');
        $diseaseids = XRequest::getValue('diseaseids', []);
        if (!$diseaseids) {
            return self::TEXTJSON;
        }
        $diseaseidstr = implode(',', $diseaseids);
        $cond = "";
        if ($diseaseidstr) {
            $cond = " AND b.diseaseid IN ({$diseaseidstr})";
        }
        $sql = "SELECT a.* FROM doctors a INNER JOIN doctordiseaserefs b
            ON a.id = b.doctorid
            WHERE a.id < 100000 $cond AND a.name LIKE :name
            GROUP BY a.id LIMIT 10
            ";
        $bind = [
            ':name' => "%{$q}%",
        ];
        $doctors = Dao::loadEntityList('Doctor', $sql, $bind);
        $data = [];
        $data['list'] = [];
        foreach ($doctors as $doctor) {
            $data['list'][] = ['id' => $doctor->id, 'text' => $doctor->name . ' ' . $doctor->hospital->name];
        }
        $this->result['data'] = $data;

        return self::TEXTJSON;
    }

    public function doChangeis_alkJson() {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, 'doctor is null');

        $is_alk = XRequest::getValue('is_alk', 0);

        $doctor->is_alk = $is_alk;

        echo "success";

        return self::BLANK;
    }

    private function getRules() {
        $arr = [
            "bigpipe" => ['body script:eq(1)', 'html'],
            // "name" => ['.doctor_about .toptr .lt .nav h1 a', 'text'],
            // "headimg_url" => ['.doctor_about .middletr .tbody .ys_tx img', 'src'],
            // "title" => ['.doctor_about .middletr .tbody tr>td[valign="top"]:eq(2)', 'text'],
            // "department" => ['.doctor_about .middletr .tbody tr>td>a>h2', 'text'],
            // "brief" => ['.doctor_about .middletr .tbody tr>td[colspan="3"] #full', 'text', '-span'],
            // "be_good_at" => ['.doctor_about .middletr .tbody tr>td[colspan="3"] #full_DoctorSpecialize', 'text', '-span'],
        ];
        return $arr;
    }

}
