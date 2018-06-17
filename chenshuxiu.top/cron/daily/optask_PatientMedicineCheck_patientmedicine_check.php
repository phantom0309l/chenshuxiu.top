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

class optask_PatientMedicineCheck_patientmedicine_check extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 09:30 发送 靶向药定期核对（用药核对）';
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
        echo "\n 推送[begin]";
        $unitofwork = BeanFinder::get("UnitOfWork");

        $fromtime = date('Y-m-d 00:00:00', time() + 3600 * 24); // 明天0点0分0秒
        $totime = date('Y-m-d 23:59:59', time() + 3600 * 24); // 明天23点59分59秒

        $optasks = OpTaskDao::getListByUnicodeStatus('patientmedicine:check', 0, $fromtime, $totime);

        foreach ($optasks as $optask) {
            echo "\n optask[{$optask->id}] ";

            $patient = $optask->patient;
            if (false == $patient instanceof Patient) {
                echo " [没有推送成，patientid={$optask->patientid}] ";
                continue;
            }

            $pmCheck = $optask->obj;
            $pmCheck->status = 1;

            $pcard = $patient->getMasterPcard();

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri."/patientmedicinecheck/check?optaskid={$optask->id}";

            $first = array(
                "value" => "患者用药核对",
                "color" => "");
            $keyword2 = "您好，请点击本条消息，填写用药核对调查表，内容包括服用情况、相关不良反应、及近期评估。提交后我们会及时进行查看若有问题会主动与您联系。请您点击本条消息。";

            $keywords = array(
                array(
                    "value" => "{$pcard->doctor->name}",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600")
            );
            $content = WxTemplateService::createTemplateContent($first, $keywords);
            PushMsgService::sendTplMsgToWxUsersOfPcardBySystem($pcard, "doctornotice", $content, $url);
            echo " [suc] ";
        }
        $unitofwork->commitAndInit();

        echo "\n 推送[end]";
    }
}

// //////////////////////////////////////////////////////

$process = new Optask_PmSideEffect_Remind(__FILE__);
$process->dowork();
