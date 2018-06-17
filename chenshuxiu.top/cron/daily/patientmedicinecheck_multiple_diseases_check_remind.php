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

class Patientmedicinecheck_multiple_diseases_check_remind extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:15, 给多疾病患者发送用药核对，如果当天患者没填写，次日再次发送';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return $this->cronlog_brief > 0;
    }

    protected function doworkImp() {
        $this->sendToday();
        $this->sendYestoday();
    }

    private function sendToday () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getTodayIds();

//         $ids = [323871856];

        $brief = 0;
        $logcontent = '';

        $sends = [];
        foreach ($ids as $id) {
            $patientmedicinecheck = PatientMedicineCheck::getById($id);
            $patient = $patientmedicinecheck->patient;
            $patientmedicinecheck->status = 1;

            $sends["{$patient->id}"] = $patientmedicinecheck;
        }

        foreach ($sends as $patientid => $patientmedicinecheck) {
            $patient = $patientmedicinecheck->patient;

            if ($patient->is_medicine_check == 0) {
                continue;
            }

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patientmedicinecheck/checkofmultiplediseases?todate=" . date('Y-m-d') . "&patientmedicinecheckid=" . $patientmedicinecheck->id;

            $first = [
                "value" => "您好，与您核对一下近期的服药情况，请您点击“详情”进行用药核对。【注意：一定要仔细认真填写！】因为您填写完成后会直接发送给医生查看，下次我们不再与您二次核实。如果您填写错误会直接给医生一个错误的信息，有碍医生对您的病情判断以及诊疗。",
                "color" => ""
            ];
            $keywords = [
                [
                    "value" => "{$patient->doctor->name}医生随访团队 ",
                    "color" => "#aaa"
                ],
                [
                    "value" => "用药情况核对",
                    "color" => "#ff6600"
                ]
            ];
            $remark = "医嘱内容：请点击详情进行用药核对，请注意。您的核对结果会直接汇报给医生及医生助理。如有问题请直接与我们联系。";
            $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);

            PushMsgService::sendTplMsgToPatientBySystem($patient, "doctornotice", $content, $url);

            $brief ++;
            $logcontent .= $patientmedicinecheck->id . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function sendYestoday () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getYestodayIds();

        //         $ids = [323871856];

        $brief = 0;
        $logcontent = '';

        $sends = [];
        foreach ($ids as $id) {
            $patientmedicinecheck = PatientMedicineCheck::getById($id);
            $patient = $patientmedicinecheck->patient;
            $patientmedicinecheck->status = 1;

            $sends["{$patient->id}"] = $patientmedicinecheck;
        }

        foreach ($sends as $patientid => $patientmedicinecheck) {
            $patient = $patientmedicinecheck->patient;

            if ($patient->is_medicine_check == 0) {
                continue;
            }

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patientmedicinecheck/checkofmultiplediseases?todate=" . date('Y-m-d', time() - 3600 * 24) . "&patientmedicinecheckid=" . $patientmedicinecheck->id;

            $first = [
                "value" => "您好，请尽快进行用药核对。如果遇到什么问题您可以直接与我们联系。",
                "color" => ""
            ];
            $keywords = [
                [
                    "value" => "{$patient->doctor->name}医生随访团队 ",
                    "color" => "#aaa"
                ],
                [
                    "value" => "用药情况核对",
                    "color" => "#ff6600"
                ]
            ];
            $remark = "请点击详情进行用药核对，请注意。您的核对结果会直接汇报给医生。如有问题请直接与我们联系。";
            $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);

            if ($patientmedicinecheck->wxuser instanceof WxUser) {
                PushMsgService::sendTplMsgToWxUserBySystem($patientmedicinecheck->wxuser, "doctornotice", $content, $url);
            } elseif ($patientmedicinecheck->patient instanceof Patient) {
                PushMsgService::sendTplMsgToPatientBySystem($patientmedicinecheck->patient, "doctornotice", $content, $url);
            } else {
                Debug::warn("patientmedicinecheck[{$patientmedicinecheck->id}] 没有wxuser,没有patient");
                continue;
            }

            $brief ++;
            $logcontent .= $patient->id . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function getTodayIds () {
        $today = date('Y-m-d');

        $sql = "select id
            from patientmedicinechecks
            where type = 'multiple_diseases' and plan_send_date = '{$today}' and status = 0 ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }

    private function getYestodayIds () {
        $yestoday = date('Y-m-d', time() - 3600 * 24);

        $sql = "select id
        from patientmedicinechecks
        where type = 'multiple_diseases' and plan_send_date = '{$yestoday}' and status in (0, 1) ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
}

$test = new Patientmedicinecheck_multiple_diseases_check_remind(__FILE__);
$test->dowork();
