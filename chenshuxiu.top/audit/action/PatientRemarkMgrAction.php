<?php
// PatientRemarkMgrAction
class PatientRemarkMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagenum = XRequest::getValue('pagenum', 1);
        $pagesize = XRequest::getValue('pagesize', 50);

        $patientid = XRequest::getValue('patientid', 0);
        $diseaseid = XRequest::getValue('_diseaseid_', 0);
        $doctorid = XRequest::getValue('doctorid', 0);
        $typestr = XRequest::getValue('typestr', '');
        $name = XRequest::getValue('name', '');
        $thedate = XRequest::getValue('thedate', '');
        $revisitrecordid = XRequest::getValue('revisitrecordid', '');

        $cond = '';
        $bind = [];
        $url = '';

        if ($patientid) {
            $url .= "&patientid={$patientid}";
            $cond .= " and patientid = :patientid ";
            $bind[':patientid'] = $patientid;
        }

        if ($doctorid) {
            $url .= "&doctorid={$doctorid}";
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($typestr) {
            $url .= "&typestr={$typestr}";
            $cond .= " and typestr = :typestr ";
            $bind[':typestr'] = $typestr;
        }

        if ($name) {
            $url .= "&name={$name}";
            $cond .= " and name = :name ";
            $bind[':name'] = $name;
        }

        if ($thedate) {
            $url .= "&thedate={$thedate}";
            $cond .= " and thedate = :thedate ";
            $bind[':thedate'] = $thedate;
        }

        if ($revisitrecordid) {
            $url .= "&revisitrecordid={$revisitrecordid}";
            $cond .= " and revisitrecordid = :revisitrecordid ";
            $bind[':revisitrecordid'] = $revisitrecordid;
        }

        $cond .= " order by createtime desc ";

        $patientremarks = Dao::getEntityListByCond4Page('PatientRemark', $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(*) from patientremarks where 1 = 1 " .  $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/patientremarkmgr/list?diseaseid={$diseaseid}" . $url;
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('patientremarks', $patientremarks);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
