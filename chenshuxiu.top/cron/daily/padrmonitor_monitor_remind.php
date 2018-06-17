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

class Padrmonitor_monitor_remind extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:25, 给患者发送不良反应监测';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return $this->cronlog_brief > 0;
    }

    protected function doworkImp() {
        $this->remind();
        $this->remindAgain();
    }

    private function remind () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        
        $ids = $this->getRemindIds();

        $brief = 0;
        $logcontent = '';

        $sends = [];
        foreach ($ids as $id) {
            $padrmonitor = PADRMonitor::getById($id);
            $patientid = $padrmonitor->patientid;
            $padrmonitor->status = 1;

            $arr = $sends["{$patientid}"] ?? [];
            $enameStr = ADRMonitorRuleItem::getItemStr($padrmonitor->adrmonitorruleitem_ename);
            if (!in_array($enameStr, $arr)) {
                $arr[] = $enameStr;
                $sends["{$patientid}"] = $arr;
            }

        }

        foreach ($sends as $patientid => $items) {
            $patient = Patient::getById($patientid);

            if ($patient->is_adr_monitor == 0) {
                continue;
            }

            // 患者用药中需要哪些检查
            $itemstr = implode('、', $items);

            // 发送模板消息
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/padrmonitor/list";

            $first = [
                "value" => "您好，{$itemstr}检查做了么？如果还没有请尽快进行检查。如果已经检查请点击详情进行上传。",
                "color" => ""
            ];
            $keywords = [
                [
                    "value" => $itemstr,
                    "color" => "#ff6600"
                ],
                [
                    "value" => "",
                    "color" => "#ff6600"
                ],
                [
                    "value" => "",
                    "color" => "#ff6600"
                ]
            ];
            $remark = "请点击详情进行处理，请注意您的上传内容会直接汇报给医生及医生助理。如有问题请直接与我们联系。";
            $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);

            PushMsgService::sendTplMsgToPatientBySystem($patient, "jyjc_remind", $content, $url);

            $brief ++;
            $logcontent .= $patientid . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function remindAgain () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        
        $ids = $this->getRemindAgainIds();
        
        $brief = 0;
        $logcontent = '';

        $sends = [];
        foreach ($ids as $id) {
            $padrmonitor = PADRMonitor::getById($id);
            $patientid = $padrmonitor->patientid;
            $padrmonitor->status = 1;

            $arr = $sends["{$patientid}"] ?? [];
            $enameStr = ADRMonitorRuleItem::getItemStr($padrmonitor->adrmonitorruleitem_ename);
            if (!in_array($enameStr, $arr)) {
                $arr[] = $enameStr;
                $sends["{$patientid}"] = $arr;
            }
        }

        foreach ($sends as $patientid => $items) {
            $patient = Patient::getById($patientid);

            if ($patient->is_adr_monitor == 0) {
                continue;
            }

            // 患者用药中需要哪些检查
            $itemstr = implode('、', $items);

            // 发送模板消息
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/padrmonitor/list";

            $first = [
                "value" => "请点击详情上传您的{$itemstr}检查结果。",
                "color" => ""
            ];
            $keywords = [
                [
                    "value" => $itemstr,
                    "color" => "#ff6600"
                ],
                [
                    "value" => "",
                    "color" => "#ff6600"
                ],
                [
                    "value" => "",
                    "color" => "#ff6600"
                ]
            ];
            $remark = "请点击详情进行处理，请注意您的上传内容会直接汇报给医生及医生助理。如有问题请直接与我们联系。";
            $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);

            PushMsgService::sendTplMsgToPatientBySystem($patient, "jyjc_remind", $content, $url);

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

    /*
     * 发送时机为“前1后2天”
     */

    // 提前一天提醒
    private function getRemindIds () {
        $date = date('Y-m-d', time() + 3600 * 24);
    
        $sql = "SELECT a.id
                FROM padrmonitors a
                LEFT JOIN patients b on a.patientid = b.id
                WHERE a.plan_date <= '{$date}'
                AND b.is_adr_monitor = 1
                AND a.diseaseid IN (2,3,6,22)
                AND a.status = 0 ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
    // 延后两天推送
    private function getRemindAgainIds () {
        $date = date('Y-m-d', time() - 3600 * 24 * 2);

        $sql = "SELECT a.id
                FROM padrmonitors a
                LEFT JOIN patients b on a.patientid = b.id
                WHERE a.plan_date = '{$date}'
                AND b.is_adr_monitor = 1
                AND a.diseaseid IN (2,3,6,22)
                AND a.status = 1
                ORDER BY a.plan_date DESC ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
}

$test = new Padrmonitor_monitor_remind(__FILE__);
$test->dowork();
