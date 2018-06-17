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

class Fix_shopproduct_is_water
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $arr = [305494016,287697596,543005116,543580646,282709756,287695876,305404166,
                287696276,315866086,315873746,287700186,467658196,551891336,
                554677746,507203976,554606816,504023516,507253516,
                546491746,551920346,317464456,317621766,317633866];

        foreach($arr as $id){
            $shopProduct = ShopProduct::getById($id);
            $shopProduct->is_water = 1;
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_shopproduct_is_water.php]=====");

$process = new Fix_shopproduct_is_water();
$process->dowork();

Debug::trace("=====[cron][end][Fix_shopproduct_is_water.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
