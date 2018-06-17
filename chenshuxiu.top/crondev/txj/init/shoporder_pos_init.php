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

class Shoporder_pos_init
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select patientid from shoporders group by patientid";
        $ids = Dao::queryValues($sql);
        $i = 0;

        foreach($ids as $id){
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $patient = Patient::getById($id);

            $cond = " and patientid = :patientid order by id asc";
            $bind = [];
            $bind[':patientid'] = $patient->id;
            $shopOrders = Dao::getEntityListByCond('ShopOrder', $cond, $bind);
            foreach($shopOrders as $j => $a){
                $a->pos = $j + 1;
            }

        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Shoporder_pos_init.php]=====");

$process = new Shoporder_pos_init();
$process->dowork();

Debug::trace("=====[cron][end][Shoporder_pos_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
