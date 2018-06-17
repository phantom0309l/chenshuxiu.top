<?php

// RevisitRecordMgrAction
class RevisitRecordMgrAction extends AuditBaseAction
{

    // 门诊列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 30);
        $pagenum = XRequest::getValue("pagenum", 1);
        $isclick = XRequest::getValue('isclick', 0);
        $revisitrecordid = XRequest::getValue('revisitrecordid', 0);

        XContext::setValue('isclick', $isclick);
        XContext::setValue('revisitrecordid', $revisitrecordid);

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

        $diseaseidstr = $this->getContextDiseaseidStr();

        $cond .= " and p.diseaseid in ($diseaseidstr) ";


        if ($doctorid) {
            $cond .= " and rr.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($patient_name) {
            $cond .= " and  p.patient_name like :name ";
            $bind[':name'] = "%{$patient_name}%";
        }

        if ($patientid) {
            $cond .= " and  p.patientid = :patientid ";
            $bind[':patientid'] = "{$patientid}";
        }

        $cond .= " order by rr.createtime desc ";

        $sql = "select distinct rr.*
                from revisitrecords rr
                inner join pcards p on p.patientid = rr.patientid
                where 1=1 " . $cond;

        $revisitrecords = Dao::loadEntityList4Page('RevisitRecord', $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $countSql = "select count( distinct rr.id ) as cnt
                      from revisitrecords rr
                      inner join pcards p on p.patientid = rr.patientid
                      where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/revisitrecordmgr/list?doctorid={$doctorid}&patient_name={$patient_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue('revisitrecords', $revisitrecords);

        return self::SUCCESS;
    }

    public function doOneHtml () {
        $revisitrecordid = XRequest::getValue('revisitrecordid', 0);

        $revisitrecord = RevisitRecord::getById($revisitrecordid);

        XContext::setValue('revisitrecord', $revisitrecord);

        return self::SUCCESS;
    }

    public function doAdd () {
        $patientid = XRequest::getValue('patientid', 0);

        $patient = Patient::getById($patientid);

        XContext::setValue("patient", $patient);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $patientid = XRequest::getValue('patientid', 0);
        $thedate = XRequest::getValue('thedate', '0000-00-00');
        $content = XRequest::getValue('content', '');

        if ($thedate == '0000-00-00') {
            $thedate = date('Y-m-d');
        }

        $patient = Patient::getById($patientid);

        $revisitrecord = RevisitRecordDao::getByPatientidThedate($patientid, $thedate);

        if (false == $revisitrecord instanceof RevisitRecord) {
            $row = array();
            $row['patientid'] = $patientid;
            $row['doctorid'] = $patient->doctorid;
            $row['thedate'] = $thedate;
            $row['content'] = $content;

            $revisitrecord = RevisitRecord::createByBiz($row);

            $pipe = Pipe::createByEntity($revisitrecord);
        }

        XContext::setJumpPath("/revisitrecordmgr/list");

        return self::SUCCESS;
    }

    public function doModify () {
        $revisitrecordid = XRequest::getValue('revisitrecordid', 0);
        $revisitrecord = RevisitRecord::getById($revisitrecordid);

        XContext::setValue('revisitrecord', $revisitrecord);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $revisitrecordid = XRequest::getValue('revisitrecordid', 0);
        $revisitrecord = RevisitRecord::getById($revisitrecordid);

        $thedate = XRequest::getValue('thedate', '0000-00-00');
        $content = XRequest::getValue('content', '');

        $revisitrecord->thedate = $thedate;
        $revisitrecord->content = $content;

        $preMsg = "修改已提交 " . XDateTime::now();
        XContext::setJumpPath("/revisitrecordmgr/modify?revisitrecordid={$revisitrecordid}&preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $revisitrecordid = XRequest::getValue('revisitrecordid', 0);
        $revisitrecord = RevisitRecord::getById($revisitrecordid);

        $revisitrecord->remove();
        echo "success";

        return self::BLANK;
    }

    public function doRevisitrecord_calendar () {
        return self::SUCCESS;
    }
}
