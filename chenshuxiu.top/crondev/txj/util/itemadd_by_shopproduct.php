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

class Itemadd_by_shopproduct
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from shopproducts where sku_code !='' and id in (554739406)";

        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            $shopProduct = ShopProduct::getById($id);
            if( $shopProduct instanceof ShopProduct ){
                $aa = GuanYiService::itemAddByShopProduct($shopProduct);
                $i++;
                if($aa["success"] == true){
                    echo "\n[{$i}][ok][{$id}]\n";
                }else{
                    echo "\n[{$i}][fail][{$id}]\n";
                    $result = json_encode($aa, JSON_UNESCAPED_UNICODE);
                    echo "\n==result[{$result}]===\n";
                }
            }
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Itemadd_by_shopproduct.php]=====");

$process = new Itemadd_by_shopproduct();
$process->dowork();

Debug::trace("=====[cron][end][Itemadd_by_shopproduct.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
