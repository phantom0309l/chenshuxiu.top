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

class PurchaseArriveAdd
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $data = $this->getData();
        foreach ($data as $str) {
            if(empty($str)){
                continue;
            }
            $arr = explode(",", $str);
            $sku_code = $arr[0];
            $end_date = $arr[1];
            $qty = $arr[2];
            $aa = GuanYiService::purchaseArriveAdd($sku_code, $qty, $end_date);
            if($aa == false){
                echo "\n[fail--1][{$sku_code}]\n";
            }else{
                if($aa["success"] == true){
                    echo "\n[ok][{$sku_code}]\n";
                }else{
                    echo "\n[fail--2][{$sku_code}]\n";
                }
            }
        }
        $unitofwork->commitAndInit();
    }
    private function getData(){
        $str = file_get_contents("purchase.csv");
        $d = explode("\n", $str);
        return $d;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][PurchaseArriveAdd.php]=====");

$process = new PurchaseArriveAdd();
$process->dowork();

Debug::trace("=====[cron][end][PurchaseArriveAdd.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
