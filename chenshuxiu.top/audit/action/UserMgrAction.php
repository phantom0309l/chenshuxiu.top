<?php

class UserMgrAction extends AuditBaseAction
{

    public function doDefault () {
        return self::SUCCESS;
    }

    public function doListForPatient () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $cond = '';
        $bind = [];

        $sql_body = " from users a
                    inner join patients b on b.id=a.patientid and b.name <> ''
                    inner join pcards x on x.patientid=a.patientid
                    where 1=1 ";

        $diseaseidstr = $this->getContextDiseaseidStr();

        $cond .= " and x.diseaseid in ($diseaseidstr) ";

        if ($doctorid) {
            $cond .= " and b.doctorid=:doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($patientid) {
            $cond .= " and b.id = :patientid ";
            $bind[':patientid'] = $patientid;
        } else {
            if ($patient_name) {
                $cond .= " and x.patient_name like :patient_name ";
                $bind[':patient_name'] = "%{$patient_name}%";
            }
        }

        $cond .= " order by b.auditstatus asc, b.createtime desc ";
        $sql = " select a.* " . $sql_body . $cond;

        $users = Dao::loadEntityList4Page("User", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("users", $users);

        $countSql = " select count(*) as cnt  " . $sql_body . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/usermgr/listforpatient?doctorid={$doctorid}&doctor_name={$doctor_name}&patientid={$patientid}&patient_name={$patient_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 用户列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $word = XRequest::getValue("word", '');
        $usertype = XRequest::getValue('usertype', 'all');

        $cond = " where 1 = 1 ";
        $bind = [];

        $sql = " select distinct u.*
            from users u ";

        $condsql = "";

        $diseaseidstr = $this->getContextDiseaseidStr();

        if ($usertype != 'all') {
            // 患者
            if ($usertype == 'Patient') {
                $condsql .= " inner join patients b on b.id = u.patientid
                    inner join pcards c on c.patientid = b.id ";

                $cond .= " and c.diseaseid in ($diseaseidstr) ";
            }

            // 医生
            if ($usertype == 'Doctor') {
                $condsql .= " inner join doctors d on d.userid = u.id
                    inner join doctordiseaserefs df on df.doctorid = d.id ";

                $cond .= " and df.diseaseid in ($diseaseidstr) ";
            }

            // 运营
            if ($usertype == 'Auditor') {
                $condsql .= " inner join auditors at on at.userid = u.id
                    inner join auditordiseaserefs af on af.auditorid = at.id ";

                $cond .= " and af.diseaseid in ($diseaseidstr) ";
            }
        }

        $sql .= $condsql;
        $sql .= $cond;

        $countSql = "select count( distinct(u.id) ) from users u " . $condsql . $cond;

        // 输入
        if ($word) {
            $cond = ' left join wxusers w on w.userid = u.id
            left join patients p on p.id = u.patientid
            where u.mobile like :mobile or u.username like :username or u.name like :name
            or w.nickname like :nickname or p.name like :pname
            ';

            $bind = [];
            $bind[':mobile'] = "%{$word}%";
            $bind[':username'] = "%{$word}%";
            $bind[':name'] = "%{$word}%";
            $bind[':nickname'] = "%{$word}%";
            $bind[':pname'] = "%{$word}%";

            $sql = " select distinct u.* from users u " . $cond;
            $countSql = "select count(distinct u.id) from users u " . $cond;

            $usertype = 'all';
        }

        $users = Dao::loadEntityList4Page('User', $sql, $pagesize, $pagenum, $bind);

        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/usermgr/list?word={$word}&usertype={$usertype}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("word", $word);
        XContext::setValue('usertype', $usertype);
        XContext::setValue("users", $users);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 修改页的显示
    public function doModify () {
        $userid = XRequest::getValue("userid", 0);
        $user = User::getById($userid);

        $patientaddress = PatientAddressDao::getByTypePatientid('mobile_place', $user->patientid);

        XContext::setValue("user", $user);
        XContext::setValue("patientaddress", $patientaddress);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $userid = XRequest::getValue("userid", 0);
        $name = XRequest::getValue("name", '');
        // $shipstr = XRequest::getValue("shipstr", '');
        // $mobile = XRequest::getValue("mobile", '');
        $password = XRequest::getValue("password", '');
        $auditremark = XRequest::getValue("auditremark", '');

        $mobile_place = XRequest::getValue('mobile_place', []);
        $mobile_place = PatientAddressService::fixNull($mobile_place);

        $user = User::getById($userid);
        $user->name = $name;
        // $user->shipstr = $shipstr;
        // $user->mobile = $mobile; //报到后不修改
        if ($this->myauditor->isHasRole([
            'admin',
            'yunying',
            'yunyingmgr'])) {
            $user->modifyPassword($password);
        }

        $user->auditremark = $auditremark;

        BeanFinder::get("UnitOfWork")->commitAndInit();

        $patient = $user->patient;

        if ($patient instanceof Patient) {
            PatientAddressService::updatePatientAddress($mobile_place, 'mobile_place', $patient->id, false);
        } else {
            Debug::warn("user[{$user->id}] 没有patient");
        }

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/usermgr/modify?userid=" . $userid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 新建的显示
    public function doAdd () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        XContext::setValue("patientid", $patientid);
        XContext::setValue("patient", $patient);

        return self::SUCCESS;
    }

    // 新建提交
    public function doAddPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $name = XRequest::getValue("name", '');
        $shipstr = XRequest::getValue("shipstr", '');
        $mobile = XRequest::getValue("mobile", '');
        $password = XRequest::getValue("password", '');
        $auditremark = XRequest::getValue("auditremark", '');
        $fbt_isopen = XRequest::getValue("fbt_isopen", 0);

        DBC::requireNotEmpty($mobile, '手机号不能为空');

        $user = UserDao::getByMobile($mobile);

        DBC::requireEmpty($user, '手机号重复,请先搜索');

        if (empty($password)) {
            $password = substr($mobile, - 6);
        }

        $row = array();
        $row["patientid"] = $patientid;
        $row["name"] = $name;
        $row["shipstr"] = $shipstr;
        $row["mobile"] = $mobile;
        $row["password"] = $password;
        $row["auditremark"] = $auditremark;

        $user = User::createByBiz($row);

        XContext::setJumpPath("/usermgr/modify?userid=" . $user->id);
        return self::SUCCESS;
    }
}
