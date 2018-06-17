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

class Del_doctorshopproductref
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from doctorshopproductrefs where shopproductid=467769366";
        $ids = Dao::queryValues($sql);
        foreach ($ids as $id) {
            $doctorShopProductRef = DoctorShopProductRef::getById($id);
            if ($doctorShopProductRef instanceof DoctorShopProductRef) {
                echo "\n====[$i][{$id}]===\n";
                $doctorShopProductRef->remove();
            }
            if($i%50 == 0){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Del_doctorshopproductref.php]=====");

$process = new Del_doctorshopproductref();
$process->dowork();

Debug::trace("=====[cron][end][Del_doctorshopproductref.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
