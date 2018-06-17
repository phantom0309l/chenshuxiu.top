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

class Beitailong_send_paper extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:02, 给患者发送倍泰龙注射日记量表';
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
                    $round = $d['round']; // 轮数 0-5
                    $number = $d['number']; // 次数 0-7

                    // 如果已经发送完所有的量表
                    if ($round >= 5 && $number >= 7) {
                        continue;
                    }

                    // 隔天发送
                    $plan_date = date('Y-m-d', strtotime('+2 day', strtotime($prev_send_date)));
                    $now = date('Y-m-d');
                    if ($plan_date == $now) {
                        $number++;
                        if ($number > 7) {
                            // 进入下一轮
                            $round++;
                            $number = 0;
                        }
                        $this->send($patient, $round, $number);

                        $d['prev_send_date'] = date('Y-m-d H:i:s');
                        $d['round'] = $round;
                        $d['number'] = $number;
                        $d['is_write'] = 0;
                        $patientlog->content = json_encode($d, JSON_UNESCAPED_UNICODE);

                        $brief++;
                        $logcontent .= $patient->id . " ";
                    }
                } else {
                    Debug::warn("{$patientlog->id}(倍泰龙)json数据解析失败");
                    continue;
                }
            } else { // 首次，不存在patientlog
                $round = 0;
                $number = 0;
                $this->send($patient, $round, $number);

                $d = [
                    'prev_send_date' => date('Y-m-d H:i:s'),
                    'round' => $round,
                    'number' => $number,
                    'is_write' => 0,
                ];

                $row = array();
                $row["patientid"] = $patient->id;
                $row["type"] = $type;
                $row["title"] = '倍泰龙注射日记';
                $row["content"] = json_encode($d, JSON_UNESCAPED_UNICODE);
                PatientLog::createByBiz($row);

                $brief++;
                $logcontent .= $patient->id . " ";
            }

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
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
                "value" => '您好，注射倍泰龙期间需要关注药物可能引起的副作用以便给您相应指导建议，所以请您按照表中推荐的部位完成注射后，认真仔细填写表中内容并提交，以便医生查看。',
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);
    }

    private function getPatients() {
        return PatientDao::getListByPatientGroupid(PatientGroup::beitailongid);
    }
}

$test = new Beitailong_send_paper(__FILE__);
$test->dowork();
