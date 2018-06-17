<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// #4130
// 复制pcard
class Dbfix_clone_pcard
{

    public function dowork () {

        // 820 (秦蒙蒙) => 1446 (曾珍) 疾病不限
        // 32 (王迁) => 1294 (协和风湿免疫科) 疾病22

        // from
        $from_doctorid = 820;

        // new
        $new_doctorid = 1446;

        // 修正 patient->doctorid
        $fix_patient_doctorid = true;

        $cond = " and doctorid = {$from_doctorid} ";
        $pcards = Dao::getEntityListByCond('Pcard', $cond);

        foreach ($pcards as $a) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            $patient = $a->patient;
            $doctor = $a->doctor;

            echo "\n{$a->id}, {$patient->id}[{$patient->name}], {$doctor->id}, {$a->last_scan_time} ";

            // 是否已存在
            $pcard = PcardDao::getByPatientidDoctorid($patient->id, $new_doctorid);

            if ($pcard instanceof Pcard) {
                echo " == ";
            } else {

                $row = array();
                $row["last_scan_time"] = XDateTime::now();
                $row["patientid"] = $a->patientid;
                $row["doctorid"] = $new_doctorid;
                $row["diseaseid"] = $a->diseaseid;
                $row["diseasename_show"] = $a->diseasename_show;
                $row["patient_name"] = $a->patient_name;
                $row["groupstr4doctor"] = $a->groupstr4doctor;
                $row["create_doc_date"] = $a->create_doc_date;
                $row["out_case_no"] = $a->out_case_no;
                $row["patientcardno"] = $a->patientcardno;
                $row["patientcard_id"] = $a->patientcard_id;
                $row["bingan_no"] = $a->bingan_no;
                $row["fee_type"] = $a->fee_type;
                $row["scientific_no"] = $a->scientific_no;
                $row["complication"] = $a->complication;
                $row["first_happen_date"] = $a->first_happen_date;
                $row["first_visit_date"] = $a->first_visit_date;
                $row["last_incidence_date"] = $a->last_incidence_date;
                // $row["has_update"] = $a->has_update;
                // $row["lastpipeid"] = $a->lastpipeid;
                // $row["lastpipe_createtime"] = $a->lastpipe_createtime;
                $row["send_pmsheet_status"] = $a->send_pmsheet_status;
                $row["next_pmsheet_time"] = $a->next_pmsheet_time;
                $row["status"] = $a->status;
                $row["auditstatus"] = $a->auditstatus;
                $row["auditorid"] = $a->auditorid;
                $row["auditremark"] = $a->auditremark;
                $row["audittime"] = $a->audittime;
                $row["create_patientid"] = $a->create_patientid;
                // $row["remark_doctor"] = $a->remark_doctor;
                $pcard = Pcard::createByBiz($row);

                echo " ++ [{$pcard->id}]";
            }

            if ($fix_patient_doctorid) {
                echo " patient[{$patient->id}][doctorid] => {$new_doctorid} ";

                if ($patient->doctorid == $from_doctorid) {
                    $patient->set4lock("doctorid", $new_doctorid);
                    echo " [++]";
                } else {
                    echo " [--]";
                }

                $wxusers = WxUserDao::getListByPatient($patient);

                foreach ($wxusers as $w) {

                    echo " wxuser[{$w->id}][doctorid] => {$new_doctorid} ";

                    if ($w->doctorid == $from_doctorid) {
                        $w->set4lock("doctorid", $new_doctorid);
                        echo " [+]";
                    } else {
                        echo " [-]";
                    }
                }
            }

            $unitofwork->commitAndInit();
        }
    }
}

echo "\n==== begin ====\n";
$process = new Dbfix_clone_pcard();
$process->dowork();
echo "\n==== end ====\n";
