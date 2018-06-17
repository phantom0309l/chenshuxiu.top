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

// Debug::$debug = 'Dev';

class Lilly_optask_watch extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'hourly';
        $row["title"] = '按时, 汇总礼来相关的任务，提醒超过1.5小时未关闭的任务';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $optasktpl_patientmsg = OpTaskTplDao::getOneByUnicode('PatientMsg:message');

        $send_data = array();
        $sql = "select count(id) from optasks where level=5 and diseaseid=1 and status=0 and plantime < :fromtime";
        $sql1 = "select count(id) from optasks where level=5 and diseaseid=1 and status=0 and optasktplid = {$optasktpl_patientmsg->id} and plantime < :fromtime";

        $time = time() - 5400;
        $time = date("Y-m-d H:i:s", $time);
        $bind = [];
        $bind[':fromtime'] = $time;

        //总任务数
        $cnt = Dao::queryValue($sql, $bind);

        //消息任务数
        $cnt1 = Dao::queryValue($sql1, $bind);

        $content = "礼来运营任务未关闭情况：\n1.5h以上未关闭总任务数：{$cnt}\n1.5h以上未关闭消息任务数：{$cnt1}";

        PushMsgService::sendMsgToAuditorBySystem("Sunflower", 1, $content);

    }
}

// //////////////////////////////////////////////////////

$process = new Lilly_optask_watch(__FILE__);
$process->dowork();
