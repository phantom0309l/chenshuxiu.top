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

// MARK: - 这个不需要再发送了
class PatientMedicineSheet_Send_Patient extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:01 每天 发送 用药核对表格';
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

        // /每天10：00发送提醒
        $fromtime = date('Y-m-d H:i:s', time() - 86400);
        $totime = date('Y-m-d H:i:s');
        $unitofwork = BeanFinder::get("UnitOfWork");

        $fromtime = date('Y-m-d 12:00:00', time() - 24 * 3600 * 1);
        $totime = date('Y-m-d 12:00:00');

        $sql = "select b.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.next_pmsheet_time > :fromtime and b.next_pmsheet_time < :totime and b.diseaseid not in (2,3,6,22) ";

        $bind = [];
        $bind[':fromtime'] = $fromtime;
        $bind[':totime'] = $totime;

        $pcards = Dao::loadEntityList('Pcard', $sql, $bind);

        foreach ($pcards as $pcard) {
            $patient = $pcard->patient;
            $doctor = $pcard->doctor;

            echo "\n patient:{$patient->name}";

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patientmedicinesheet/one?i=1";

            $first = array(
                "value" => "患者用药核对",
                "color" => "");
            $keyword2 = "您好，请点击本条消息，填写用药核对调查表。我们会记录您当前的用药情况并据此核对用药的正确性，以减少您因错误服药导致的疗效下降和不良反应。请您点击本条消息。";

            $keywords = array(
                array(
                    "value" => "{$pcard->doctor->name}",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);
            PushMsgService::sendTplMsgToWxUsersOfPcardBySystem($pcard, "doctornotice", $content, $url);

            $pcard->send_pmsheet_status = 1;

            echo " [suc]";

            $pcard->next_pmsheet_time = XDateTime::getNewDate($pcard->next_pmsheet_time, 30);

            // 生成任务: 系统创建跟进[用药核对]
            $plantime = date('Y-m-d', strtotime($pcard->next_pmsheet_time) + 3600 * 24);
            OpTaskService::createOpTaskByUnicode($wxuser = null, $patient, $doctor, 'system:PatientMedicineSheet', null, $plantime, 1);
        }
        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new PatientMedicineSheet_Send_Patient(__FILE__);
$process->dowork();
