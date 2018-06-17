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

class Init_shoporder_pos
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $now = date("Y-m-d H:i:s", time());
        $sql = "select patientid from shoporders where is_pay=1 and patientid > 0 group by patientid";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $cond = " and patientid = :patientid and is_pay = 1 order by time_pay asc, id asc";
            $bind = [];
            $bind[':patientid'] = $id;
            $shopOrders = Dao::getEntityListByCond('ShopOrder', $cond, $bind);
            foreach($shopOrders as $index => $a){
                $pos = $a->pos;
                $current_pos = $index + 1;
                if($pos != $current_pos){
                    echo "\n====shoporderid[{$a->id}]===\n";
                }
                $a->pos = $current_pos;
            }

            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_shoporder_pos.php]=====");

$process = new Init_shoporder_pos();
$process->dowork();

Debug::trace("=====[cron][end][Init_shoporder_pos.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
