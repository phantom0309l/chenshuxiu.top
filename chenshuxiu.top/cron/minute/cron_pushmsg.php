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
class cron_pushmsg extends CronBase
{

    private $allcnt = 0;

    private static $patientids_test = array();

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = '每5钟, pushmsg 消息补漏';
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
        self::$patientids_test = PatientDao::getIdsOfCompany();

        $allcnt = 0;

        $j = 0;
        for ($i = 0; $i < 20; $i ++) {
            $allcnt += $cnt = $this->pushList();

            if ($cnt > 0) {
                $j ++;
            } else {
                echo ".";
                sleep(1);
            }

            if ($j > 10) {
                break;
            }
        }

        $this->cronlog_brief = "cnt={$allcnt}";

        return $this->allcnt = $allcnt;
    }

    public function pushList () {

        // $userid_cond = " and userid in (97,10007,10022) ";
        $userid_cond = '';

        // 截止到2分钟前
        $to_time = date('Y-m-d H:i:s', time() - 120);

        // 加锁
        $sql = " update pushmsgs set send_status=1 where send_status=0 and wxuserid > 0 and createtime < '{$to_time}' {$userid_cond} limit 100 ";
        Dao::executeNoQuery($sql);

        $sql = " select id from pushmsgs where send_status=1 and wxuserid > 0 ";
        $ids = Dao::queryValues($sql, []);

        // todo : 代码重复是许喆造成的
        if ('fangcunyisheng.com' == Config::getConfig('key_prefix')) {
            foreach ($ids as $id) {
                $unitofwork = BeanFinder::get("UnitOfWork");
                $this->pushOneOnline($id);
                $unitofwork->commitAndInit();
            }
        } else {
            foreach ($ids as $id) {
                $unitofwork = BeanFinder::get("UnitOfWork");
                $this->pushOne($id);
                $unitofwork->commitAndInit();
            }
        }

        return count($ids);
    }

    public function pushOneOnline ($id) {
        echo "\n{$id} ";
        $pushmsg = PushMsg::getById($id);
        if ($pushmsg instanceof PushMsg) {
            // 再次检查发送状态
            if ($pushmsg->send_status == 1) {
                $pushmsg->sendByCron();
                $this->cronlog_content .= "{$pushmsg->id}\n";
            }
        }
    }

    public function pushOne ($id) {
        echo "\n{$id} ";
        $pushmsg = PushMsg::getById($id);
        if (in_array($pushmsg->patientid, self::$patientids_test)) {
            $pushmsg->sendByCron();
            $pushmsg->send_status = 2;
            return;
        }
        $pushmsg->send_status = 2;
        echo "[ignore] \n";
    }
}

// //////////////////////////////////////////////////////

$process = new cron_pushmsg(__FILE__);
$cnt = $process->dowork();
