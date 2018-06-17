<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Output_5408
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $arr_range = [
            [0,28],[29,56],[57,84],[85,112],[113,140],[141,168]
        ];

        $sql = "select id from patients where status=1 and doctorid=2";
        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            echo "[{$id}]\n";
            $i ++;
            if ($i > 0 && $i % 50 == 0) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $patient = Patient::getById($id);
            if( $patient instanceof Patient ){
                $temp = array();
                $temp[] = $patient->id;
                $temp[] = $patient->name;
                $temp[] = substr($patient->createtime, 0, 10);

                $d = $patient->getDayCntFromBaodao();
                $temp[] = $d;

                $pmedicine1 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 2);
                $is_zesida = $pmedicine1 instanceof PatientMedicineRef ? 1 : 0;
                $temp[] = $is_zesida ? "是" : "否";

                $pmedicine2 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 3);
                $is_zhuanzuda = $pmedicine2 instanceof PatientMedicineRef ? 1 : 0;
                $temp[] = $is_zhuanzuda ? "是" : "否";

                $last_drugchange_date = $this->getPatientmedicineLast_drugchange_date($patient);
                $temp[] = $last_drugchange_date;

                //判断两个药是否都有且最后更新时间一致
                $last_drugchange_date1 = $pmedicine1->last_drugchange_date;
                $last_drugchange_date2 = $pmedicine2->last_drugchange_date;
                if( $is_zesida && $is_zhuanzuda && ($last_drugchange_date1 == $last_drugchange_date2) ){
                    $temp[] = $pmedicine1->medicine->name . "\n" . $pmedicine2->medicine->name;
                    $temp[] = $pmedicine1->medicine->name . "[{$pmedicine1->value}]\n" . $pmedicine2->medicine->name . "[{$pmedicine2->value}]";
                }else{
                    $pmedicine = $this->getLastPatientmedicine($patient);
                    $temp[] = $pmedicine instanceof PatientMedicineRef ? $pmedicine->medicine->name : "";
                    $temp[] = $pmedicine instanceof PatientMedicineRef ? $pmedicine->value : "";
                }

                foreach($arr_range as $item){
                    $temp[] = $this->getScaleCnt($patient, $item[0], $item[1]);
                    $temp[] = $this->getHomeWorkCnt($patient, $item[0], $item[1]);
                    $temp[] = $this->getWxTxtMsgCnt($patient, $item[0], $item[1]);
                }

                $data[] = $temp;
            }
        }
        $headarr = array(
            "patientid",
            "患者姓名",
            "报到日期",
            "报到天数",
            "用药记录中是否包含择思达",
            "用药记录中是否包含专注达",
            "最后一次更新用药（包含专注达/择思达）日期",
            "最后一次更新用药（包含专注达/择思达）药物种类",
            "最后一次更新用药（包含专注达/择思达）药物剂量",
            "在报到0-28天完成常规评估次数",
            "在报到0-28天时是否提交行为训练作业",
            "在报到0-28天是否有微信提问",
            "在报到29-56天完成常规评估次数",
            "在报到29-56天时是否提交行为训练作业",
            "在报到29-56天是否有微信提问",
            "在报到57-84天完成常规评估次数",
            "在报到57-84天时是否提交行为训练作业",
            "在报到57-84天是否有微信提问",
            "在报到85-112天完成常规评估次数",
            "在报到85-112天时是否提交行为训练作业",
            "在报到85-112天是否有微信提问",
            "在报到113-140天完成常规评估次数",
            "在报到113-140天时是否提交行为训练作业",
            "在报到113-140天是否有微信提问",
            "在报到141-168天完成常规评估次数",
            "在报到141-168天时是否提交行为训练作业",
            "在报到141-168天是否有微信提问",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_5408.xlsx");
        $unitofwork->commitAndInit();
    }

    // 取择思达、专注达最近一次更新用药日期
    private function getPatientmedicineLast_drugchange_date ($patient) {
        $pmedicine1 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 2);
        $pmedicine2 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 3);
        $last_drugchange_date1 = "";
        $last_drugchange_date2 = "";
        if ($pmedicine1 instanceof PatientMedicineRef) {
            $last_drugchange_date1 = $pmedicine1->last_drugchange_date;
        }

        if ($pmedicine2 instanceof PatientMedicineRef) {
            $last_drugchange_date2 = $pmedicine2->last_drugchange_date;
        }

        if (empty($last_drugchange_date1) && empty($last_drugchange_date2)) {
            return "";
        }

        if ($last_drugchange_date1 >= $last_drugchange_date2) {
            return $last_drugchange_date1;
        } else {
            return $last_drugchange_date2;
        }
    }

    // 取择思达、专注达最近一次更新用药日期
    private function getLastPatientmedicine ($patient) {
        $pmedicine1 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 2);
        $pmedicine2 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 3);
        $last_drugchange_date1 = "";
        $last_drugchange_date2 = "";
        if ($pmedicine1 instanceof PatientMedicineRef) {
            $last_drugchange_date1 = $pmedicine1->last_drugchange_date;
        }

        if ($pmedicine2 instanceof PatientMedicineRef) {
            $last_drugchange_date2 = $pmedicine2->last_drugchange_date;
        }

        if (empty($last_drugchange_date1) && empty($last_drugchange_date2)) {
            return "";
        }

        if ($last_drugchange_date1 >= $last_drugchange_date2) {
            return $pmedicine1;
        } else {
            return $pmedicine2;
        }
    }

    //获取做常规评估数
    private function getScaleCnt($patient, $day_l, $day_r){
        $createtime = $patient->createtime;
        $startdate = date("Y-m-d", strtotime($createtime) + $day_l*86400);
        $enddate = date("Y-m-d", strtotime($createtime) + ($day_r+1)*86400);
        $d = $patient->getDayCntFromBaodao();
        $bind = array();
        $bind[":patientid"] = $patient->id;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;

        $sql = "select count(*) as cnt
                from papers
                where ename = 'adhd_iv' and patientid = :patientid and createtime >= :startdate and createtime < :enddate";
        return 0 + Dao::queryValue($sql, $bind);
    }

    //获取行为训练作业数
    private function getHomeWorkCnt($patient, $day_l, $day_r){
        $createtime = $patient->createtime;
        $startdate = date("Y-m-d", strtotime($createtime) + $day_l*86400);
        $enddate = date("Y-m-d", strtotime($createtime) + ($day_r+1)*86400);
        $d = $patient->getDayCntFromBaodao();
        $bind = array();
        $bind[":patientid"] = $patient->id;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;

        $sql = "select count(a.id) as cnt
                from studys a
                inner join studyplans b on b.id = a.studyplanid
                where b.objcode='hwk' and a.patientid = :patientid and a.createtime >= :startdate and a.createtime < :enddate";
        return 0 + Dao::queryValue($sql, $bind);
    }

    //获取微信提问
    private function getWxTxtMsgCnt($patient, $day_l, $day_r){
        $createtime = $patient->createtime;
        $startdate = date("Y-m-d", strtotime($createtime) + $day_l*86400);
        $enddate = date("Y-m-d", strtotime($createtime) + ($day_r+1)*86400);
        $d = $patient->getDayCntFromBaodao();
        $bind = array();
        $bind[":patientid"] = $patient->id;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;

        $sql = "select count(*) as cnt
                from wxtxtmsgs
                where patientid = :patientid and createtime >= :startdate and createtime < :enddate";
        return 0 + Dao::queryValue($sql, $bind);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5408.php]=====");

$process = new Output_5408();
$process->dowork();

Debug::trace("=====[cron][end][Output_5408.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
