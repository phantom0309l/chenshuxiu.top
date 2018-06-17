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
class Check_hangup_time_out extends CronBase
{

    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 00:05, 把昨天挂起的任务，做挂起超时处理';
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

        $optaskids = $this->getOptaskIds();

        // $optaskids = [459621178];

        $brief = 0;
        $logcontent = '';

        foreach ($optaskids as $id) {
            $optask = OpTask::getById($id);

            // #5112 关联了【定时消息】且未发送的任务，不会被唤醒或者流转节点
            $unsent_cnt = Plan_txtMsgDao::getUnsentCntByObj($optask);
            if ($unsent_cnt > 0) {
                continue;
            }

            // #5212 任务挂起后，隔天00:05:00自动唤醒 : 将状态从挂起=>进行中
            OpTaskStatusService::changeStatus($optask, 0, $auditorid = 1);

            // #5212 如果有timeout Flow, 则切换到下个节点。
            $opnodeflow = OpNodeFlowDao::getByFrom_opnodeType($optask->opnode, 'timeout');
            if ($opnodeflow instanceof OpNodeFlow) {

                echo "\nOpTask[{$optask->id}] OpNodeFlow[{$opnodeflow->id}]\n";

                // 任务节点切换(流转): 超时流的下一个节点
                OpTaskEngine::flow($optask, $opnodeflow, $auditorid = 1);

                $brief ++;
                $logcontent .= $optask->id . " ";
            }

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function getOptaskIds () {
        $today = date('Y-m-d');

        $sql = "select distinct a.id
            from optasks a
            inner join opnodes c on c.id = a.opnodeid
            where a.plantime < '{$today}' and a.status = 2 ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
}

$test = new Check_hangup_time_out(__FILE__);
$test->dowork();
