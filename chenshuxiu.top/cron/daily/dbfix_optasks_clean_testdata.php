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

class Dbfix_optasks_clean_testdata extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 07:10 数据清理 optasks, 清理测试数据';
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
        $unitofwork = BeanFinder::get("UnitOfWork");

        $time = date("Y-m-d H:i:s");
        echo "\n ===== {$time} begin =====\n";

        $sql = 'select id
            from optasks a
            where userid > 10000 and userid < 20000 and userid <> 10001 and userid <> 10013;';

        $ids = Dao::queryValues($sql);

        $cnt = count($ids);

        echo "\ncnt=$cnt\n";

        foreach ($ids as $id) {

            $randno = XObjLog::getTablenoByObjtypeObjid('OpTask', $id);
            $sql = "delete from xobjlogs{$randno} where objtype='OpTask' and objid={$id}";
            Dao::executeNoQuery($sql, [], 'xworkdb');

            $randno = date('Ym');
            $sql = "delete from xobjlogs{$randno} where objtype='OpTask' and objid={$id}";
            Dao::executeNoQuery($sql, [], 'xworkdb');

            $sql = "delete from optasks where id={$id}";
            Dao::executeNoQuery($sql);

            $this->cronlog_content .= "{$id}\n";
            echo ".";
        }

        $this->cronlog_brief = $cnt;

        $unitofwork->commitAndInit();

        $time = date("Y-m-d H:i:s");
        echo "\n ===== {$time} end =====\n";
    }
}

$process = new Dbfix_optasks_clean_testdata(__FILE__);
$process->dowork();
