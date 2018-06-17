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

class Optask_PmSideEffect_Remind extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 09:50 每天 发送 药物副反应检测';
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

    // 模板方法的实现, 重载
    public function doworkImp () {
        echo "\n 推送[begin]";

        $unitofwork = BeanFinder::get("UnitOfWork");

        // 推送
        $fromtime = date('Y-m-d 00:00:00', time() + 3600 * 24); // 明天
        $totime = date('Y-m-d 00:00:00', time() + 3600 * 24 * 2); // 后天

        echo "  {$fromtime} ~ {$totime}";
        $optasks = OpTaskDao::getListByUnicodeStatus('remind:PmSideEffect', 0, $fromtime, $totime);

        $count = 0;
        $logcontent = '';
        foreach ($optasks as $optask) {
            echo "\n optask[{$optask->id}] ";
            $result = PmSideEffectService::sendOpTaskPmRemid($optask);
            if ($result) {
                echo ' [suc] ';
                $count ++;
                $logcontent .= $optask->id . " ";
            } else {
                echo ' [fai] ';
            }
        }
        $this->cronlog_brief = $count;
        $this->cronlog_content = $logcontent;
        $unitofwork->commitAndInit();
        echo "\n 推送[end]";

        // 过期任务置顶
        echo "\n 过期任务置顶[begin]";
        $fromtime = date('Y-m-d 00:00:00', time() - 3600 * 24 * 2);
        $totime = date('Y-m-d 00:00:00', time() - 3600 * 24);

        echo "  {$fromtime} ~ {$totime}";
        $unitofwork = BeanFinder::get("UnitOfWork");

        $optasks = OpTaskDao::getListByUnicodeStatus('remind:PmSideEffect', 0, $fromtime, $totime);

        foreach ($optasks as $optask) {
            echo "\n optask[{$optask->id}]->level = 5";
            $optask->level = 5;
            echo " [suc]";
        }
        $unitofwork->commitAndInit();
        echo "\n 过期任务置顶[end]";

        // 提交提醒
        echo "\n 提交提醒[begin]";
        $fromtime = date('Y-m-d 00:00:00', time() - 3600 * 24 * 5);
        $totime = date('Y-m-d 00:00:00', time() - 3600 * 24 * 4);

        echo "  {$fromtime} ~ {$totime}";
        $unitofwork = BeanFinder::get("UnitOfWork");

        $optasks = OpTaskDao::getListByUnicodeStatus('remind:PmSideEffect', 0, $fromtime, $totime);

        foreach ($optasks as $optask) {
            echo "\n optask[{$optask->id}] ";

            $patient = $optask->patient;
            if (false == $patient instanceof Patient) {
                echo " [没有推送成，patientid={$optask->patientid}] ";
                continue;
            }

            $pcard = $patient->getMasterPcard();

            PushMsgService::sendTxtMsgToWxUsersOfPcardBySystem($pcard, "{$patient->name}您好，您的检查做了吗？检查结果有无异常");
            echo " [suc] ";
        }
        $unitofwork->commitAndInit();
        echo "\n 提交提醒[end]";
    }
}

// //////////////////////////////////////////////////////

$process = new Optask_PmSideEffect_Remind(__FILE__);
$process->dowork();
