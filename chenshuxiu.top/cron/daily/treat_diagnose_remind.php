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

/**
 * #4719 #4720
 * @author fhw
 *
 */
class Treat_diagnose_remind extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:20, 给昨天报到后没有填写的患者发送治疗信息调查';
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
        $logcontent = '';

        foreach ($ids as $id) {
            $patientcollection = PatientCollection::getById($id);

            if (false == $patientcollection->patient instanceof Patient) {
                Debug::warn("patientcollection : {$patientcollection->id} is not patient");
                continue;
            }

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patientcollection/stepone?patientcollectionid=" . $patientcollection->id;

            $first = array(
                "value" => "请您尽快填写当前治疗情况调查（操作方法：点击本条消息详情进行填写）",
                "color" => "");

            $keywords = [
                [
                    "value" => "{$patientcollection->patient->doctor->name}医生随访团队",
                    "color" => "#ff6600"
                ],
                [
                    "value" => "请点击详情填写当前治疗情况调查",
                    "color" => "#ff6600"
                ]
            ];
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToPatientBySystem($patientcollection->patient, "doctornotice", $content, $url);

            $brief ++;
            $logcontent .= $patientcollection->id . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function getIds () {
        $today = date('Y-m-d');
        $towtodaybefore = date('Y-m-d', time() - 3600 * 24 * 2);

        $sql = "select id
            from patientcollections
            where createtime > '{$towtodaybefore}' and createtime < '{$today}' and type = 'treet_diagnose' and is_fill = 0 ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }

}

$test = new Treat_diagnose_remind(__FILE__);
$test->dowork();
