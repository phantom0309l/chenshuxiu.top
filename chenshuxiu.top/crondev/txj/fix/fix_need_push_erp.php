<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Fix_need_push_erp
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from shoppkgs
                    where need_push_erp = 0 and is_push_erp = 0
                    and is_goodsout = 0 and is_sendout = 0
                    and status = 1
                    and (userid < 10000 or userid > 20000) and time_pay > '2018-04-04' and id in (661558136)";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $i++;
            echo "[{$i}][{$id}]\n";
            $shopPkg = ShopPkg::getById($id);
//            $shopPkg->need_push_erpSet();
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][fix_need_push_erp.php]=====");

$process = new Fix_need_push_erp();
$process->dowork();

Debug::trace("=====[cron][end][fix_need_push_erp.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
