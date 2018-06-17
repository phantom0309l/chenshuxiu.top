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
class Auto_send_optask_pro extends CronBase
{

    protected function getRowForCronTab () {
        $row = [];
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 9:55, 执行定时事件';
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
            $optaskcronids = $this->getOptaskCronIds();

            $brief = 0;
            $logcontent = '';

            foreach ($optaskcronids as $id) {
                $optaskcron = OpTaskCron::getById($id);
                $optask = $optaskcron->optask;
                $patient = $optask->patient;

                if (false == $optask instanceof OpTask) {
                    $optaskcron->status = 2;
                    $optaskcron->remark = "[自动消息中断] 任务不存在";

                    continue;
                }

                if (false == $patient instanceof Patient) {
                    $optaskcron->status = 2;
                    $optaskcron->remark = "[自动消息中断] 任务对应的患者不存在";
                    OpTaskService::addOptLog($optask, $optaskcron->remark, 1);

                    continue;
                }

                // 中断条件：如果患者回复，则中断
                if (true == $this->haveMessage($optaskcron)) {
                    $optaskcron->status = 2;
                    $optaskcron->remark = "[自动消息中断] 患者有回复";
                    OpTaskService::addOptLog($optask, $optaskcron->remark, 1);

                    continue;
                }

                // 第一步执行时，任务必须在根节点
                if ($optaskcron->optasktplcron->step == 1 && $optaskcron->optask->opnode->code != 'root') {
                    $optaskcron->status = 2;
                    $optaskcron->remark = "[自动消息中断] 首次执行时,任务不在根节点上";
                    OpTaskService::addOptLog($optask, $optaskcron->remark, 1);

                    continue;
                }

                // 发送消息
                $this->send($optaskcron);

                // 发送消息后的处理
                $this->sendAfter($optaskcron);

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

    // 获取定时事件的id
    private function getOptaskCronIds () {
        $today = date('Y-m-d', time() + 3600 * 24);

        $sql = "select a.id
                from optaskcrons a
                inner join optasks b on b.id = a.optaskid
                where a.status = 0 and a.plan_exe_time < '{$today}' and b.status in (0, 2) "; //

        $ids = Dao::queryValues($sql);

        return $ids;
    }

    // 指定时间内是否有过消息任务
    private  function haveMessage ($optaskcron) {
        $optask = $optaskcron->optask;
        $patient = $optask->patient;

        // #6053 除【基本信息收集任务】(id=493488746) 外，其他任务不受回复过消息影响，正常自动发送。增加手动中止自动发送消息功能
        if ($optask->optasktplid != 493488746) {
            return false;
        } else {
            if ($optaskcron->optasktplcron->step == 1) {
                // 如果在第一步则判断当前是否有未关闭的消息任务
                $sql = "select count(*)
                    from optasks
                    where optasktplid = 123261855 and status = 0 and patientid = {$patient->id} ";
                $cnt = Dao::queryValue($sql);
            } else {
                // 查找上一步骤的执行时间，计算到现在创建的消息任务数
                $pre_optasktplcron = OpTaskTplCronDao::getByOptasktplidStep($optask->optasktplid, $optaskcron->optasktplcron->step - 1);
                $pre_optaskcron = OpTaskCronDao::getByOptaskidOptasktplcronid($optask->id, $pre_optasktplcron->id);
                $endDate = $optaskcron->plan_exe_time;
                $startDate = date('Y-m-d', strtotime($pre_optaskcron->plan_exe_time)) . " 09:55:02";

                $sql = "select count(*)
                        from optasks
                        where optasktplid in(123261855, 564637416) and patientid = {$patient->id} and createtime > '{$startDate}' and createtime <= '{$endDate}' ";
                $cnt = Dao::queryValue($sql);
            }
        }

        return $cnt > 0;
    }

    // 发送消息
    private function send(OpTaskCron $optaskcron) {
        // 任务非关闭状态下才发提醒
        $content = $this->getContent($optaskcron);

        if ($content) {
            $wxusers = WxUserDao::getListByPatient($optaskcron->optask->patient);
            foreach ($wxusers as $wxuser) {
                PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
            }
        }
    }

    // 获取消息内容
    private function getContent (OpTaskCron $optaskcron) {
        $optask = $optaskcron->optask;

        $content = $optaskcron->getSendContent();

        /*
         pp (小写) : 患者姓名
         dd (小写) : 医生姓名
         DD (大写) : 疾病名
         * */

        $content = str_replace('pp', $optask->patient->name, $content);
        $content = str_replace('dd', $optask->patient->doctor->name, $content);
        $content = str_replace('DD', $optask->patient->disease->name, $content);

        return $content;
    }

    //  发送消息之后的处理
    private function sendAfter (OpTaskCron $optaskcron) {
        $optask = $optaskcron->optask;
        $optasktplcron = $optaskcron->optasktplcron;

        // 发送完消息之后的处理  挂起，关闭，约定跟进
        if ($optasktplcron->dealwith_type == 'hang_up') {
            // 挂起任务
            OpTaskStatusService::changeStatus($optask, 2);
        } elseif ($optasktplcron->dealwith_type == 'unfinish') {
            //　未完成
            $from_opnode = $optask->opnode;
            $to_opnode = OpNodeDao::getByCodeOpTaskTplId('unfinish', $optask->optasktplid);

            if ($from_opnode instanceof OpNode && $to_opnode instanceof OpNode) {
                $opnodeflow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($from_opnode, $to_opnode);

                if ($opnodeflow instanceof OpNodeFlow) {
                    // 任务节点切换(流转)
                    OpTaskEngine::flow($optask, $opnodeflow, 1);
                }
            }
        } else {
            // 下一步
            $nextstep_optasktplcron = OpTaskTplCronDao::getByOptasktplidStep($optask->optasktplid, $optasktplcron->step + 1);

            $plantime = date('Y-m-d', time() + 3600 * 24 * $optasktplcron->follow_daycnt);
            $optask->plantime = $plantime;

            // 约定跟进
            $opnode_follow = OpNodeDao::getByCodeOpTaskTplId('appoint_follow', $optask->optasktplid);
            if ($opnode_follow instanceof OpNode) {
                $optask->opnodeid = $opnode_follow->id;

                if ($nextstep_optasktplcron instanceof OpTaskTplCron) {
                    $row = [];
                    $row["optaskid"] = $optaskcron->optaskid;
                    $row["optasktplcronid"] = $nextstep_optasktplcron->id;
                    $row["plan_exe_time"] = $plantime;
                    $row["status"] = 0;
                    OpTaskCron::createByBiz($row);
                }
            }
        }

        // 设置状态
        $optaskcron->status = 1;

        // 发送消息内容
        $content = $this->getContent($optaskcron);

        // 任务日志记录
        OpTaskService::addOptLog($optask, " 已发送自动消息【{$content}】", 1);
    }
}

$test = new Auto_send_optask_pro(__FILE__);
$test->dowork();
