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

class Send_dc_patientplanitem extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 11:00, 给患者发送患者项目收集填写量表';
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

        $brief = 0;
        $logcontent = '';

        foreach ($ids as $id) {
            $dc_patientplanitem = Dc_patientPlanItem::getById($id);
            $dc_patientplan = $dc_patientplanitem->dc_patientplan;
            $dc_doctorproject = $dc_patientplan->dc_doctorproject;
            $patient = $dc_patientplanitem->patient;
            $status = $dc_patientplanitem->getStatus();

            // 已完成，不再发送
            if ($status == 2) {
                continue;
            }

            // 发送模板消息
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . '/dc_patientplanitem/list?dc_patientplanid=' . $dc_patientplan->id;

            $first = [
                "value" => "{$dc_doctorproject->send_content_tpl}",
                "color" => ""
            ];

            $keywords = [
                [
                    "value" => "{$patient->name}",
                    "color" => "#ff6600"
                ],
                [
                    "value" => date('Y-m-d'),
                    "color" => "#ff6600"
                ],
                [
                    "value" => "请点击详情进行填写",
                    "color" => "#ff6600"
                ]
            ];
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);

            $brief ++;
            $logcontent .= $dc_patientplanitem->id . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "\n{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function getIds () {
        $today = date('Y-m-d');

        $sql = "select id
            from dc_patientplanitems
            where plan_date = '{$today}' ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
}

$test = new Send_dc_patientplanitem(__FILE__);
$test->dowork();
