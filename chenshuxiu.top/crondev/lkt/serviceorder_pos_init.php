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

class ServiceOrder_pos_init
{

    public function dowork() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT patientid FROM serviceorders GROUP BY patientid";
        $ids = Dao::queryValues($sql);
        echo "\n";
        echo count($ids);
        echo "\n";
        $i = 0;
        foreach ($ids as $id) {
            $cond = " AND patientid = :patientid AND is_pay = 1 AND serviceproduct_type = 'quickpass' ORDER BY time_pay ASC";
            $bind = [];
            $bind[':patientid'] = $id;
            $serviceOrders = Dao::getEntityListByCond('ServiceOrder', $cond, $bind);
            echo "\n";
            echo count($serviceOrders);
            echo "\n";
            foreach ($serviceOrders as $j => $a) {
                $a->pos = $j + 1;
                $i++;

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][serviceorder_pos_init.php]=====");

$process = new ServiceOrder_pos_init();
$process->dowork();

Debug::trace("=====[cron][end][serviceorder_pos_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
