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
class ScaleNotice_pkuh6 extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:40 按周期, 发送六院管理计划评估';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select distinct a.id from patients a
                    inner join mgtgrouptpls b on b.id = a.mgtgrouptplid
                    inner join patientmedicinerefs c on c.patientid = a.id
                    where a.diseaseid=1 and a.status=1 and b.ename = 'pkuh6' and c.medicineid in (2,3) and c.first_start_date > :first_start_date";
        $bind = array();
        $thedate = date("Y-m-d", time() - 86400 * 83);
        $bind[":first_start_date"] = $thedate;
        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 20) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                echo "\n===patientid[1][{$id}]===\n";
                if (false == $this->canSendMsg($patient)) {
                    continue;
                }

                echo "\n===patientid[2][{$id}]===\n";
                $this->sendmsg($patient);

                $this->createOptask($patient);
            }
        }
        $unitofwork->commitAndInit();
    }

    public function createOptask ($patient) {

        // 两天后任务
        $plantime = date("Y-m-d", time() + 86400 * 2);

        // 生成任务: 基础评估任务
        OpTaskService::createPatientOpTask($patient, 'baseScale:', null, $plantime, 1);
    }

    public function canSendMsg ($patient) {
        $today_date = date("Y-m-d", time());
        $done_cnt = $this->getPaperCntOfThedateByPatientid($patient->id, $today_date);

        //如果今天已经做过量表，不再催
        if($done_cnt >= 3){
            return false;
        }

        $startdate = $this->getPatientmedicineStartdate($patient);
        if (empty($startdate)) {
            return false;
        }
        $diff = XDateTime::getDateDiff($today_date, $startdate);

        $arr = array(
            5,
            12,
            19,
            26,
            40,
            54,
            82);
        if (in_array($diff, $arr)) {
            return true;
        } else {
            return false;
        }
    }

    private function getPaperCntOfThedateByPatientid ($patientid, $thedate) {
        $sql = "select count(*) as cnt
            from papers
            where patientid = :patientid and createtime > :startdate and createtime < :enddate";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':startdate'] = $thedate;
        $bind[':enddate'] = date("Y-m-d", strtotime($thedate) + 86400);

        return Dao::queryValue($sql, $bind);
    }

    // 取择思达、专注达较晚的开始服药日期
    private function getPatientmedicineStartdate ($patient) {
        $pmedicine1 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 2);
        $pmedicine2 = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 3);
        $first_start_date1 = "";
        $first_start_date2 = "";
        if ($pmedicine1 instanceof PatientMedicineRef) {
            $first_start_date1 = $pmedicine1->first_start_date;
        }

        if ($pmedicine2 instanceof PatientMedicineRef) {
            $first_start_date2 = $pmedicine2->first_start_date;
        }

        if ("0000-00-00" == $first_start_date1 && "0000-00-00" == $first_start_date2) {
            return "";
        }

        if ($first_start_date1 >= $first_start_date2) {
            return $first_start_date1;
        } else {
            return $first_start_date2;
        }
    }

    public function sendmsg ($patient) {
        $wxusers = $patient->getWxUsers();
        foreach ($wxusers as $wxuser) {
            if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
                $doctor_name = $patient->doctor->name;
                $str = "{$doctor_name}医生助理";

                $content = $this->getSendContent($patient);

                $first = array(
                    "value" => "",
                    "color" => "#ff6600");
                $keywords = array(
                    array(
                        "value" => $str,
                        "color" => "#aaa"),
                    array(
                        "value" => $content,
                        "color" => "#ff6600"));
                $content = WxTemplateService::createTemplateContent($first, $keywords);

                $openid = $wxuser->openid;

                $url = $this->getSendUrl($openid);

                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
        }
    }

    private function getSendContent ($patient) {
        $name = $patient->name;
        $str = "{$name}家长，用药和孩子情况是治疗中的重要参考依据。为了解孩子用药和变化情况，请及时更新用药记录并完成SNAP-IV、治疗反应问卷和副反应评估量表。点击『详情』完成更新。";
        return $str;
    }

    private function getSendUrl ($openid) {
        $wx_uri = Config::getConfig("wx_uri");
        $url = $wx_uri . "/paper/indexAdhd?openid={$openid}";
        return $url;
    }
}

// //////////////////////////////////////////////////////

$process = new ScaleNotice_pkuh6(__FILE__);
$process->dowork();
