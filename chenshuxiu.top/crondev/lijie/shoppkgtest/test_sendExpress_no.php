<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 18-05-30
 * Time: 上午11:44
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$shopPkgId = $argv[1];

class Test_sendExpress_no
{

    public function dowork($shopPkgId) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $shopPkg = ShopPkg::getById($shopPkgId);

        if ($shopPkg instanceof ShopPkg) {
            ExpressService::sendExpress_no($shopPkg);
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Test_sendExpress_no.php]=====");

$process = new Test_sendExpress_no();
$process->dowork($shopPkgId);

Debug::trace("=====[cron][end][Test_sendExpress_no.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
