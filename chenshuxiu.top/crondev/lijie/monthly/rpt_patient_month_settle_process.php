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

class Rpt_patient_month_settle_process
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $patientidsarr = $this->getPassedPatientidsArr();

        $pipecntbypatient = 0;

        // 跑脚本时间
        $date = date("Y-m", time());

        $themonth = date("Y-m-d", strtotime("last month", strtotime($date)));
        echo "\n\n---------================================================----- " . $themonth;
        // 截取的年月

        foreach ($patientidsarr as $k => $v) {
            $id = $v["id"];

            echo "\n\n-----begin----- " . $id;

            $patient = Patient::getById($id);

            if($patient instanceof Patient){
                //isscan用来记录是否为扫码患者
                $isscan = $this->getIsscanByPatient($patient);

                //取出取关时间和报到时间的差值
                //取关时间大于报到时间，把天数差值记录下来
                //取关时间小于报到时间或者没有取关时间，$patientdaycnt记为空
                $subtime = $v["subtime"];
                if($subtime>=0){
                    //floor向下舍入最接近的整数，取关时间与报到时间间隔小于24小时，记 $patientdaycnt 为0
                    $patientdaycnt = floor($subtime / 86400);
                }else{
                    $patientdaycnt = "";
                }

                // 脚本统计上月数据的　年月
                $themonthshort = substr($themonth, 0, 7);
                // 患者报到时间的　年月
                //为什么不用
                //
                $baodao_month = substr($patient->createtime, 0, 7);

                //患者报到时间应该小于或等于统计月
                if (strtotime($baodao_month) > strtotime($themonthshort)) {
                    continue;
                }
                $pipecnt = $patient->getPipecntByDateYm($themonthshort);

                //患者从报到至现在的受管理月数
                $month_pos = XDateTime::getDateDiffOfMonth($baodao_month, $date);

                echo "............................." . $month_pos;

                $row["patientid"] = $patient->id;
                $row["isscan"] = $isscan;
                $row["patientstatus"] = $patient->status;
                $row["patientdaycnt"] = $patientdaycnt;
                $row["doctorid"] = $patient->doctorid;
                $row["themonth"] = $themonth;
                $row["baodaodate"] = substr($patient->createtime, 0, 10);
                $row["pipecntbypatient"] = $pipecnt;
                $row["month_pos"] = $month_pos;

                $rpt_patient_month_settle = Rpt_patient_month_settleDao::getByPatientidAndDateYmd($patient->id, $themonth);

                if (false == ($rpt_patient_month_settle instanceof Rpt_patient_month_settle)) {
                    $entity = Rpt_patient_month_settle::createByBiz($row);
                }

            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }

    private function getPassedPatientidsArr () {
        $sql = " SELECT a.id as id, c.wx_ref_code as wx_ref_code, c.ref_objtype as ref_objtype,
              (unix_timestamp(c.unsubscribe_time)-unix_timestamp(a.createtime))  as subtime
            FROM patients a
            INNER JOIN users b ON b.patientid = a.id
            INNER JOIN wxusers c ON c.userid = b.id
            WHERE a.status=1 AND c.wxshopid=1
                AND (b.id < 10000 OR b.id > 20000 )
                AND a.doubt_type=0
            GROUP BY a.id ";
        return Dao::queryRows($sql);
    }

    private function getIsscanByPatient($patient){
        $isscan = 0;
        $users = $patient->getUsers();
        if(count($users) > 0){
            foreach($users as $user){
                $wxuser = $user->getMasterWxUser(1);
                if($wxuser instanceof WxUser && $wxuser->wx_ref_code && $wxuser->ref_objtype=='Doctor'){
                    $isscan = 1;
                    break;
                }
            }
        }
        return $isscan;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Rpt_patient_month_settle_process.php]=====");

$process = new Rpt_patient_month_settle_process();
$process->dowork();

Debug::trace("=====[cron][end][Rpt_patient_month_settle_process.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
