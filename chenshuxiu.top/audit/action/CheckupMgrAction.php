<?php

class CheckupMgrAction extends AuditBaseAction
{

    // 检查报告列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        // 外部来的参数
        $patientid = XRequest::getValue('patientid', 0);
        $checkupid = XRequest::getValue('checkupid', 0);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patient_name = XRequest::getValue('patient_name', '');
        $fromdate = XRequest::getValue('fromdate', '');
        $todate = XRequest::getValue('todate', '');

        $patient = Patient::getById($patientid);

        $sql = "select distinct a.*
                from checkups a
                inner join patients b on b.id = a.patientid
                inner join pcards c on c.patientid = b.id  and a.doctorid = c.doctorid
                where a.status = 0 and a.checkuptplid > 0 ";
        $cond = "";
        $url = "/checkupmgr/list?1=1";
        $bind = [];

        if ($patientid) {
            $url .= "&patientid={$patientid}";
            $cond .= ' and a.patientid = :patientid ';
            $bind[':patientid'] = $patientid;
        }

        if ($checkupid) {
            $url .= "&checkupid={$checkupid}";
            $cond = ' and a.id = :id ';
            $bind[':id'] = $checkupid;
        }

        if ($patient_name) {
            $cond .= " and b.name like :name ";
            $bind[':name'] = "%{$patient_name}%";
        }

        if ($doctorid) {
            $url .= "&doctorid={$doctorid}";
            $cond .= " and c.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        // 开始时间
        if ($fromdate) {
            $url .= "&fromdate={$fromdate}";
            $cond .= " and a.check_date >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
        }

        // 截至时间
        if ($todate) {
            $url .= "&todate={$todate}";
            $cond .= " and a.check_date < :todate ";
            $bind[':todate'] = $todate;
        }

        $cond .= " order by a.check_date desc ";

        $sql .= $cond;

        $checkups = Dao::loadEntityList4Page('Checkup', $sql, $pagesize, $pagenum, $bind);

        // 分页
        $sqlcnt = str_replace('distinct a.*', 'count(distinct a.id)', $sql);
        $cnt = Dao::queryValue($sqlcnt, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient', $patient);
        XContext::setValue('checkups', $checkups);
        XContext::setValue("doctorid", $doctorid);
        Xcontext::setValue('patient_name', $patient_name);
        Xcontext::setValue('doctor_name', $doctor_name);
        Xcontext::setValue('fromdate', $fromdate);
        Xcontext::setValue('todate', $todate);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 检查报告答卷录入
    public function doAddXQuestion () {
        $patientid = XRequest::getValue('patientid', 0);
        $check_date = XRequest::getValue('check_date', '');
        $checkuptplid = XRequest::getValue('checkuptplid', 0);

        $patient = Patient::getById($patientid);
        $checkuptpl = CheckupTpl::getById($checkuptplid);
        $xsheet = $checkuptpl->xquestionsheet;

        if ($checkuptpl instanceof CheckupTpl) {
            // 获取当前时间的checkup
            $checkup = CheckupDao::getByPatientCheckupTplCheck_date($patient, $checkuptpl, $check_date);
            if ($checkup && $checkup->xanswersheet instanceof XAnswerSheet) {
                $xsheet = $checkup->xanswersheet;
            }
        }

        $patient = Patient::getById($patientid);
        $checkuptpls = Dao::getEntityListByCond('CheckupTpl');

        XContext::setValue('checkuptplid', $checkuptplid);
        XContext::setValue('check_date', $check_date);

        XContext::setValue('patient', $patient);
        XContext::setValue('checkuptpl', $checkuptpl);
        XContext::setValue('checkup', $checkup);
        XContext::setValue('xsheet', $xsheet);

        XContext::setValue('checkuptpls', $checkuptpls);

        return self::SUCCESS;
    }

    // 检查报告答卷提交
    public function doAddxquestionPost () {
        $patientid = XRequest::getValue('patientid', 0);
        $check_date = XRequest::getValue('check_date', date('Y-m-d'));
        $checkuptplid = XRequest::getValue('checkuptplid', 0);
        $sheets = XRequest::getValue('sheets', array());
        $hospitalstr = XRequest::getValue('hospitalstr', '');
        $content = XRequest::getValue('content', '');

        $patient = Patient::getById($patientid);

        $checkuptpl = CheckupTpl::getById($checkuptplid);

        // 获取当前时间的checkup
        $checkup = CheckupDao::getByPatientCheckupTplCheck_date($patient, $checkuptpl, $check_date);

        if (isset($sheets['XQuestionSheet']) && $checkup instanceof Checkup) {
            DBC::requireTrue(false, '重复提交检查报告');
        }

        if (false == $checkup instanceof Checkup) {
            $row = array();
            $row['patientid'] = $patientid;
            $row["doctorid"] = $patient->doctorid; // done pcard fix
            $row['check_date'] = $check_date;
            $row['hospitalstr'] = $hospitalstr;
            $row['checkuptplid'] = $checkuptplid;
            $row['content'] = $content;

            $checkup = Checkup::createByBiz($row);
            $pipe = Pipe::createByEntity($checkup);
        }

        $checkup->hospitalstr = $hospitalstr;
        $checkup->content = $content;

        $maxXAnswer = XWendaService::doPost($sheets, $patient->createuser, 'Checkup', $checkup->id);
        $checkup->xanswersheetid = $maxXAnswer->xanswersheetid;

        XContext::setJumpPath("/checkupmgr/list?checkupid={$checkup->id}");

        return self::SUCCESS;
    }

    // 检查报告新建
    public function doAdd () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);

        XContext::setValue('patient', $patient);

        return self::SUCCESS;
    }

    // 检查报告新建提交
    public function doAddPost () {
        $patientid = XRequest::getValue("patientid", "");
        $check_date = XRequest::getValue("check_date", "");
        $hospitalstr = XRequest::getValue("hospitalstr", "");
        $content = XRequest::getValue("content", "");

        $patient = Patient::getById($patientid);

        // 将前台获取的数据放置进数组中，用于初始化对象属性
        $row = array();
        $row['patientid'] = $patientid;
        $row['doctorid'] = $patient->doctorid; // done pcard fix
        $row['check_date'] = $check_date;
        $row['hospitalstr'] = $hospitalstr;
        $row['content'] = $content;
        $row['auditorid'] = $this->myauditor->id;

        // （对象属性使用数组方式，即从前台获取的数据数组，
        // 和后台对象的属性数组相加的方式，进行赋值，然后框架自动生成insert语句）
        $checkup = Checkup::createByBiz($row);
        $pipe = Pipe::createByEntity($checkup);

        XContext::setJumpPath("/checkupmgr/list");
        return self::SUCCESS;
    }

    // 检查报告修改
    public function doModify () {
        $checkupid = XRequest::getValue("checkupid", 0);

        $checkup = Checkup::getById($checkupid);

        $picturerefs = PictureRefDao::getListByObj($checkup);
        $wxpicmsgs = WxPicMsgDao::getListByPatientid($checkup->patientid);

        XContext::setValue("checkup", $checkup);
        XContext::setValue('picturerefs', $picturerefs);
        XContext::setValue('wxpicmsgs', $wxpicmsgs);
        return self::SUCCESS;
    }

    // 检查报告修改提交
    public function doModifyPost () {
        $checkupid = XRequest::getValue("checkupid", 0);
        $check_date = XRequest::getValue("check_date", "0000-00-00");
        $hospitalstr = XRequest::getValue("hospitalstr", "");
        $content = XRequest::getValue("content", "");

        $checkup = Checkup::getById($checkupid);
        $checkup->check_date = $check_date;
        $checkup->hospitalstr = $hospitalstr;
        $checkup->content = $content;

        $preMsg = "修改已提交 " . XDateTime::now();

        XContext::setJumpPath("/checkupmgr/modify?checkupid={$checkupid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 检查报告删除Json
    public function doDeleteJson () {
        $checkupid = XRequest::getValue('checkupid', 0);

        $checkup = Checkup::getById($checkupid);
        $checkuppictures = CheckupPictureDao::getListByCheckupid($checkup->id);
        foreach ($checkuppictures as $checkuppicture) {
            $checkuppicture->remove();
        }

        $checkup->remove();

        echo "success";

        return self::BLANK;
    }
}
