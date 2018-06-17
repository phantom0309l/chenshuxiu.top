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
$patient_id = $argv[1];

//催评估后3天没做，提醒
class DrugScaleNoticeLilly_3
{
    public function dowork ($patient_id) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patient_id, " and status=1 ");
        if(false == $patient_hezuo instanceof Patient_hezuo){
            echo "\n=========没有找到合作患者！";
            return;
        }

        $fromdate = date('Y-m-d', strtotime($patient_hezuo->createtime));
        $adhd_papertpl = PaperTplDao::getByEname("adhd_iv");
        $QCD_papertpl = PaperTplDao::getByEname("QCD");
        $patient = $patient_hezuo->patient;

        //如果最近一次催评估，催用药后，患者填写了（SNAP-IV评估，QCD评估，用药）跳出
        if($this->haveFinishAll($patient, $fromdate, $adhd_papertpl->id, $QCD_papertpl->id)){
            // return;
        }

        //提醒
        $this->sendmsg($patient);

        $unitofwork->commitAndInit();
        echo "\n=========成功！";
    }

    private function haveFinishAll ($patient, $date, $adhd_papertplid, $QCD_papertplid) {
        $drugitem = DrugItemDao::getByPatientid($patient->id, " and createtime>'{$date}' ");
        $paper_adhd = PaperDao::getLastByPatientidPapertplid($patient->id, $adhd_papertplid, " and createtime>'{$date}' ");
        $paper_QCD = PaperDao::getLastByPatientidPapertplid($patient->id, $QCD_papertplid, " and createtime>'{$date}' ");

        if($drugitem instanceof DrugItem && $paper_adhd instanceof Paper && $paper_QCD instanceof Paper){
            return true;
        }else {
            return false;
        }
    }

    public function sendmsg ($patient) {
        $user = $patient->createuser;
        $wxuser = $user->createwxuser;
        if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
            $doctor_name = $patient->doctor->name;
            $str = "向日葵关爱行动";
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
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patient/drug?openid={$openid}";

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }

    public function getSendContent ($patient) {
        $name = $patient->name;
        $str = "距离本次评估时间已超过三天，定期的用药和评估记录会帮助医生为孩子做出更准确的医疗决策，赶快更新记录吧。点击『详情』完成更新！";
        return $str;
    }

}

// //////////////////////////////////////////////////////

$process = new DrugScaleNoticeLilly_3(__FILE__);
$process->dowork($patient_id);
