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
Debug::$debug_mergexworklog = false;

class LinkmanFetch extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 02:10, 抓取手机号对应的省市';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        echo "\n\n----- [fetchInfoByMobile][begin] -----" . XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT id FROM linkmans WHERE mobile != '' AND xprovinceid=0 AND xcityid=0 AND fetch_cnt<3";

        $ids = Dao::queryValues($sql);

        foreach ($ids as $i => $id) {
            if ($i % 100 == 0) {
                echo "\n" . date('Y-m-d H:i:s') . " {$i} ";
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $linkman = Linkman::getById($id);
            LinkmanService::updateXprovinceidAndXcityid($linkman);
            $linkman->fetch_cnt ++;
        }

        $unitofwork->commitAndInit();
        echo "\n\n----- [fetchInfoByMobile][end] -----" . XDateTime::now();
    }
}

// //////////////////////////////////////////////////////

$process = new LinkmanFetch(__FILE__);
$process->dowork();
