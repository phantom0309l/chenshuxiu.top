<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/12/4
 * Time: 15:57
 */
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
class cron_plan_txtmsg extends CronBase
{

    private $allcnt = 0;

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = '每2分钟, plan_txtmsg 定时消息发送';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return $this->allcnt > 0;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $allcnt = 0;

        $allcnt += $this->pushList();

        $this->cronlog_brief = "cnt={$allcnt}";

        return $this->allcnt = $allcnt;
    }

    public function pushList () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $brief = 0;

        $to_time = date('Y-m-d H:i:s');

        $sql = "SELECT * 
                FROM plan_txtmsgs 
                WHERE type = 1 
                AND pushmsgid = 0
                AND plan_send_time <= :to_time";
        $bind = [
            ":to_time" => $to_time
        ];
        $plantxtmsgs = Dao::loadEntityList("Plan_txtMsg", $sql, $bind);

        foreach ($plantxtmsgs as $plantxtmsg) {
            $plantxtmsg->send($plantxtmsg->auditor);

            $brief++;

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;

        $unitofwork->commitAndInit();

        return count($plantxtmsgs);
    }
}

// //////////////////////////////////////////////////////

$process = new cron_plan_txtmsg(__FILE__);
$process->dowork();
