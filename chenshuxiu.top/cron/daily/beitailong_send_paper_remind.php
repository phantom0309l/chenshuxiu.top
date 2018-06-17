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

class Beitailong_send_paper_remind extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 12:01, 第一次跟进未填写当天的【倍泰龙注射日记】的患者';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return true;
    }

    protected function doworkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $brief = 0;
        $logcontent = '';

        $type = 'beitailong';
        $patients = $this->getPatients();
        foreach ($patients as $patient) {
            $patientlog = PatientLogDao::getLastOneByPatientidAndType($patient->id, $type);
            if ($patientlog instanceof PatientLog) {
                $d = json_decode($patientlog->content, true);
                if ($d) {
                    $prev_send_date = $d['prev_send_date']; // 上次发送日期
                    $round = $d['round']; // 轮数
                    $number = $d['number']; // 次数

                    $plan_date = date('Y-m-d', strtotime($prev_send_date));
                    $today = date('Y-m-d');

                    //判断是否今天发送的
                    if ($plan_date == $today) {
                        $is_write = $d['is_write']; // 是否填写
                        if ($is_write == 1) { // 已经填写，跳过
                            continue;
                        }
                        $this->send($patient, $round, $number);

                        $brief ++;
                        $logcontent .= $patient->id . " ";
                    } else { // 上次日期不是今天，跳过
                        continue;
                    }
                } else {
                    Debug::warn("{$patientlog->id}(倍泰龙)json数据解析失败");
                    continue;
                }
            } else { // patientlog不存在
                // 跳过
                continue;
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "\n{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function send(Patient $patient, $round, $number) {
        $wx_uri = Config::getConfig("wx_uri");
        $url = "{$wx_uri}/paper/beitailong/?round={$round}&number={$number}";

        $first = array(
            "value" => "倍泰龙注射日记（第" . ($round + 1) . "轮，第" . ($number + 1) . "期）",
            "color" => "#ff6600");
        $keywords = array(
            array(
                "value" => $patient->name,
                "color" => "#aaa"),
            array(
                "value" => date("Y-m-d H:i:s"),
                "color" => "#aaa"),
            array(
                "value" => '您好，您今天完成注射了吗？请您按照表中推荐的部位完成注射后，认真仔细填写表中内容并提交，以便医生查看。',
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);
    }

    private function getPatients() {
        return PatientDao::getListByPatientGroupid(PatientGroup::beitailongid);
    }
}

$test = new Beitailong_send_paper_remind(__FILE__);
$test->dowork();
