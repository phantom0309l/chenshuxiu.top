<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class GetData_Dependence
{

    public function dowork () {

        echo "\n [GetData_Dependence] begin ";

        $this->setPatientids();
        $this->getPatient_Drug();
        $this->getPatient_Assesss();
        $this->getPatient_Lesson();

        echo "\n [GetData_Dependence] finished \n";

    }

    private $patientids = array();

    private function write2file ($filename, $dataarr) {
        $file = fopen("/home/xuzhe/dev/fangcunyisheng.com/cron/xuzhe/fcqx_yicongxing/calcfunctions/{$filename}.py", 'w+');
        $filecontent = "# -*- coding: utf-8 -*- \n{$filename}={\n";

        foreach ($dataarr as $patientid => $data) {
            $filecontent .= "{$patientid}:{$data},\n";
        }
        $filecontent .= "}";
        fwrite($file, $filecontent);
    }

    private function setPatientids () {
        $ids = Doctor::getTestDoctorIdStr();
        $sql = "select distinct a.id
            from patients a
            inner join pcards b on b.patientid=a.id
            where b.doctorid not in ({$ids}) and b.diseaseid=1 and a.status=1 and a.subscribe_cnt>0 ";
        $this->patientids = Dao::queryValues($sql);

        $patientids = $this->patientids;

        $dataarr = array();
        foreach ($patientids as $index => $patientid) {
            $now = date("H:i:s");
            echo "\n [GetData_Dependence][{$now}] patient_name {$index} : {$patientid} ";

            $patient = Patient::getById($patientid);
            $dataarr["'{$patientid}'"] = "'{$patient->name}'";

        }

        $this->write2file('patient_name_data', $dataarr);
    }

    private function getPatient_Assesss () {

        $patientids = $this->patientids;

        $dataarr = array();
        foreach ($patientids as $index => $patientid) {
            $now = date("H:i:s");
            echo "\n [GetData_Dependence][{$now}] patient_assess_data {$index} : {$patientid} \n";

            $patient = Patient::getById($patientid);

            $sql = " SELECT count(*) FROM comments WHERE content='患者 {$patient->name} ID {$patient->id} 已发送催评估消息' ";

            $account = Dao::queryValue($sql);

            $count = 0;

            $cond = " and content='患者 {$patient->name} ID {$patient->id} 已发送催评估消息' ";
            $comments = Dao::getEntityListByCond('Comment', $cond);
            foreach ($comments as $comment) {
                $fromdate = date('Y-m-d', strtotime($comment->createtime));
                $todate = XDateTime::getNewDate($fromdate, 2);

                echo "{$fromdate}=>{$todate} | ";
                $sql = " SELECT count(*) FROM papers WHERE groupstr='scale' and patientid={$patientid}
                        and createtime >= '{$fromdate}' and createtime <= '{$todate}' ";

                $count += Dao::queryValue($sql);
            }

            $account *= 3;

            if ($account > 0) {
                $value = $count / $account;
                if ($value > 1) {
                    $value = 1;
                }
            } else {
                $value = 0.5;
            }
            $dataarr["'{$patientid}'"] = "{$value}";

        }

        $this->write2file('patient_assess_data', $dataarr);
    }

    private function getPatient_Lesson () {

        $patientids = $this->patientids;

        $courseidsstr = "(100713315,100761037,100761243,100761253)";
        $dataarr = array();
        foreach ($patientids as $index => $patientid) {
            $now = date("H:i:s");
            echo "\n [GetData_Dependence][{$now}] patient_lesson_data {$index} : {$patientid} ";

            $patient = Patient::getById($patientid);

            $cond = " and courseid in {$courseidsstr} and patientid = {$patientid} ";

            $lessonuserrefs = Dao::getEntityListByCond("LessonUserRef", $cond);

            $account = 0;
            $count = 0;

            foreach ($lessonuserrefs as $ref) {
                $account ++;
                $sql = " select count(*) from xanswersheets where patientid={$patientid} and objtype='LessonUserRef' and objid={$ref->id}";
                $count += Dao::queryValue($sql);
            }
            $account *= 2;

            if ($account > 0) {
                $value = $count / $account;
                if ($value > 1) {
                    $value = 1;
                }
            } else {
                $value = 0.5;
            }
            $dataarr["'{$patientid}'"] = "{$value}";

        }

        $this->write2file('patient_lesson_data', $dataarr);
    }

    private function getPatient_Drug () {

        $patientids = $this->patientids;

        $dataarr = array();
        foreach ($patientids as $index => $patientid) {
            $now = date("H:i:s");
            echo "\n [GetData_Dependence][{$now}] patient_drug_data {$index} : {$patientid} ";

            $patient = Patient::getById($patientid);

            $drugitem = Dao::getEntityByCond("DrugItem", " and patientid={$patientid} order by createtime asc limit 1");

            $value = 0;
            if (false == $drugitem instanceof DrugItem) {
                $dataarr["'{$patientid}'"] = "{$value}";
                continue;
            }

            $sql = " SELECT count(*) FROM comments WHERE content='患者 {$patient->name} ID {$patient->id} 已发送催评估消息' ";

            $account = Dao::queryValue($sql);

            $count = 0;

            $cond = " and content='患者 {$patient->name} ID {$patient->id} 已发送催评估消息' ";
            $comments = Dao::getEntityListByCond('Comment', $cond);
            foreach ($comments as $comment) {
                $fromdate = date('Y-m-d', strtotime($comment->createtime));
                $todate = XDateTime::getNewDate($fromdate, 1);

                $sql = " select COUNT(*) from (select * from drugitems where patientid={$patientid} and createtime >= '{$fromdate}' and createtime <= '{$todate}'
                          group by TO_DAYS(createtime)) tt ";

                $count += Dao::queryValue($sql);
            }

            if ($account > 0) {
                $value = $count / $account;
                if ($value > 1) {
                    $value = 1;
                }
            } else {
                $value = 00.5;
            }

            $dataarr["'{$patientid}'"] = "{$value}";

        }

        $this->write2file('patient_drug_data', $dataarr);
    }

}

$process = new GetData_Dependence();
$process->dowork();
