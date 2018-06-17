<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Rpt_patient_init
{

    public function dowork () {

        $thetime = strtotime('2016-11-01');
        $endtime = strtotime('2016-11-15');

        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        while ($thetime < $endtime) {

            $thedate = date("Y-m-d", $thetime);

            echo "========={$thedate}=============\n";

            $rptids = $this->getListByDate($thedate);

            if(count($rptids) > 0){
                foreach($rptids as $rptid){
                    $rpt = Rpt_patient::getById($rptid, 'statdb');
                    $this->doRpt_patient($rpt);

                    echo "\n======================\n".$rpt->id;
                    if($i == 1000){
                        $unitofwork->commitAndInit();
                        $unitofwork = BeanFinder::get("UnitOfWork");
                        $i = 0;
                    }

                    $i++;
                }
            }

            $thetime = $thetime + 86400;
        }

        $unitofwork->commitAndInit();
    }

    private function getListByDate ($thedate) {
        $sql = " select id from rpt_patients where 1=1 ";

        $sql .= " and thedate = :thedate ";
        $bind = array(
            ':thedate' => $thedate);

        return Dao::queryValues($sql, $bind, 'statdb');
    }

    private function doRpt_patient($rpt){
        $patientid = $rpt->patientid;
        $patient = $rpt->patient;

        if($patient instanceof Patient){
            $thedate = $rpt->thedate;

            //给rpt_patients修补数据
            $rpt->isscan = $this->getIsscan($patient, $thedate);
            $rpt->baodaodate = substr($patient->createtime, 0, 10);
            $rpt->patient_daycnt_lifecycle = $this->getLifecycleOfPatient($patient);
            $rpt->patient_status = $patient->status;

            $sql = " select count(*) from pipes where left(createtime, 10)='{$thedate}' and patientid='{$patientid}' ";
            $rpt->drug_status = $this->getDrugstatusByPatientThedate($patient, $thedate);
            $rpt->drugitem_cnt = Dao::queryValue(" select count(*) from drugitems where left(createtime, 10)='{$thedate}' and patientid='{$patientid}' and medicineid>0 and type=1 ");

            $pipedataArr = $this->getPipeData($thedate, $patientid);

            $paper_cnt=0;
            $wxvoicemsg_cnt=0;
            $lessonuserref_hwk_cnt=0;
            $lessonuserref_test_cnt=0;
            $comment_share_cnt=0;
            if(count($pipedataArr) > 0){
                foreach($pipedataArr as $item){
                    if($item["objtype"] == "Paper"){
                        $paper_cnt = $item["cnt"];
                        continue;
                    }
                    if($item["objtype"] == "WxVoiceMsg"){
                        $wxvoicemsg_cnt = $item["cnt"];
                        continue;
                    }
                    if($item["objtype"] == "LessonUserRef" && $item["objcode"] == "hwk"){
                        $lessonuserref_hwk_cnt = $item["cnt"];
                        continue;
                    }
                    if($item["objtype"] == "LessonUserRef" && $item["objcode"] == "test"){
                        $lessonuserref_test_cnt = $item["cnt"];
                        continue;
                    }
                    if($item["objtype"] == "Comment" && $item["objcode"] == "share"){
                        $comment_share_cnt = $item["cnt"];
                        continue;
                    }
                }
            }

            $rpt->paper_cnt = $paper_cnt;
            $rpt->wxvoicemsg_cnt = $wxvoicemsg_cnt;
            $rpt->lessonuserref_hwk_cnt = $lessonuserref_hwk_cnt;
            $rpt->lessonuserref_test_cnt = $lessonuserref_test_cnt;
            $rpt->comment_share_cnt = $comment_share_cnt;

        }
    }

    private function getIsscan($patient, $thedate){
        $isscan=0;
        $sql = " SELECT a.* FROM wxusers a
            INNER JOIN users b ON a.userid=b.id
            WHERE a.wx_ref_code!='' AND a.ref_objtype='Doctor'
            AND b.patientid={$patient->id} AND LEFT(a.createtime, 10)<='{$thedate}' ";
        $wxuser = Dao::loadEntity("WxUser", $sql);

        if($wxuser instanceof WxUser){
            $isscan = 1;
        }

        return $isscan;
    }

    private function getLifecycleOfPatient($patient){
        $sql = " SELECT
              (unix_timestamp(c.unsubscribe_time)-unix_timestamp(a.createtime))  as subtime
            FROM patients a
            INNER JOIN users b ON b.patientid = a.id
            INNER JOIN wxusers c ON c.userid = b.id
            WHERE a.id={$patient->id} ";
        $subtime = Dao::queryValue($sql);

        if($subtime>=0){
            //floor向下舍入最接近的整数，取关时间与报到时间间隔小于24小时，记 $patientdaycnt 为0
            $lifecycle = floor($subtime / 86400);
        }else{
            $lifecycle = -1;
        }

        return $lifecycle;
    }

    private function getDrugstatusByPatientThedate($patient, $thedate){
//        用药状态,0:无填写记录，1：用药，2：不服药，3：停药
        $status=0;
        $cond = " AND left(createtime, 10)<='{$thedate}' AND patientid = :patientid ";
        $bind = array(
            ":patientid" => $patient->id);
        $refs = Dao::getEntityListByCond("PatientMedicineRef", $cond, $bind);

        $cnt = count($refs);
        if($cnt > 0){
            foreach($refs as $k => $ref){
                if($ref->medicineid>0 && $ref->status>0){
                    $status=1;
                    break;
                }
                if($cnt==$k+1 && $ref->medicineid==0){
                    $status=2;
                    break;
                }
            }
            if($status == 0){
                $status = 3;
            }
        }

        return $status;
    }

    private function getPipeData($thedate, $patientid){
        $sql = " SELECT objtype, objcode, count(*) as cnt
             FROM pipes
             where left(createtime, 10)='{$thedate}' and patientid='{$patientid}'
             GROUP BY objtype, objcode ";

        return Dao::queryRows($sql);
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Rpt_patient_init.php]=====");

$process = new Rpt_patient_init();
$process->dowork();

Debug::trace("=====[cron][end][Rpt_patient_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
