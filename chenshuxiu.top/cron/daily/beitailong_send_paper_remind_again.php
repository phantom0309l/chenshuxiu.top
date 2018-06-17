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

class Beitailong_send_paper_remind_again extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 16:01, 第二次跟进未填写当天的【倍泰龙注射日记】的患者';
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

                    // 判断是否今天
                    if ($plan_date == $today) {
                        $is_write = $d['is_write']; // 是否填写
                        if ($is_write == 1) { // 已经填写，跳过
                            continue;
                        }

                        // 第二次跟进的时候，如果还未填写，生成任务
                        $optask = OpTaskService::createOpTaskByUnicode(null, $patient, $patient->doctor, 'beitailong:remind');
                        $content = "第" . ($round + 1) . "轮";
                        $content .= "，第" . ($number + 1) . "次";
                        $optask->content = $content;

                        $brief++;
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

    private function getPatients() {
        return PatientDao::getListByPatientGroupid(PatientGroup::beitailongid);
    }
}

$test = new Beitailong_send_paper_remind_again(__FILE__);
$test->dowork();
