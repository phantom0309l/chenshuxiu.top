<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-4-15
 * Time: 下午7:02
 */
class LetterMgrAction extends AuditBaseAction
{

    //  感谢信列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $audit_status = XRequest::getValue('audit_status', 0);
        $show_in_doctor = XRequest::getValue('show_in_doctor', 2);
        $show_in_auditor = XRequest::getValue('show_in_auditor', 1);
        $binding_in_fc = XRequest::getValue('binding_in_fc',2);

        $bind = [];
        if ($doctorid) {
            $cond = " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        } else {
            $diseaseidstr = $this->getContextDiseaseidStr();
            $cond = " and doctorid in (
                    select DISTINCT doctorid
                    from doctordiseaserefs
                    where diseaseid in ($diseaseidstr)
               ) ";

            if($binding_in_fc == 0){
                $cond .= " and doctorid NOT IN (
                       SELECT DISTINCT c.id FROM wxusers a
                         LEFT JOIN users b ON a.userid=b.id
                         LEFT JOIN doctors c ON c.userid=b.id
                       WHERE a.wxshopid=2 AND a.nickname NOT IN(
                         SELECT c.nickname FROM auditors a
                            LEFT JOIN users b ON a.userid=b.id
                            LEFT JOIN wxusers c ON b.createwxuserid=c.id
                         WHERE c.nickname IS NOT NULL)
                       AND c.id IS NOT NULL
                    )";
            }elseif ($binding_in_fc == 1) {
                $cond .= " AND doctorid IN (
                       SELECT DISTINCT c.id FROM wxusers a
                         LEFT JOIN users b ON a.userid=b.id
                         LEFT JOIN doctors c ON c.userid=b.id
                       WHERE a.wxshopid=2 AND a.nickname NOT IN(
                         SELECT c.nickname FROM auditors a
                           LEFT JOIN users b ON a.userid=b.id
                           LEFT JOIN wxusers c ON b.createwxuserid=c.id
                         WHERE c.nickname IS NOT NULL)
                       AND c.id IS NOT NULL
                      )";
            }
        }

        if($audit_status < 2){
            $cond .= " and audit_status = :audit_status ";
            $bind[':audit_status'] = $audit_status;
        }

        if($show_in_doctor < 2){
            $cond .= " and show_in_doctor = :show_in_doctor ";
            $bind[':show_in_doctor'] = $show_in_doctor;
        }

        if($show_in_auditor < 2){
            $cond .= " and show_in_auditor = :show_in_auditor ";
            $bind[':show_in_auditor'] = $show_in_auditor;
        }

        $cond .= " and (userid <10000 or userid > 20000) ";

        $letters = Dao::getEntityListByCond4Page('Letter', $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(id) as cnt from letters where 1=1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/lettermgr/list?doctorid={$doctorid}&audit_status={$audit_status}&show_in_doctor={$show_in_doctor}&show_in_auditor={$show_in_auditor}&binding_in_fc={$binding_in_fc}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("audit_status", $audit_status);
        XContext::setValue("show_in_doctor", $show_in_doctor);
        XContext::setValue("show_in_auditor", $show_in_auditor);
        XContext::setValue("binding_in_fc", $binding_in_fc);

        XContext::setValue("letters", $letters);
        XContext::setValue("pagelink", $pagelink);


        return self::SUCCESS;
    }

    public function doAddJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $pipeid = XRequest::getValue("pipeid", 0);
        $typestr = XRequest::getValue("typestr", "");
        $content = XRequest::getValue("content", "");

        if ($patientid) {
            $patient = Patient::getById($patientid);
            $pipe = Pipe::getById( $pipeid );

            if( $pipe instanceof Pipe && $pipe->canJoinLetter()){
                $row = array();
                $row['wxuserid'] = $pipe->wxuserid;
                $row['userid'] = $pipe->userid;
                $row['patientid'] = $patientid;
                $row['doctorid'] = $patient->doctorid; // done pcard fix
                $row['typestr'] = $typestr;
                $row['content'] = $content;
                $row['objtype'] = $pipe->objtype;
                $row['objid'] =  $pipe->obj->id;
                Letter::createByBiz($row);
            }
            echo "ok";
        }

        return self::blank;
    }

    public function doModifyShowInDoctorJson () {
        $letterid = XRequest::getValue("letterid", 0);
        $showindoctor = XRequest::getValue("showindoctor", 0);

        $letter = Letter::getById( $letterid );
        if( $letter instanceof Letter ){
            $letter->audit_status = 1;
            $letter->audit_time = XDateTime::now();
            $letter->show_in_doctor = $showindoctor;
        }
        echo "ok";
        return self::blank;
    }

    public function doModifyShowInAuditorJson () {
        $letterid = XRequest::getValue("letterid", 0);
        $showinauditor = XRequest::getValue("showinauditor", 0);

        $letter = Letter::getById( $letterid );
        if( $letter instanceof Letter ){
            $letter->show_in_auditor = $showinauditor;
        }
        echo "ok";
        return self::blank;
    }
}
