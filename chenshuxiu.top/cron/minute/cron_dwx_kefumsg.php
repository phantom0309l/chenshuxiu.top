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
class cron_dwx_kefumsg extends CronBase
{

    private $allcnt = 0;

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = '每5钟, dwx_kefumsg 消息补漏';
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
        $sql = " update dwx_kefumsgs set send_status=1 where send_status=0 and wxuserid > 0 and createtime < '{$to_time}' {$userid_cond} limit 100 ";
        Dao::executeNoQuery($sql);

        $sql = " select id from dwx_kefumsgs where send_status=1 and wxuserid > 0 ";
        $ids = Dao::queryValues($sql, []);

        if ('fangcunyisheng.com' == Config::getConfig('key_prefix')) {
            foreach ($ids as $id) {
                $unitofwork = BeanFinder::get("UnitOfWork");
                $this->pushOneOnline($id);
                $unitofwork->commitAndInit();
            }
        } else {
            // 测试环境
        }

        return count($ids);
    }

    public function pushOneOnline ($id) {
        echo "\n{$id} ";
        $dwx_kefumsg = Dwx_kefumsg::getById($id);
        if ($dwx_kefumsg instanceof Dwx_kefumsg) {
            $dwx_kefumsg->sendByCron();

            $this->cronlog_content .= "{$dwx_kefumsg->id}\n";
        }
    }
}

// //////////////////////////////////////////////////////

$process = new cron_dwx_kefumsg(__FILE__);
$process->dowork();
