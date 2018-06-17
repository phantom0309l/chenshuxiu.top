<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class QuickPass_service_expire_remind extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:08, 给患者发送快速通行证到期提醒';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return $this->cronlog_brief > 0;
    }

    /*
     * 提前三天发送到期提醒
     * 到期当天发送提醒
     */

    protected function doworkImp() {
        $this->remindAfterThreeDays();
        $this->remindToday();
    }

    private function remindAfterThreeDays() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getAfterThreeDaysIds();

        $brief = 0;
        $logcontent = '';

        foreach ($ids as $id) {
            $quickpass_serviceitem = QuickPass_ServiceItem::getById($id);
            $patient = $quickpass_serviceitem->patient;

            // 发送模板消息
            $wx_uri = Config::getConfig("wx_uri");

            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                $gh = $wxuser->wxshop->gh;
                $content = "你好，你的『快速通行证』将于3日后到期，如仍需『快速通行证』服务，请及时续费。\n<a href='{$wx_uri}/quickpass_serviceorder/buy?gh={$gh}'>立即续费</a>";
                PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
            }

            $brief++;
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

    private function remindToday() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getTodayIds();

        $brief = 0;
        $logcontent = '';

        foreach ($ids as $id) {
            $quickpass_serviceitem = QuickPass_ServiceItem::getById($id);
            $patient = $quickpass_serviceitem->patient;

            // #5658 如果患者vip失效的处理, TODO fhw : 改到第二天凌晨降级
            $patient->expireVIP();

            // 发送模板消息
            $wx_uri = Config::getConfig("wx_uri");

            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                $gh = $wxuser->wxshop->gh;
                $content = "你好，你的『快速通行证』已于今日到期，如仍需『快速通行证』服务，请及时续费。\n<a href='{$wx_uri}/quickpass_serviceorder/buy?gh={$gh}'>立即续费</a>";
                PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
            }

            $brief++;
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

    // 提前三天提醒
    private function getAfterThreeDaysIds() {
        $timestamp = time() + 3600 * 24 * 3;

        return $this->queryValues($timestamp);
    }

    // 到期当天推送
    private function getTodayIds() {
        $timestamp = time();

        return $this->queryValues($timestamp);
    }

    private function queryValues($timestamp) {
        $starttime = date('Y-m-d 00:00:00', $timestamp);
        $endtime = date('Y-m-d 23:59:59', $timestamp);

        $sql = "SELECT id
                FROM (
                    SELECT * 
                    FROM (
                        SELECT a.*
                        FROM quickpass_serviceitems a
                        INNER JOIN serviceorders b ON a.serviceorderid = b.id
                        WHERE a.status = 1
                        AND b.serviceproduct_type = :type
                        ORDER BY a.endtime DESC) t1 
                    GROUP BY patientid
                    ORDER BY null) t2
                WHERE endtime >= :starttime
                AND endtime <= :endtime";
        $bind = [
            ':type' => 'quickpass',
            ':starttime' => $starttime,
            ':endtime' => $endtime
        ];

        $ids = Dao::queryValues($sql, $bind);
        return $ids;
    }
}

$test = new QuickPass_service_expire_remind(__FILE__);
$test->dowork();
