<?php
// DoctordbOplogMgrAction
class DoctordbOplogMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $word = XRequest::getValue('word', '');

        $cond = " and doctorid = :doctorid ";
        $bind = [];
        $bind[':doctorid'] = $doctorid;

        if ($word) {
            if (is_numeric($word)) {
                $cond .= " and patientid like :word ";
            } else {
                $cond .= " and patientid in (
                        select id
                        from patients
                        where doctorid = :doctorid and name like :word ) ";
            }
            $bind[':word'] = "%{$word}%";
        }

        $cond .= " order by createtime desc ";

        $doctordboplogs = Dao::getEntityListByCond4Page("DoctordbOplog", $pagesize, $pagenum, $cond, $bind);

        // 翻页begin
        $countSql = "select count(*) as cnt from doctordboplogs where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctordboplogmgr/list?word={$word}&doctorid={$doctorid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('doctordboplogs', $doctordboplogs);
        XContext::setValue('word', $word);

        return self::SUCCESS;
    }
}
