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

// fhw address
class Auto_send_optask extends CronBase
{

    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 9:55, 发送自动消息，超过9:55创建的任务当日不发';
        return $row;
    }

    protected function needFlushXworklog () {
        return true;
    }

    protected function needCronlog () {
        return $this->cronlog_brief > 0;
    }

    protected function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        // 法定放假日不发送消息
        $today = date('Y-m-d');
        if (false == FUtil::isHoliday($today)) {
            $optaskids = $this->getOptaskIds();

            $brief = 0;
            $logcontent = '';

            foreach ($optaskids as $id) {
                $optask = OpTask::getById($id);
                $patient = $optask->patient;

                if (false == $patient instanceof Patient) {
                    continue;
                }

                // 如果当天有消息任务的患者不推送
                if (true == $this->haveMessage($patient)) {
                    continue;
                }

                // 发送消息
                $this->send($optask, $patient);

                // 发送消息后的处理
                $this->sendAfter($optask);

                $brief ++;
                $logcontent .= "{$id},";

                if ($brief % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }

            $this->cronlog_brief = $brief;
            $this->cronlog_content = $logcontent;

            echo "{$this->cronlog_brief} {$this->cronlog_content} \n";
        }

        $unitofwork->commitAndInit();
    }

    // 获取要发送消息任务的id
    private function getOptaskIds () {
        $today = date('Y-m-d', time() + 3600 * 24);

        $sql = "select a.id
            from optasks a
            inner join optasktpls b on b.id = a.optasktplid
            inner join opnodes c on c.id = a.opnodeid
            where a.plantime < '{$today}' and a.status = 0 and a.send_status = 0 and c.code = 'root' and b.is_auto_send = 1 and b.auto_send_content <> '' ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }

    // 是否有进行中的消息任务
    private function haveMessage ($patient) {
        $sql = "select count(*)
                from optasks
                where optasktplid = 123261855 and status = 0 and patientid = {$patient->id} ";
        $cnt = Dao::queryValue($sql);

        return $cnt > 0;
    }

    // 发送消息
    private function send (OpTask $optask, Patient $patient) {
        $content = $this->getContent($optask);

        if ($content) {
            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
            }
        }
    }

    // 获取消息内容
    private function getContent (OpTask $optask) {
        if ($optask->optasktplid == 270243896) {
            if ($optask->patient->patientstage instanceof PatientStage) {
                if (in_array($optask->patient->patientstage->title, ['手术', '其他'])) {
                    return "{$optask->patient->name}您好，患者目前在怎么治疗？治疗方案有变化么？目前患者情况怎么样？有什么需要我们协助解决的问题么？";
                } elseif (in_array($optask->patient->patientstage->title, ['化疗', '靶向'])) {
                    return "{$optask->patient->name}您好，目前患者情况怎么样？出现过什么不良反应么？有什么需要我们协助解决的问题么？";
                }
            }
        } else {
            $content = $optask->optasktpl->auto_send_content;

            /*
             pp (小写) : 患者姓名<br>
             dd (小写) : 医生姓名
             DD (大写) : 疾病名
             * */

            $content = str_replace('pp', $optask->patient->name, $content);
            $content = str_replace('dd', $optask->patient->doctor->name, $content);
            $content = str_replace('DD', $optask->patient->disease->name, $content);

            return $content;
        }
    }

    //  发送消息之后的处理
    private function sendAfter (OpTask $optask) {
        // 发送完消息之后的处理  挂起，约定跟进
        if ($optask->optasktpl->send_done_dealwith_type == 'hang_up') {
            // 挂起任务
            OpTaskStatusService::changeStatus($optask, 2);
        } else {
            // 修改plantime
            if ($optask->optasktpl->appoint_follow_daycnt > 0) {
                $optask->plantime = date('Y-m-d', time() + 3600 * 24 * $optask->optasktpl->appoint_follow_daycnt);
            }

            $from_opnode = OpNodeDao::getByCodeOpTaskTplId('root', $optask->optasktplid);
            $to_opnode = OpNodeDao::getByCodeOpTaskTplId('appoint_follow', $optask->optasktplid);

            if ($from_opnode instanceof OpNode && $to_opnode instanceof OpNode) {
                $opnodeflow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($from_opnode, $to_opnode);

                if ($opnodeflow instanceof OpNodeFlow) {
                    // 任务节点切换(流转)
                    OpTaskEngine::flow($optask, $opnodeflow, 1);
                }
            }

        }

        // 设置发送状态
        $optask->send_status = 1;

        // 发送消息内容
        $content = $this->getContent($optask);

        // 任务日志记录
        OpTaskService::addOptLog($optask, " 已发送自动消息【{$content}】", 1);
    }
}

$test = new Auto_send_optask(__FILE__);
$test->dowork();
