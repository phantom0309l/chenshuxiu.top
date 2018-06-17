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

/*
 * #4722
 */
class Tnfo_check_remind extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:25, 给昨天未填写信息核对的患者再次发送';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return $this->cronlog_brief > 0;
    }

    protected function doworkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getIds();

//         $ids = [323871856];

        $brief = 0;

        foreach ($ids as $id) {
            $patientcollection = PatientCollection::getById($id);

            $patient = $patientcollection->patient;

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patientcollection/medicinecheck"; // #4722

            $first = [
                "value" => "请您尽快填写信息表（操作方法：点击本条信息详情进行填写）",
                "color" => ""
            ];

            $keywords = [
                [
                    "value" => $patient->name,
                    "color" => "#ff6600"
                ],
                [
                    "value" => $patient->doctor->name . "诊后随访团队",
                    "color" => "#ff6600"
                ],
                [
                    "value" => "医嘱信息",
                    "color" => "#ff6600"
                ]
            ];
            $remark = "请点击详情进行信息核对。";
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToPatientBySystem($patient, "info_check_notice", $content, $url);

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

    // 获取昨日没有填写信息调查的患者
    private function getIds () {
        $today = date('Y-m-d');
        $towtodaybefore = date('Y-m-d', time() - 3600 * 24 * 2);

        $cond = " and type = 'info_check' and createtime > '{$towtodaybefore}' and createtime < '{$today}' ";
        $patientcollections = Dao::getEntityListByCond('PatientCollection', $cond);

        $ids = [];
        foreach ($patientcollections as $patientcollection) {
            $json = json_encode($patientcollection->json_content, true);
            $status = $json['status'];

            if ($status == 0) {
                $ids[] = $patientcollection->id;
            }
        }

        return $ids;
    }
}

$test = new Tnfo_check_remind(__FILE__);
$test->dowork();
