<?php

// CheckupPictureMgrAction
class CheckupPictureMgrAction extends AuditBaseAction
{

    // 检查报告图片列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $checkuppictureid = XRequest::getValue('checkuppictureid', 0);

        $cond = " ";
        $url = "/checkuppicturemgr/list?1=1";
        $bind = [];

        if ($checkuppictureid) {
            $cond .= " and cp.id = :id ";
            $bind[':id'] = "{$checkuppictureid}";
        } else {
            if ($doctorid) {
                $url .= "&doctorid={$doctorid}";
                $cond .= " and x.doctorid = :doctorid ";
                $bind[':doctorid'] = $doctorid;
            }

            if ($patientid) {
                $cond .= " and p.id = :patientid ";
                $bind[':patientid'] = $patientid;
            } else {
                if ($patient_name) {
                    $cond .= " and p.name like :name ";
                    $bind[':name'] = "%{$patient_name}%";
                }
            }
        }

        $sql = "select cp.*
                from checkuppictures cp
                inner join patients p on cp.patientid=p.id
                inner join pcards x on x.patientid=p.id
                where 1=1
                ";

        $cond .= " and p.auditstatus = 1 ";
        $cond .= " order by cp.status asc, cp.id desc ";

        $sql .= $cond;
        $checkuppictures = Dao::loadEntityList4Page("CheckupPicture", $sql, $pagesize, $pagenum, $bind);

        // 翻页begin

        $countSql = "select count(distinct cp.id) as cnt
                    from checkuppictures cp
                    inner join patients p on cp.patientid=p.id
                    inner join pcards x on x.patientid=p.id
                    where 1=1 " . $cond;
        // 分页
        $cnt = Dao::queryValue($countSql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("checkuppictures", $checkuppictures);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 检查报告图片修改状态
    public function doChangeStatusPost() {
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);
        $checkuppicture = CheckupPicture::getById($checkuppictureid);

        $checkuppicture->status = 1 - $checkuppicture->status;
        XContext::setJumpPath("/checkuppicturemgr/list");
        return self::SUCCESS;
    }

    // 检查报告图片列表2
    public function doList4Show() {
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

        $cond = "";
        $bind = [];

        if ($doctorid) {
            $cond .= " and x.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($patientid) {
            $cond .= " and p.id = :patientid ";
            $bind[':patientid'] = $patientid;
        } else {
            if ($patient_name) {
                $cond .= " and p.name like :name ";
                $bind[':name'] = "%{$patient_name}%";
            }
        }

        $sql = "select p.*
                from patients p
                inner join pcards x on x.patientid=p.id
                left join (select * from checkuppictures GROUP BY patientid ORDER by id desc)
                cp ON cp.patientid = p.id
                where 1=1 ";

        $cond .= " and p.auditstatus = 1 ";
        $cond .= " order by cp.createtime desc ";

        $sql .= $cond;
        $patients = Dao::loadEntityList4Page("Patient", $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $countSql = "select count(*)
                     from patients as p
                     inner join pcards x on x.patientid=p.id
                     left join (select * from checkuppictures GROUP BY patientid ORDER by id desc) cp ON cp.patientid = p.id
                     where 1 = 1 " . $cond;

        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/checkuppicturemgr/list4show?doctorid={$doctorid}&patient_name={$patient_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue('patients', $patients);

        return self::SUCCESS;
    }

    public function doOneHtml4Pic() {
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);
        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        XContext::setValue("checkuppicture", $checkuppicture);
        return self::SUCCESS;
    }

    public function doOneHtml4Show() {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        $checkuppictures = CheckupPictureDao::getListByPatientid($patientid);

        $checkuppictures_arr = array();
        foreach ($checkuppictures as $checkuppicture) {
            $checkuppictures_arr[$checkuppicture->getCreateDay()][] = $checkuppicture;
        }
        XContext::setValue("patient", $patient);
        XContext::setValue("checkuppictures_arr", $checkuppictures_arr);
        return self::SUCCESS;
    }

    public function doList4bind() {
        $checkupid = XRequest::getValue("checkupid", 0);
        $checkup = Checkup::getById($checkupid);

        $checkuppictureall = CheckupPictureDao::getListByCheckupid(0, $checkup->patientid);
        $checkuppicturethis = CheckupPictureDao::getListByCheckupid($checkupid);

        XContext::setValue("checkup", $checkup);
        XContext::setValue("checkuppictureall", $checkuppictureall);
        XContext::setValue("checkuppicturethis", $checkuppicturethis);
        return self::SUCCESS;
    }

    public function doBindPost() {
        $checkupid = XRequest::getValue("checkupid", 0);
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);

        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        $checkuppicture->checkupid = $checkupid;

        XContext::setJumpPath("/checkuppicturemgr/list4bind?checkupid={$checkupid}");
        return self::BLANK;
    }

    public function doCancelPost() {
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);

        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        $checkupid = $checkuppicture->checkupid;
        $checkuppicture->checkupid = 0;

        XContext::setJumpPath("/checkuppicturemgr/list4bind?checkupid={$checkupid}");
        return self::BLANK;
    }

    public function doCheckupHtml() {
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);
        $checkuppicture = CheckupPicture::getById($checkuppictureid);

        $checkuptpls = CheckupTplDao::getListByDoctorAndDiseaseid_isInTkt_isInAdmin($checkuppicture->doctor, null, 0);

        XContext::setValue("checkuptpls", $checkuptpls);
        XContext::setValue("patient", $checkuppicture->patient);
        return self::SUCCESS;
    }

    public function doCheckupListHtml() {
        $patientid = XRequest::getValue("patientid", 0);
        $checkuptplid = XRequest::getValue("checkuptplid", 0);
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);

        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        XContext::setValue("checkuppicture", $checkuppicture);

        $patient = Patient::getById($patientid);
        $checkuptpl = CheckupTpl::getById($checkuptplid);

        if (false == $checkuptpl instanceof CheckupTpl) {
            echo "不存在该类型";
            return self::SUCCESS;
        }
        $checkups = CheckupDao::getListByPatientCheckupTpl($patient, $checkuptpl);

        XContext::setValue("checkuptpl", $checkuptpl);
        XContext::setValue("checkups", $checkups);
        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    public function doCheckupAddHtml() {
        $checkuptplid = XRequest::getValue("checkuptplid", 0);
        $patientid = XRequest::getValue("patientid", 0);
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);

        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        XContext::setValue("checkuppicture", $checkuppicture);

        $patient = Patient::getById($patientid);
        $checkuptpl = CheckupTpl::getById($checkuptplid);

        XContext::setValue("patient", $patient);
        XContext::setValue("checkuptpl", $checkuptpl);

        return self::SUCCESS;
    }

    public function doCheckupModifyHtml() {
        $patientid = XRequest::getValue("patientid", 0);
        $checkupid = XRequest::getValue("checkupid", 0);
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);

        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        XContext::setValue("checkuppicture", $checkuppicture);

        $patient = Patient::getById($patientid);
        $checkup = Checkup::getById($checkupid);

        XContext::setValue("patient", $patient);
        XContext::setValue("checkup", $checkup);

        return self::SUCCESS;
    }

    public function doCheckupAddPost() {
        $patientid = XRequest::getValue("patientid", 0);
        $checkuptplid = XRequest::getValue('checkuptplid', 0);
        $check_date = XRequest::getValue('check_date', date('Y-m-d'));
        $sheets = XRequest::getValue('sheets', array());
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);

        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        $patientpicture = PatientPictureDao::getByObj($checkuppicture);

        $patient = Patient::getById($patientid);

        // 记录
        $checkuptpl = CheckupTpl::getById($checkuptplid);

        $checkup = CheckupDao::getByPatientCheckupTplCheck_date($patient, $checkuptpl, $check_date);

        if ($checkup instanceof Checkup) {
            if ($checkup->xanswersheetid > 0) {
                $newsheets = array();
                foreach ($sheets['XQuestionSheet'] as $arr) {
                    $newsheets['XAnswerSheet']["{$checkup->xanswersheetid}"] = $arr;
                }
                $maxXAnswer = XWendaService::doPost($newsheets, $patient->createuser, 'Checkup', $checkup->id);
            } else {
                $maxXAnswer = XWendaService::doPost($sheets, $patient->createuser, 'Checkup', $checkup->id);
                $checkup->xanswersheetid = $maxXAnswer->xanswersheetid;
            }

        } else {
            $row = array();
            $row["patientid"] = $patient->id;
            $row["doctorid"] = $checkuptpl->doctorid; // done pcard fix
            $row["checkuptplid"] = $checkuptpl->id;
            $row["check_date"] = $check_date;

            $checkup = Checkup::createByBiz($row);
            $pipe = Pipe::createByEntity($checkup);

            $maxXAnswer = XWendaService::doPost($sheets, $patient->createuser, 'Checkup', $checkup->id);
            $checkup->xanswersheetid = $maxXAnswer->xanswersheetid;
        }

        $checkuppics = CheckupPictureDao::getListByCheckupid($checkup->id);
        if (false == empty($checkuppics)) {
            $patientpicture->outGroup();
            $patientpicture->joinGroup(PatientPictureDao::getByObj($checkuppics[0]));
        }

        $patientpicture->getMainPatientPicture()->thedate = $check_date;
        $patientpicture->setDone();
        $checkuppicture->checkupid = $checkup->id;
        echo 'ok';
        return self::BLANK;
    }

    public function doCheckupModifyPost() {
        $patientid = XRequest::getValue("patientid", 0);
        $checkupid = XRequest::getValue('checkupid', 0);
        $check_date = XRequest::getValue('check_date', date('Y-m-d'));
        $sheets = XRequest::getValue('sheets', array());
        $checkuppictureid = XRequest::getValue("checkuppictureid", 0);

        $checkuppicture = CheckupPicture::getById($checkuppictureid);
        $patient = Patient::getById($patientid);

        // 记录
        $checkup = Checkup::getById($checkupid);
        $checkup->check_date = $check_date;

        $maxXAnswer = XWendaService::doPost($sheets, $patient->createuser, 'Checkup', $checkup->id);

        $patientpicture = PatientPictureDao::getByObj($checkuppicture);
        $patientpicture->outGroup();

        $checkuppics = CheckupPictureDao::getListByCheckupid($checkupid);
        $patientpicture->joinGroup(PatientPictureDao::getByObj($checkuppics[0]));
        $patientpicture->getMainPatientPicture()->thedate = $check_date;

        $patientpicture->setDone();
        $checkuppicture->checkupid = $checkup->id;

        echo 'ok';
        return self::BLANK;
    }
}


