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
class Lilly_drugscalenotice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:40 按周期, 发送合作患者的 填写服药记录和SNAP-IV评估的提醒以及催评估后3天未做提醒，7天未做生成运营任务，14天未做再提醒，21天未做置不活跃退出';
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

        $sql = "select id from patient_hezuos where status=1 and company='Lilly'";
        // $sql = "select id from patient_hezuos where patientid=278143256 ";

        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo "\n====id[{$id}]===" . XDateTime::now();

            $patient_hezuo = Patient_hezuo::getById($id);
            $diff = $patient_hezuo->getDayCntFromCreate();
            echo "\n====diff[{$diff}]===";
            if ($diff < 7) {
                continue;
            }

            $patient = $patient_hezuo->patient;
            if (false == $patient instanceof Patient) {
                continue;
            }

            // 出组逻辑要写在催用药、评估前面，在患者报到28天的时候，要先判断是否出组然后再催
            // if($diff == 28){
            // $fromdate = date('Y-m-d', strtotime($patient_hezuo->createtime) +
            // 7 * 86400);
            // $enddate = XDateTime::now();
            // $isFinishedDrugAndPaper =
            // $patient_hezuo->isFinishedDrugAndPaperImp($fromdate, $enddate);
            // if(false == $isFinishedDrugAndPaper){
            // $patient_hezuo->goOut(3);
            // continue;
            // }
            // }

            // 如果用药和评估都完成了，不催也不提醒了
            $isFinishedDrugAndPaper = $patient_hezuo->isFinishedDrugAndPaper();
            if ($isFinishedDrugAndPaper) {
                continue;
            }

            // 催患者做用药和评估
            $need_urge = $this->needUrge($diff);
            if ($need_urge) {
                $this->urge($patient_hezuo);
                continue;
            }

            // 对患者的提醒操作
            // $need_remind = $this->needRemind($diff);
            // if ($need_remind) {
            //     $this->remind($patient_hezuo);
            //     continue;
            // }

            // 生成运营任务
            // $need_createOptasks = $this->needCreateOptasks($diff);
            // if($need_createOptasks){
            // $this->createOptasks($patient_hezuo);
            // continue;
            // }

            // 警告患者7天后出组
            // $need_warnning = $this->needWarnning($diff);
            // if($need_warnning){
            // $this->warnning($patient_hezuo);
            // continue;
            // }

            // 没有完成全部的用药和评估则将患者并标记不活跃状态
            // $need_goout = $patient_hezuo->needNotActiveOut();
            // if($need_goout){
            // $patient_hezuo->goOut(3);
            // continue;
            // }
        }

        $unitofwork->commitAndInit();
    }

    private function needUrge ($diff) {
        $arr0 = [
            // 7,
            28,
            56,
            84,
            112,
            140,
            168];
        // 催评估，催用药
        if (in_array($diff, $arr0)) {
            return true;
        }
        return false;
    }

    private function urge ($patient_hezuo) {
        $patient = $patient_hezuo->patient;
        echo "\n====urge===";
        $this->sendmsg($patient, "urge");
    }

    private function needRemind ($diff) {
        $arr3 = [
            7 + 3,
            28 + 3,
            56 + 3,
            84 + 3,
            112 + 3,
            140 + 3,
            168 + 3];
        // 提醒
        if (in_array($diff, $arr3)) {
            return true;
        }
        return false;
    }

    private function remind ($patient_hezuo) {
        $patient = $patient_hezuo->patient;
        echo "\n====remind===";
        $this->sendmsg($patient, "remind");
    }

    private function needCreateOptasks ($diff) {
        $arr7 = [
            7 + 7,
            28 + 7,
            56 + 7,
            84 + 7,
            112 + 7,
            140 + 7,
            168 + 7];
        // 生成optask任务
        if (in_array($diff, $arr7)) {
            return true;
        }
        return false;
    }

    private function createOptasks ($patient_hezuo) {
        echo "\n===== createoptasks =====";

        $patient = $patient_hezuo->patient;

        $theday = $patient_hezuo->getNearlyDayCnt();
        $fromdate = date('Y-m-d', strtotime($patient_hezuo->createtime) + $theday * 86400);
        $enddate = XDateTime::now();

        $isFinishedDrug = $patient_hezuo->isFinishedDrug($fromdate, $enddate);
        if (false == $isFinishedDrug) {
            // 生成任务: 基础用药任务
            OpTaskService::createPatientOpTask($patient, 'baseDrug:', null, '', 1);
        }

        $isFinishedAdhdPaper = $patient_hezuo->isFinishedAdhdPaper($fromdate, $enddate);
        if (false == $isFinishedAdhdPaper) {
            // 生成任务: 基础评估任务
            // OpTaskService::createPatientOpTask($patient, 'baseScale:', null, '', 1);
        }
    }

    private function needWarnning ($diff) {
        $arr14 = [
            7 + 14,
            28 + 14,
            56 + 14,
            84 + 14,
            112 + 14,
            140 + 14,
            168 + 14];
        // 提醒并告知
        if (in_array($diff, $arr14)) {
            return true;
        }
        return false;
    }

    private function warnning ($patient_hezuo) {
        $patient = $patient_hezuo->patient;
        echo "\n====warnning===";
        $this->sendmsg($patient, "warnning");
    }

    private function sendmsg ($patient, $type) {
        $user = $patient->createuser;
        $wxuser = $user->createwxuser;
        if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
            $str = "向日葵关爱行动";
            $content = $this->getSendContentNew($wxuser, $type);
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

    private function getSendContentNew ($wxuser, $type) {
        if ($type == "urge") {
            $str = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_drugscalenotice_urge');
        }
        if ($type == "remind") {
            $str = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_drugscalenotice_remind');
        }
        if ($type == "warnning") {
            $str = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_drugscalenotice_warnning');
        }

        if ($type == "remind" || $type == "warnning") {
            $finishDrug = $this->finishDrug;
            $finishPaper = $this->finishPaper;
            if ($finishDrug) {
                $strFix = "评估";
            } else
                if ($finishPaper) {
                    $strFix = "用药";
                } else {
                    $strFix = "用药和评估";
                }
            $str = str_replace('#str_fix#', $strFix, $str);
        }

        return $str;
    }
}

// //////////////////////////////////////////////////////

$process = new Lilly_drugscalenotice(__FILE__);
$process->dowork();
