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

class Certican_remind_patient extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '10:10 给秦燕项目患者每天发送记录表填写,并且如果昨日填写，则给运营创建提醒任务';
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

    /**
     *
     * {@inheritdoc}
     *
     * @see CronBase::doworkImp()
     */
    protected function doworkImp () {
        $this->sendPatient();
        $this->createOptask();
    }

    private function getSendIds () {
        $today = date('Y-m-d');

        $sql = "select id
            from certicanitems
            where plan_date = '{$today}' and is_fill = 0 ";
        return Dao::queryValues($sql);
    }

    private function getYesterdayNotFillIds () {
        $yesterday = date('Y-m-d', time() - 3600 * 24 * 1);

        $sql = "select id
            from certicanitems
            where plan_date = '{$yesterday}' and is_fill = 0 ";
        return Dao::queryValues($sql);
    }

    private function sendPatient () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getSendIds();
        echo "sendPatient:";
        print_r($ids);
        $brief = 0;
        $logcontent = "";
        $i = 0;
        $k = 0;
        foreach ($ids as $id) {
            $certicanitem = CerticanItem::getById($id);
            $certican = $certicanitem->certican;
            $patient = $certican->patient;
            $pcard = $patient->getPcardByDoctorid($patient->doctorid);
            if (false == $patient instanceof Patient) {
                continue;
            }

            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            // 发送模板消息
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . '/certicanitem/list?certicanid=' . $certican->id;

            $first = array(
                "value" => "依维莫司服药及不良反应表",
                "color" => "");
            $keyword2 = "【依维莫司服药及不良反应表】[{$certicanitem->plan_date}]请您按时定期填写该表。";

            $keywords = array(
                array(
                    "value" => "{$patient->name}",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToPatientByDoctor($patient, $patient->doctor, 'followupNotice', $content, $url);

            $logcontent .= $certicanitem->id . ",";
        }

        $brief = $k;

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        $unitofwork->commitAndInit();
    }

    private function createOptask () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getYesterdayNotFillIds();
        echo "getYesterdayNotFillIds:";
        print_r($ids);

        foreach ($ids as $id) {
            $certicanitem = CerticanItem::getById($id);
            $certican = $certicanitem->certican;
            $patient = $certican->patient;

            // 生成任务: 秦燕项目任务
            $arr = [];
            $arr['content'] = "患者{$certicanitem->plan_date}没有填写记录";
            $optask = OpTaskService::createPatientOpTask($patient, 'qinyan_certicanitem:CerticanItem', $certicanitem, $plantime = '', $auditorid = 1, $arr);
        }

        $unitofwork->commitAndInit();
    }
}

$process = new Certican_remind_patient(__FILE__);
$process->dowork();
